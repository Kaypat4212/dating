#!/bin/bash
#==============================================================================
# Reverb Server Startup Script for cPanel
# Starts Laravel Reverb WebSocket server in background with nohup
#==============================================================================

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}╔════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  Hearts Connect - Reverb Server Startup      ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════╝${NC}"
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP not found. Please ensure PHP is in your PATH.${NC}"
    exit 1
fi

# Check if artisan exists
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ artisan file not found. Are you in the project root?${NC}"
    exit 1
fi

# Check if Reverb is already running
echo -e "${YELLOW}🔍 Checking for existing Reverb processes...${NC}"
if pgrep -f "reverb:start" > /dev/null; then
    echo -e "${YELLOW}⚠️  Reverb is already running!${NC}"
    echo ""
    echo "Running processes:"
    ps aux | grep "reverb:start" | grep -v grep
    echo ""
    read -p "Do you want to restart the server? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}🛑 Stopping existing Reverb processes...${NC}"
        pkill -f "reverb:start"
        sleep 2
    else
        echo -e "${GREEN}✅ Keeping existing server running.${NC}"
        exit 0
    fi
fi

# Load environment variables
if [ -f ".env" ]; then
    export $(grep -v '^#' .env | xargs)
    echo -e "${GREEN}✅ Environment loaded from .env${NC}"
else
    echo -e "${YELLOW}⚠️  .env file not found. Using defaults.${NC}"
fi

# Get configuration from .env or use defaults
REVERB_HOST="${REVERB_HOST:-localhost}"
REVERB_PORT="${REVERB_PORT:-8080}"
LOG_FILE="storage/logs/reverb.log"

# Create log directory if it doesn't exist
mkdir -p storage/logs

echo ""
echo -e "${GREEN}📋 Configuration:${NC}"
echo -e "   Host: ${YELLOW}${REVERB_HOST}${NC}"
echo -e "   Port: ${YELLOW}${REVERB_PORT}${NC}"
echo -e "   Log:  ${YELLOW}${LOG_FILE}${NC}"
echo ""

# Start Reverb with nohup
echo -e "${GREEN}🚀 Starting Reverb server...${NC}"
nohup php artisan reverb:start --host="${REVERB_HOST}" --port="${REVERB_PORT}" > "${LOG_FILE}" 2>&1 &
REVERB_PID=$!

# Wait a moment for the process to start
sleep 3

# Check if the process is still running
if ps -p $REVERB_PID > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Reverb server started successfully!${NC}"
    echo ""
    echo -e "${GREEN}════════════════════════════════════════════════${NC}"
    echo -e "   PID: ${YELLOW}${REVERB_PID}${NC}"
    echo -e "   WebSocket: ${YELLOW}ws://${REVERB_HOST}:${REVERB_PORT}/app/${REVERB_APP_ID}${NC}"
    echo -e "   Log file: ${YELLOW}${LOG_FILE}${NC}"
    echo -e "${GREEN}════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${YELLOW}📖 Useful commands:${NC}"
    echo -e "   • View logs:        ${GREEN}tail -f ${LOG_FILE}${NC}"
    echo -e "   • Check status:     ${GREEN}ps aux | grep reverb${NC}"
    echo -e "   • Stop server:      ${GREEN}pkill -f 'reverb:start'${NC}"
    echo -e "   • View this PID:    ${GREEN}ps -p ${REVERB_PID}${NC}"
    echo ""
    
    # Save PID to file for later reference
    echo "$REVERB_PID" > storage/logs/reverb.pid
    echo -e "${GREEN}💾 PID saved to storage/logs/reverb.pid${NC}"
else
    echo -e "${RED}❌ Failed to start Reverb server!${NC}"
    echo -e "${YELLOW}Check the log file for errors: ${LOG_FILE}${NC}"
    exit 1
fi
