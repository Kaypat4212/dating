#!/bin/bash
#==============================================================================
# Reverb Production Deployment Script
# Sets up Laravel Reverb WebSocket server for production environments
#==============================================================================

set -e  # Exit on error

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/html/dating"
WEB_USER="www-data"
SUPERVISOR_CONF_DIR="/etc/supervisor/conf.d"
SYSTEMD_DIR="/etc/systemd/system"
WEB_SERVER="nginx"  # Change to 'apache' if using Apache

#==============================================================================
# Helper Functions
#==============================================================================

print_header() {
    echo -e "${BLUE}╔════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║  Reverb Production Deployment                 ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════╝${NC}"
    echo ""
}

print_step() {
    echo -e "${GREEN}▶ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_error "This script must be run as root (use sudo)"
        exit 1
    fi
}

#==============================================================================
# Prerequisites Check
#==============================================================================

check_prerequisites() {
    print_step "Checking prerequisites..."
    
    # Check if project directory exists
    if [ ! -d "$PROJECT_DIR" ]; then
        print_error "Project directory not found: $PROJECT_DIR"
        exit 1
    fi
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed"
        exit 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
    print_success "PHP $PHP_VERSION found"
    
    # Check Composer
    if ! command -v composer &> /dev/null; then
        print_warning "Composer not found in PATH - trying global installation"
        if [ ! -f /usr/local/bin/composer ]; then
            print_error "Composer is not installed"
            exit 1
        fi
    fi
    print_success "Composer found"
    
    # Check .env file
    if [ ! -f "$PROJECT_DIR/.env" ]; then
        print_error ".env file not found"
        exit 1
    fi
    print_success ".env file found"
}

#==============================================================================
# Process Manager Selection
#==============================================================================

select_process_manager() {
    print_step "Select process manager:"
    echo "1) Supervisor (recommended)"
    echo "2) Systemd"
    echo "3) Skip (manual setup)"
    read -p "Enter choice [1-3]: " PM_CHOICE
    
    case $PM_CHOICE in
        1)
            PROCESS_MANAGER="supervisor"
            ;;
        2)
            PROCESS_MANAGER="systemd"
            ;;
        3)
            PROCESS_MANAGER="none"
            print_warning "Skipping process manager setup"
            return
            ;;
        *)
            print_error "Invalid choice"
            exit 1
            ;;
    esac
}

#==============================================================================
# Install Dependencies
#==============================================================================

install_dependencies() {
    print_step "Installing dependencies..."
    
    cd "$PROJECT_DIR"
    
    # Install PHP dependencies
    sudo -u $WEB_USER composer install --no-dev --optimize-autoloader --no-interaction
    
    print_success "Dependencies installed"
}

#==============================================================================
# Configure Reverb
#==============================================================================

configure_reverb() {
    print_step "Configuring Reverb..."
    
    cd "$PROJECT_DIR"
    
    # Check if Reverb credentials exist
    REVERB_APP_KEY=$(grep REVERB_APP_KEY .env | cut -d '=' -f2)
    
    if [ -z "$REVERB_APP_KEY" ]; then
        print_warning "Reverb credentials not found in .env"
        read -p "Generate new credentials? (y/n): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            # Generate credentials
            REVERB_APP_ID=$(openssl rand -hex 16)
            REVERB_APP_KEY=$(openssl rand -hex 32)
            REVERB_APP_SECRET=$(openssl rand -hex 32)
            
            # Append to .env
            echo "" >> .env
            echo "# Reverb Configuration (auto-generated)" >> .env
            echo "REVERB_APP_ID=$REVERB_APP_ID" >> .env
            echo "REVERB_APP_KEY=$REVERB_APP_KEY" >> .env
            echo "REVERB_APP_SECRET=$REVERB_APP_SECRET" >> .env
            
            print_success "Reverb credentials generated"
        fi
    else
        print_success "Reverb credentials already configured"
    fi
    
    # Verify other settings
    print_step "Verifying .env configuration..."
    
    REVERB_HOST=$(grep REVERB_HOST .env | cut -d '=' -f2 | tr -d '"' | tr -d "'")
    if [ "$REVERB_HOST" = "localhost" ] || [ "$REVERB_HOST" = "127.0.0.1" ]; then
        print_error "REVERB_HOST is set to localhost - this won't work in production!"
        echo "Please update REVERB_HOST in .env to your public domain"
        exit 1
    fi
    
    print_success "Configuration verified"
}

#==============================================================================
# Setup Supervisor
#==============================================================================

setup_supervisor() {
    if [ "$PROCESS_MANAGER" != "supervisor" ]; then
        return
    fi
    
    print_step "Setting up Supervisor..."
    
    # Install Supervisor if not present
    if ! command -v supervisorctl &> /dev/null; then
        print_step "Installing Supervisor..."
        if command -v apt-get &> /dev/null; then
            apt-get update
            apt-get install -y supervisor
        elif command -v yum &> /dev/null; then
            yum install -y supervisor
            systemctl enable supervisord
            systemctl start supervisord
        else
            print_error "Could not install Supervisor - please install manually"
            exit 1
        fi
    fi
    
    # Create Supervisor config
    cat > "$SUPERVISOR_CONF_DIR/reverb.conf" <<EOF
[program:reverb]
process_name=%(program_name)s
command=php $PROJECT_DIR/artisan reverb:start
directory=$PROJECT_DIR
user=$WEB_USER
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
redirect_stderr=true
stdout_logfile=$PROJECT_DIR/storage/logs/reverb.log
stdout_logfile_maxbytes=50MB
stdout_logfile_backups=10
startsecs=5
startretries=10
stopwaitsecs=30
EOF
    
    # Reload and start
    supervisorctl reread
    supervisorctl update
    supervisorctl start reverb
    
    print_success "Supervisor configured and Reverb started"
}

#==============================================================================
# Setup Systemd
#==============================================================================

setup_systemd() {
    if [ "$PROCESS_MANAGER" != "systemd" ]; then
        return
    fi
    
    print_step "Setting up Systemd service..."
    
    # Create systemd service file
    cat > "$SYSTEMD_DIR/reverb.service" <<EOF
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target mysql.service redis.service

[Service]
Type=simple
User=$WEB_USER
Group=$WEB_USER
Restart=always
RestartSec=5
ExecStart=/usr/bin/php $PROJECT_DIR/artisan reverb:start
WorkingDirectory=$PROJECT_DIR
StandardOutput=append:$PROJECT_DIR/storage/logs/reverb.log
StandardError=append:$PROJECT_DIR/storage/logs/reverb-error.log
SyslogIdentifier=reverb

[Install]
WantedBy=multi-user.target
EOF
    
    # Enable and start service
    systemctl daemon-reload
    systemctl enable reverb
    systemctl start reverb
    
    print_success "Systemd service configured and Reverb started"
}

#==============================================================================
# Setup Web Server Proxy
#==============================================================================

setup_web_server() {
    print_step "Web server proxy configuration..."
    
    print_warning "Web server proxy must be configured manually"
    echo ""
    echo "Configuration files are available in:"
    echo "  - $PROJECT_DIR/deployment/nginx/reverb-site.conf"
    echo "  - $PROJECT_DIR/deployment/apache/heartsconnect-ssl.conf"
    echo ""
    echo "Please configure your web server according to the documentation."
    echo ""
    read -p "Press Enter to continue..."
}

#==============================================================================
# Clear Caches
#==============================================================================

clear_caches() {
    print_step "Clearing Laravel caches..."
    
    cd "$PROJECT_DIR"
    sudo -u $WEB_USER php artisan config:clear
    sudo -u $WEB_USER php artisan cache:clear
    sudo -u $WEB_USER php artisan view:clear
    
    print_success "Caches cleared"
}

#==============================================================================
# Fix Permissions
#==============================================================================

fix_permissions() {
    print_step "Setting correct permissions..."
    
    # Storage and cache directories
    chown -R $WEB_USER:$WEB_USER "$PROJECT_DIR/storage"
    chown -R $WEB_USER:$WEB_USER "$PROJECT_DIR/bootstrap/cache"
    
    chmod -R 775 "$PROJECT_DIR/storage"
    chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
    
    print_success "Permissions set"
}

#==============================================================================
# Verify Installation
#==============================================================================

verify_installation() {
    print_step "Verifying installation..."
    
    # Check if Reverb is running
    if [ "$PROCESS_MANAGER" = "supervisor" ]; then
        if supervisorctl status reverb | grep -q "RUNNING"; then
            print_success "Reverb is running (Supervisor)"
        else
            print_error "Reverb is not running"
            supervisorctl status reverb
            exit 1
        fi
    elif [ "$PROCESS_MANAGER" = "systemd" ]; then
        if systemctl is-active --quiet reverb; then
            print_success "Reverb is running (Systemd)"
        else
            print_error "Reverb is not running"
            systemctl status reverb
            exit 1
        fi
    fi
    
    # Check if port is listening
    if netstat -tlnp 2>/dev/null | grep -q ":8080"; then
        print_success "Port 8080 is listening"
    else
        print_warning "Port 8080 is not listening - check logs"
    fi
}

#==============================================================================
# Print Summary
#==============================================================================

print_summary() {
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  Reverb Deployment Complete!                  ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Next steps:"
    echo ""
    echo "1. Configure your web server (Nginx/Apache) to proxy WebSocket connections"
    echo "   See: $PROJECT_DIR/deployment/nginx/reverb-site.conf"
    echo "   Or:  $PROJECT_DIR/deployment/apache/heartsconnect-ssl.conf"
    echo ""
    echo "2. Ensure SSL certificate is installed (required for production)"
    echo ""
    echo "3. Test WebSocket connection from browser console"
    echo ""
    echo "Useful commands:"
    if [ "$PROCESS_MANAGER" = "supervisor" ]; then
        echo "  - Start:   sudo supervisorctl start reverb"
        echo "  - Stop:    sudo supervisorctl stop reverb"
        echo "  - Restart: sudo supervisorctl restart reverb"
        echo "  - Status:  sudo supervisorctl status reverb"
        echo "  - Logs:    sudo supervisorctl tail -f reverb"
    elif [ "$PROCESS_MANAGER" = "systemd" ]; then
        echo "  - Start:   sudo systemctl start reverb"
        echo "  - Stop:    sudo systemctl stop reverb"
        echo "  - Restart: sudo systemctl restart reverb"
        echo "  - Status:  sudo systemctl status reverb"
        echo "  - Logs:    sudo journalctl -u reverb -f"
    fi
    echo "  - Manual:  cd $PROJECT_DIR && php artisan reverb:start --debug"
    echo ""
}

#==============================================================================
# Main
#==============================================================================

main() {
    print_header
    check_root
    check_prerequisites
    select_process_manager
    install_dependencies
    configure_reverb
    clear_caches
    fix_permissions
    setup_supervisor
    setup_systemd
    setup_web_server
    verify_installation
    print_summary
}

# Run main function
main
