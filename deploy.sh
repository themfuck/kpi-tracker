#!/bin/bash

# Auto Deploy Script - Clean & Efficient
# Usage: ./deploy.sh

set -e

echo "🚀 Starting deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if git repo is clean
if [[ -n $(git status --porcelain) ]]; then
    print_warning "Working directory is not clean. Uncommitted changes detected."
    read -p "Continue anyway? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Pull latest changes
print_status "Pulling latest changes from git..."
git pull origin main

# Stop existing containers
print_status "Stopping existing containers..."
docker compose down

# Clean up Docker resources
print_status "Cleaning up Docker resources..."
# Remove unused containers, networks, images (dangling), and build cache
docker system prune -f

# Remove build cache specifically
docker builder prune -f

# Remove any stopped containers
docker container prune -f

# Build and start services
print_status "Building and starting services..."
docker compose up -d --build --force-recreate

# Wait a moment for services to start
sleep 5

# Clean up dangling images after successful build
print_status "Final cleanup..."
docker image prune -f

# Show status
print_status "Deployment completed! Container status:"
docker compose ps

# Show logs for verification
print_status "Recent logs:"
docker compose logs --tail=20

echo -e "${GREEN}✅ Deployment successful!${NC}"