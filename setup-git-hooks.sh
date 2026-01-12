#!/bin/bash

# Setup Git Hooks for Auto Deploy
# Usage: ./setup-git-hooks.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔧 Setting up Git Hooks for Auto Deploy${NC}"

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ Not a git repository. Please run this from your project root.${NC}"
    exit 1
fi

# Create hooks directory if it doesn't exist
mkdir -p .git/hooks

echo -e "${YELLOW}📋 Available Git Hook setups:${NC}"
echo "1. Client-side hooks (pre-push validation)"
echo "2. Server-side hooks (post-receive deployment)"
echo "3. Webhook setup (for remote deployment)"
echo "4. All of the above"

read -p "Choose setup (1-4): " choice

case $choice in
    1|4)
        echo -e "${YELLOW}🔧 Setting up client-side hooks...${NC}"
        
        # Copy pre-push hook
        cp git-hooks/pre-push .git/hooks/pre-push
        chmod +x .git/hooks/pre-push
        
        echo -e "${GREEN}✅ Pre-push hook installed${NC}"
        echo -e "${YELLOW}ℹ️  Configure the REMOTE_NAME and DEPLOY_ENDPOINT in .git/hooks/pre-push${NC}"
        ;;
esac

case $choice in
    2|4)
        echo -e "${YELLOW}🔧 Setting up server-side hooks...${NC}"
        
        # Instructions for server-side setup
        echo -e "${BLUE}📝 Server-side setup instructions:${NC}"
        echo "1. On your server, create a bare repository:"
        echo "   git clone --bare https://github.com/yourusername/yourrepo.git /path/to/repo.git"
        echo ""
        echo "2. Copy the post-receive hook:"
        echo "   cp git-hooks/post-receive /path/to/repo.git/hooks/post-receive"
        echo "   chmod +x /path/to/repo.git/hooks/post-receive"
        echo ""
        echo "3. Edit the post-receive hook and set DEPLOY_DIR to your actual deployment path"
        echo ""
        echo "4. Add the server as a remote:"
        echo "   git remote add production user@server:/path/to/repo.git"
        echo ""
        echo "5. Deploy with:"
        echo "   git push production main"
        
        echo -e "${GREEN}✅ Server-side hook template ready${NC}"
        ;;
esac

case $choice in
    3|4)
        echo -e "${YELLOW}🔧 Setting up webhook deployment...${NC}"
        
        # Make webhook executable
        chmod +x deploy-webhook.php
        
        echo -e "${BLUE}📝 Webhook setup instructions:${NC}"
        echo "1. Upload deploy-webhook.php to your server"
        echo "2. Edit the configuration in deploy-webhook.php:"
        echo "   - Set SECRET_TOKEN"
        echo "   - Set ALLOWED_IPS"
        echo "   - Set PROJECT_DIR"
        echo ""
        echo "3. Test the webhook:"
        echo "   curl -X POST 'https://yourdomain.com/deploy-webhook.php?token=your-secret-token'"
        echo ""
        echo "4. For GitHub integration, add webhook URL to your repository settings"
        
        echo -e "${GREEN}✅ Webhook deployment script ready${NC}"
        ;;
esac

# Create deployment configuration file
echo -e "${YELLOW}📝 Creating deployment configuration...${NC}"

cat > .deploy-config << EOF
# Deployment Configuration
# Edit these values according to your setup

# Server configuration
SERVER_HOST="your-server.com"
SERVER_USER="deploy"
DEPLOY_PATH="/var/www/kpi-tracker"

# Git configuration
DEPLOY_BRANCH="main"
REMOTE_NAME="production"

# Webhook configuration
WEBHOOK_URL="https://your-server.com/deploy-webhook.php"
WEBHOOK_SECRET="your-secret-token-here"

# Docker configuration
COMPOSE_FILE="docker-compose.yml"
COMPOSE_PROJECT_NAME="kpi-tracker"
EOF

echo -e "${GREEN}✅ Configuration file created: .deploy-config${NC}"

# Create simple deploy command
cat > git-deploy << 'EOF'
#!/bin/bash

# Simple Git Deploy Command
# Usage: ./git-deploy

source .deploy-config 2>/dev/null || {
    echo "❌ Configuration file not found. Run ./setup-git-hooks.sh first"
    exit 1
}

echo "🚀 Deploying to production..."

if git remote | grep -q "$REMOTE_NAME"; then
    git push "$REMOTE_NAME" "$DEPLOY_BRANCH"
else
    echo "❌ Remote '$REMOTE_NAME' not found. Add it with:"
    echo "git remote add $REMOTE_NAME $SERVER_USER@$SERVER_HOST:$DEPLOY_PATH"
fi
EOF

chmod +x git-deploy

echo -e "${GREEN}✅ Git deploy command created: ./git-deploy${NC}"

echo ""
echo -e "${BLUE}🎉 Git Hooks setup completed!${NC}"
echo ""
echo -e "${YELLOW}Next steps:${NC}"
echo "1. Edit .deploy-config with your actual server details"
echo "2. Follow the instructions above for your chosen setup"
echo "3. Test deployment with: ./git-deploy"
echo ""
echo -e "${GREEN}Happy deploying! 🚀${NC}"