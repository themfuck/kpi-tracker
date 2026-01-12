#!/bin/bash

# Script untuk trigger deployment
# Usage: ./trigger-deploy.sh

# Konfigurasi
DEPLOY_URL="https://kpi.optimus-code.com/deploy.php?secret=rahasia123"

echo "🚀 Triggering deployment..."

# Push code ke GitHub/GitLab dulu
echo "📤 Pushing code to repository..."
git add .
git commit -m "Deploy: $(date '+%Y-%m-%d %H:%M:%S')" || echo "No changes to commit"
git push origin main

# Trigger deployment di server
echo "🌐 Triggering server deployment..."
curl -s "$DEPLOY_URL" || {
    echo "❌ Failed to trigger deployment"
    exit 1
}

echo ""
echo "✅ Deployment triggered successfully!"
echo "🌐 Check: $DEPLOY_URL"