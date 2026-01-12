# KPI Tracker - Easy Deploy Commands

.PHONY: help deploy deploy-clean build clean logs status restart

# Default target
help:
	@echo "🚀 KPI Tracker Deploy Commands"
	@echo ""
	@echo "Available commands:"
	@echo "  make deploy       - Quick deploy (pull + rebuild)"
	@echo "  make deploy-clean - Clean deploy (removes all cache)"
	@echo "  make build        - Build containers only"
	@echo "  make clean        - Clean Docker resources"
	@echo "  make logs         - Show container logs"
	@echo "  make status       - Show container status"
	@echo "  make restart      - Restart containers"
	@echo ""

# Quick deploy
deploy:
	@echo "🔄 Quick deployment..."
	git pull origin main
	docker compose down
	docker compose up -d --build
	@echo "✅ Deployment completed!"

# Clean deploy (removes all cache)
deploy-clean:
	@echo "🧹 Clean deployment..."
	git pull origin main
	docker compose down
	docker system prune -f
	docker builder prune -f
	docker compose up -d --build --force-recreate
	docker image prune -f
	@echo "✅ Clean deployment completed!"

# Build only
build:
	@echo "🔨 Building containers..."
	docker compose build --no-cache

# Clean Docker resources
clean:
	@echo "🧹 Cleaning Docker resources..."
	docker compose down
	docker system prune -f
	docker builder prune -f
	docker image prune -f
	@echo "✅ Cleanup completed!"

# Show logs
logs:
	docker compose logs --tail=50 -f

# Show status
status:
	@echo "📊 Container Status:"
	docker compose ps
	@echo ""
	@echo "💾 Disk Usage:"
	docker system df

# Restart containers
restart:
	@echo "🔄 Restarting containers..."
	docker compose restart
	@echo "✅ Containers restarted!"