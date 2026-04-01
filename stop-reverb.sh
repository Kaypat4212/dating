#!/bin/bash
#==============================================================================
# Reverb Server Stop Script for cPanel
# Gracefully stops Laravel Reverb WebSocket server
#==============================================================================

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}╔════════════════════════════════════════════════╗${NC}"
echo -e "${YELLOW}║  Hearts Connect - Reverb Server Shutdown     ║${NC}"
echo -e "${YELLOW}╚════════════════════════════════════════════════╝${NC}"
echo ""

# Check if Reverb is running
if ! pgrep -f "reverb:start" > /dev/null; then
    echo -e "${YELLOW}ℹ️  Reverb server is not running.${NC}"
    exit 0
fi

echo -e "${YELLOW}🔍 Found running Reverb processes:${NC}"
ps aux | grep "reverb:start" | grep -v grep
echo ""

# Kill the processes
echo -e "${YELLOW}🛑 Stopping Reverb server...${NC}"
pkill -f "reverb:start"

# Wait for processes to terminate
sleep 2

# Verify they're stopped
if pgrep -f "reverb:start" > /dev/null; then
    echo -e "${RED}⚠️  Processes still running. Forcing termination...${NC}"
    pkill -9 -f "reverb:start"
    sleep 1
fi

# Final check
if ! pgrep -f "reverb:start" > /dev/null; then
    echo -e "${GREEN}✅ Reverb server stopped successfully!${NC}"
    
    # Remove PID file if it exists
    if [ -f "storage/logs/reverb.pid" ]; then
        rm storage/logs/reverb.pid
        echo -e "${GREEN}💾 PID file removed.${NC}"
    fi
else
    echo -e "${RED}❌ Failed to stop Reverb server!${NC}"
    echo -e "${YELLOW}Try manually: kill -9 \$(pgrep -f 'reverb:start')${NC}"
    exit 1
fi
