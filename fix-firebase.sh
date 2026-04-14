#!/bin/bash

# ═══════════════════════════════════════════════════════════════════════════
# Firebase Configuration Fix
# ═══════════════════════════════════════════════════════════════════════════

echo "╔════════════════════════════════════════════════╗"
echo "║  Firebase Push Notifications Setup            ║"
echo "╚════════════════════════════════════════════════╝"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ .env file not found!"
    exit 1
fi

echo "🔧 Adding Firebase configuration to .env..."
echo ""

# Backup .env
cp .env .env.backup.firebase.$(date +%Y%m%d_%H%M%S)
echo "✅ Backup created"

# Function to update or add env variable
update_env() {
    local key=$1
    local value=$2
    
    if grep -q "^${key}=" .env; then
        # Update existing
        if [[ "$OSTYPE" == "darwin"* ]]; then
            sed -i '' "s|^${key}=.*|${key}=${value}|" .env
        else
            sed -i "s|^${key}=.*|${key}=${value}|" .env
        fi
        echo "  ✓ Updated: ${key}"
    else
        # Add new
        echo "${key}=${value}" >> .env
        echo "  ✓ Added: ${key}"
    fi
}

# Add Firebase configuration
echo ""
echo "📱 Setting up Firebase credentials..."

# Known values from FIREBASE-SETUP-GUIDE.md
update_env "FIREBASE_API_KEY" "AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I"
update_env "FIREBASE_PROJECT_ID" "fire-base-dojo-9"

# Check if additional values exist, if not add placeholders
if ! grep -q "^FIREBASE_MESSAGING_SENDER_ID=" .env; then
    echo "FIREBASE_MESSAGING_SENDER_ID=" >> .env
    echo "  ⚠️  Added FIREBASE_MESSAGING_SENDER_ID (empty - needs manual setup)"
fi

if ! grep -q "^FIREBASE_APP_ID=" .env; then
    echo "FIREBASE_APP_ID=" >> .env
    echo "  ⚠️  Added FIREBASE_APP_ID (empty - needs manual setup)"
fi

if ! grep -q "^FIREBASE_VAPID_KEY=" .env; then
    echo "FIREBASE_VAPID_KEY=" >> .env
    echo "  ⚠️  Added FIREBASE_VAPID_KEY (empty - needs manual setup)"
fi

echo ""
echo "🧹 Clearing Laravel caches..."
php artisan config:clear 2>/dev/null
php artisan cache:clear 2>/dev/null

echo ""
echo "╔════════════════════════════════════════════════╗"
echo "║  ✅ Firebase Configuration Updated             ║"
echo "╚════════════════════════════════════════════════╝"
echo ""
echo "📋 What's Configured:"
echo "  ✅ FIREBASE_API_KEY: AIzaSyAF... (set)"
echo "  ✅ FIREBASE_PROJECT_ID: fire-base-dojo-9 (set)"
echo "  ⚠️  FIREBASE_MESSAGING_SENDER_ID: (needs manual setup)"
echo "  ⚠️  FIREBASE_APP_ID: (needs manual setup)"
echo "  ⚠️  FIREBASE_VAPID_KEY: (needs manual setup)"
echo ""
echo "🔧 Next Steps to Complete Firebase Setup:"
echo ""
echo "1. Visit Firebase Console:"
echo "   https://console.firebase.google.com/project/fire-base-dojo-9/settings/general"
echo ""
echo "2. Get these values:"
echo "   • Cloud Messaging → Sender ID"
echo "   • General Settings → App ID"
echo "   • Cloud Messaging → Web Push certificates → VAPID key"
echo ""
echo "3. Add them to .env file:"
echo "   nano .env"
echo ""
echo "4. Or use this quick command (replace XXX with actual values):"
echo "   sed -i 's/FIREBASE_MESSAGING_SENDER_ID=/FIREBASE_MESSAGING_SENDER_ID=XXX/' .env"
echo "   sed -i 's/FIREBASE_APP_ID=/FIREBASE_APP_ID=XXX/' .env"
echo "   sed -i 's/FIREBASE_VAPID_KEY=/FIREBASE_VAPID_KEY=XXX/' .env"
echo ""
echo "5. Clear config cache again:"
echo "   php artisan config:clear"
echo ""
echo "6. Test in Admin → API Key Tester"
echo ""
echo "📝 Note: Firebase push notifications are OPTIONAL."
echo "   Your site works fine without them - they just add"
echo "   browser/mobile push notifications for better UX."
echo ""
