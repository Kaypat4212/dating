# Security Cleanup Guide - Exposed Credentials

## ⚠️ Critical: Your credentials are publicly exposed on GitHub

This guide will help you secure your repository and regenerate compromised credentials.

## Step 1: Regenerate Exposed Credentials (URGENT)

### 1.1 Regenerate Firebase API Key
1. Go to https://console.firebase.google.com/project/fire-base-dojo-9/settings/general
2. Click "Web API Key" section → "Regenerate Key"
3. Copy the new API key
4. Update in Admin Panel → Firebase & Analytics Settings
5. Update `.env`: `FIREBASE_API_KEY=<new_key>`

**OR** Create a new Firebase project:
1. https://console.firebase.google.com → "Add project"
2. Set up Cloud Messaging
3. Get new credentials
4. Update everywhere

### 1.2 Regenerate Pusher Credentials
1. Go to https://dashboard.pusher.com/apps
2. Select your app or create a new one
3. Go to "App Keys" → Click "Reset Credentials"
4. Copy new: App ID, Key, Secret, Cluster
5. Update `.env`:
   ```
   PUSHER_APP_ID=<new_id>
   PUSHER_APP_KEY=<new_key>
   PUSHER_APP_SECRET=<new_secret>
   PUSHER_APP_CLUSTER=<cluster>
   ```

### 1.3 Regenerate Laravel APP_KEY
```bash
php artisan key:generate
```
This will update your `.env` automatically.

## Step 2: Clean Git History

### Option A: Remove Specific Commits (Recommended if few commits)
```bash
# Remove env.production.txt from history
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch env.production.txt" \
  --prune-empty --tag-name-filter cat -- --all

# Force push to GitHub (DESTRUCTIVE)
git push origin --force --all
```

### Option B: Create Fresh Repository (Nuclear Option)
If your repo is already public and heavily exposed:

1. **Backup your code locally**
2. **Delete the GitHub repository completely**
3. **Create a new empty repository on GitHub**
4. **Clean up sensitive files locally (see Step 3)**
5. **Push cleaned code to new repo:**
   ```bash
   git remote remove origin
   git remote add origin https://github.com/Kaypat4212/dating-new.git
   git push -u origin master
   ```

## Step 3: Remove Credentials from Code Files

### 3.1 Clean Documentation Files
Replace real credentials with placeholders in these files:

**FIREBASE-FIX.md:**
```bash
# Find and replace
AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I → YOUR_FIREBASE_API_KEY_HERE
fire-base-dojo-9 → YOUR_PROJECT_ID_HERE
```

**FIREBASE-SETUP-GUIDE.md** - Same as above

**fix-firebase.sh** - Same as above

**IN-APP-NOTIFICATIONS-README.md:**
```bash
PUSHER_APP_ID=2139938 → PUSHER_APP_ID=your_app_id_here
PUSHER_APP_KEY=1e1d2a23e398b4c746d2 → PUSHER_APP_KEY=your_key_here
PUSHER_APP_SECRET=b1347f1e61589efbf320 → PUSHER_APP_SECRET=your_secret_here
```

**REALTIME-STREAKS-DEPLOYMENT.md** - Same as above

### 3.2 Update JavaScript Files
**public/firebase-messaging-sw.js:**
```javascript
firebase.initializeApp({
    apiKey: "YOUR_FIREBASE_API_KEY",  // Don't hardcode real key
    projectId: "YOUR_PROJECT_ID",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
});
```

**resources/js/firebase-init.js:**
```javascript
const firebaseConfig = {
    apiKey: "YOUR_FIREBASE_API_KEY",  // Use template value
    projectId: "YOUR_PROJECT_ID",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
};
```

**Note:** Firebase API keys in frontend are okay if you have proper Firebase Security Rules, but PROJECT_ID should be generic in examples.

### 3.3 Update Admin Panel Files
**resources/views/filament/pages/api-key-tester.blade.php:**

Find line 164 and replace:
```php
'howto' => "✅ Example setup...\n(Use generic placeholders, not real keys)"
```

### 3.4 Delete Compiled Assets (They contain secrets!)
```bash
# These should NEVER be committed
rm -rf public/build/
echo "public/build/" >> .gitignore
```

**Important:** Run `npm run build` or `php artisan vite:build` on your server, not locally. Never commit compiled assets.

## Step 4: Update .gitignore

Add these to your `.gitignore`:
```gitignore
# Compiled assets (may contain secrets from .env)
/public/build/
/public/hot

# All environment files
.env
.env.*
env.production.txt
*.env

# Backup files
*.backup
*.bak

# IDE files that might contain secrets
.idea/
.vscode/
*.sublime-project
*.sublime-workspace
```

## Step 5: Verify Clean Repository

```bash
# Search for any remaining exposed secrets
git grep -i "AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I"
git grep -i "1e1d2a23e398b4c746d2"
git grep -i "b1347f1e61589efbf320"

# Should return no results
```

## Step 6: Security Best Practices Going Forward

### 6.1 Never Commit These:
- ❌ `.env` files (any variant)
- ❌ Real API keys in documentation
- ❌ Compiled JavaScript (`public/build/`)
- ❌ Database dumps with real data
- ❌ Backup files with credentials

### 6.2 Use Placeholders in Docs:
```markdown
# Good ✅
FIREBASE_API_KEY=your_api_key_here

# Bad ❌
FIREBASE_API_KEY=AIzaSyAFBjyUOQ1DIhcTMyqo46fP27eWfsU38_I
```

### 6.3 Use .env.example for Documentation:
- Keep `.env.example` with placeholder values
- Never copy real values to `.env.example`
- Commit `.env.example` to show required variables

### 6.4 Review Before Committing:
```bash
# Always review what you're committing
git diff --cached

# Search staged files for secrets before commit
git diff --cached | grep -i "api.*key\|secret\|password"
```

## Emergency Contacts

If credentials are already public:
1. **Firebase:** https://firebase.google.com/support
2. **Pusher:** https://support.pusher.com/
3. **GitHub Security:** https://github.com/security

## Summary Checklist

- [ ] Regenerate Firebase API key
- [ ] Regenerate Pusher credentials  
- [ ] Regenerate Laravel APP_KEY
- [ ] Clean git history or create fresh repo
- [ ] Replace real credentials with placeholders in all `.md` files
- [ ] Remove hardcoded keys from JavaScript files
- [ ] Delete `public/build/` and add to `.gitignore`
- [ ] Update `.gitignore` with comprehensive exclusions
- [ ] Verify no secrets remain: `git grep -i "secret\|key"`
- [ ] Force push cleaned history OR push to new repo
- [ ] Test application still works with new credentials

## Time Estimate
- Quick fix (just regenerate keys): 30 minutes
- Full cleanup (git history): 2-3 hours
- Fresh repo approach: 1 hour

**Priority:** Do Step 1 (Regenerate Credentials) immediately, then tackle cleanup.
