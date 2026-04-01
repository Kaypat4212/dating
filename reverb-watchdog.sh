#!/bin/bash
#==============================================================================
# Reverb Watchdog Script - Auto-restart if crashed
# Add to crontab: */5 * * * * /path/to/reverb-watchdog.sh >> /path/to/watchdog.log 2>&1
#==============================================================================

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Load environment variables
if [ -f ".env" ]; then
    export $(grep -v '^#' .env | xargs)
fi

REVERB_HOST="${REVERB_HOST:-localhost}"
REVERB_PORT="${REVERB_PORT:-8080}"
LOG_FILE="storage/logs/reverb.log"
WATCHDOG_LOG="storage/logs/reverb-watchdog.log"

# Create log directory if it doesn't exist
mkdir -p storage/logs

# Log function
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$WATCHDOG_LOG"
}

# Check if Reverb is running
if pgrep -f "reverb:start" > /dev/null; then
    # Server is running, check if port is actually listening
    if command -v netstat &> /dev/null; then
        if netstat -tuln | grep ":${REVERB_PORT}" > /dev/null; then
            # Everything is good, exit silently
            exit 0
        else
            log "⚠️  Reverb process exists but port ${REVERB_PORT} is not listening. Restarting..."
            pkill -f "reverb:start"
            sleep 2
        fi
    else
        # netstat not available, just check process
        exit 0
    fi
else
    log "❌ Reverb server is not running. Starting..."
fi

# Start Reverb
log "🚀 Starting Reverb server on ${REVERB_HOST}:${REVERB_PORT}"
nohup php artisan reverb:start --host="${REVERB_HOST}" --port="${REVERB_PORT}" >> "${LOG_FILE}" 2>&1 &
REVERB_PID=$!

# Wait and verify
sleep 3

if ps -p $REVERB_PID > /dev/null 2>&1; then
    log "✅ Reverb server started successfully (PID: ${REVERB_PID})"
    echo "$REVERB_PID" > storage/logs/reverb.pid
else
    log "❌ Failed to start Reverb server. Check ${LOG_FILE} for errors."
    exit 1
fi
