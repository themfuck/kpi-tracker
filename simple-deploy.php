<?php
/**
 * Simple Deploy Webhook
 * Taruh file ini di server: /var/www/html/deploy.php
 * Akses: https://yourdomain.com/deploy.php?secret=rahasia123
 */

// Konfigurasi
$SECRET = 'rahasia123'; // Ganti dengan password Anda
$PROJECT_PATH = '/var/www/kpi-tracker'; // Path project di server

// Cek password
if ($_GET['secret'] !== $SECRET) {
    die('❌ Password salah!');
}

echo "<h2>🚀 Starting Deployment...</h2>";
echo "<pre>";

// Function untuk jalankan command
function runCommand($cmd) {
    echo "➤ $cmd\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
    return $output;
}

// Pindah ke directory project
chdir($PROJECT_PATH);

// Step 1: Pull latest code
echo "📥 Pulling latest code...\n";
runCommand('git pull origin main');

// Step 2: Stop containers
echo "🛑 Stopping containers...\n";
runCommand('docker compose down');

// Step 3: Clean Docker
echo "🧹 Cleaning Docker...\n";
runCommand('docker system prune -f');
runCommand('docker builder prune -f');

// Step 4: Build & Start
echo "🔨 Building and starting...\n";
runCommand('docker compose up -d --build --force-recreate');

// Step 5: Final cleanup
echo "🧹 Final cleanup...\n";
runCommand('docker image prune -f');

// Step 6: Show status
echo "📊 Container status:\n";
runCommand('docker compose ps');

echo "</pre>";
echo "<h2>✅ Deployment Complete!</h2>";
?>