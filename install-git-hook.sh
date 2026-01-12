#!/bin/bash

# Script untuk install Git Hook
# Usage: ./install-git-hook.sh

echo "🔧 Installing Git Hook..."

# Cek apakah di dalam git repo
if [ ! -d ".git" ]; then
    echo "❌ Not a git repository!"
    exit 1
fi

# Copy hook ke .git/hooks
cp local-git-hook.sh .git/hooks/post-commit
chmod +x .git/hooks/post-commit

echo "✅ Git Hook installed!"
echo ""
echo "📋 How it works:"
echo "1. Setiap kali Anda commit di branch 'main'"
echo "2. Hook akan otomatis push ke GitHub"
echo "3. Lalu trigger deployment di server"
echo ""
echo "🧪 Test dengan:"
echo "git add ."
echo "git commit -m 'test auto deploy'"