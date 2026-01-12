#!/bin/bash

# Git Hook: post-commit
# File ini akan dijalankan setiap kali Anda commit
# Copy ke: .git/hooks/post-commit

# Konfigurasi
DEPLOY_URL="https://kpi.optimus-code.com/deploy.php?secret=rahasia123"
BRANCH="main"

# Cek branch saat ini
current_branch=$(git branch --show-current)

echo "🔍 Current branch: $current_branch"

# Hanya deploy jika di branch main
if [ "$current_branch" = "$BRANCH" ]; then
    echo "🚀 Auto-deploying to production..."
    
    # Push ke remote dulu
    git push origin main
    
    # Trigger deployment
    echo "🌐 Triggering server deployment..."
    curl -s "$DEPLOY_URL" > /dev/null && {
        echo "✅ Deployment triggered!"
    } || {
        echo "❌ Failed to trigger deployment"
    }
else
    echo "ℹ️  Not on main branch, skipping deployment"
fi