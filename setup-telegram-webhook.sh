#!/bin/bash

# Telegram Webhook Setup Script
# This script helps you set up the Telegram webhook for your bot

echo "🤖 Telegram Webhook Setup Script"
echo "=================================="
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    echo "Please copy .env.example to .env and configure it first."
    exit 1
fi

# Load environment variables
source .env

# Check if bot token is set
if [ -z "$TELEGRAM_BOT_TOKEN" ]; then
    echo "❌ Error: TELEGRAM_BOT_TOKEN is not set in .env file!"
    exit 1
fi

# Check if webhook token is set
if [ -z "$TELEGRAM_WEBHOOK_TOKEN" ]; then
    echo "❌ Error: TELEGRAM_WEBHOOK_TOKEN is not set in .env file!"
    exit 1
fi

# Check if APP_URL is set
if [ -z "$APP_URL" ]; then
    echo "❌ Error: APP_URL is not set in .env file!"
    exit 1
fi

WEBHOOK_URL="${APP_URL}/api/telegram/webhook"

echo "📋 Configuration:"
echo "   Bot Token: ${TELEGRAM_BOT_TOKEN:0:10}..."
echo "   Webhook URL: $WEBHOOK_URL"
echo "   Webhook Token: ${TELEGRAM_WEBHOOK_TOKEN:0:10}..."
echo ""

read -p "Do you want to set this webhook? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Cancelled."
    exit 0
fi

echo ""
echo "🔄 Setting webhook..."
echo ""

# Set webhook
RESPONSE=$(curl -s -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook" \
  -H "Content-Type: application/json" \
  -d "{
    \"url\": \"${WEBHOOK_URL}\",
    \"secret_token\": \"${TELEGRAM_WEBHOOK_TOKEN}\"
  }")

echo "Response: $RESPONSE"
echo ""

# Check if successful
if echo "$RESPONSE" | grep -q '"ok":true'; then
    echo "✅ Webhook set successfully!"
    echo ""
    echo "🔍 Verifying webhook..."
    echo ""
    
    # Get webhook info
    INFO=$(curl -s "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo")
    echo "$INFO" | python3 -m json.tool 2>/dev/null || echo "$INFO"
    
    echo ""
    echo "✅ Setup complete!"
    echo ""
    echo "📝 Next steps:"
    echo "   1. Open Telegram and search for your bot"
    echo "   2. Send /start to begin"
    echo "   3. Try sending data with format:"
    echo "      NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES"
    echo ""
    echo "   Example:"
    echo "      Andi|2026-01-12|3.5|15000000|120|5000|1200"
    echo ""
else
    echo "❌ Failed to set webhook!"
    echo ""
    echo "Please check:"
    echo "   1. Your bot token is correct"
    echo "   2. Your APP_URL is accessible from internet (must be HTTPS)"
    echo "   3. Your server allows incoming connections"
    echo ""
fi
