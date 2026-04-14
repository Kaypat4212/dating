#!/bin/bash

# ============================================================
# Fix Laravel Echo to use Reverb instead of Pusher
# ============================================================

echo "╔════════════════════════════════════════════════╗"
echo "║  Switching from Pusher to Reverb              ║"
echo "╚════════════════════════════════════════════════╝"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ .env file not found!"
    exit 1
fi

echo "🔧 Updating .env file..."

# Backup .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Backup created"

# Update BROADCAST_CONNECTION from pusher to reverb
sed -i 's/^BROADCAST_CONNECTION=pusher/BROADCAST_CONNECTION=reverb/' .env
echo "✅ Set BROADCAST_CONNECTION=reverb"

# Add VITE_BROADCAST_DRIVER if not present
if ! grep -q "^VITE_BROADCAST_DRIVER=" .env; then
    # Find the line with VITE_REVERB_SCHEME and add after it
    sed -i '/^VITE_REVERB_SCHEME=/a VITE_BROADCAST_DRIVER=reverb' .env
    echo "✅ Added VITE_BROADCAST_DRIVER=reverb"
else
    sed -i 's/^VITE_BROADCAST_DRIVER=.*/VITE_BROADCAST_DRIVER=reverb/' .env
    echo "✅ Updated VITE_BROADCAST_DRIVER=reverb"
fi

# Fix VITE_REVERB_SCHEME (should be https, not wss)
sed -i 's/^VITE_REVERB_SCHEME=wss/VITE_REVERB_SCHEME=https/' .env
echo "✅ Fixed VITE_REVERB_SCHEME=https"

# Update REVERB_HOST to match VITE_REVERB_HOST
sed -i 's/^REVERB_HOST=heartsconnect\.site/REVERB_HOST=heartsconnect.cc/' .env
echo "✅ Updated REVERB_HOST=heartsconnect.cc"

echo ""
echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "🔨 Rebuilding frontend assets..."
npm run build

echo ""
echo "╔════════════════════════════════════════════════╗"
echo "║  ✅ Configuration Updated!                     ║"
echo "╚════════════════════════════════════════════════╝"
echo ""
echo "Changes made:"
echo "  • BROADCAST_CONNECTION=reverb"
echo "  • VITE_BROADCAST_DRIVER=reverb"
echo "  • VITE_REVERB_SCHEME=https"
echo "  • REVERB_HOST=heartsconnect.cc"
echo ""
echo "Next steps:"
echo "  1. Wait for npm run build to finish"
echo "  2. Restart Reverb: bash stop-reverb.sh && bash start-reverb.sh"
echo "  3. Refresh your browser"
echo "  4. Check console - should see '🟢 Laravel Echo initialized with Reverb'"
echo ""
