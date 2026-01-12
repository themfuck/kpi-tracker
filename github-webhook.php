<?php
/**
 * GitHub Webhook Receiver
 * Taruh di server: /var/www/html/github-webhook.php
 * 
 * Setup di GitHub:
 * 1. Repo Settings > Webhooks > Add webhook
 * 2. URL: https://yourdomain.com/github-webhook.php
 * 3. Secret: rahasia123
 * 4. Events: Just push events
 */

// Konfigurasi
$SECRET = 'rahasia123';
$PROJECT_PATH = '/var/www/kpi-tracker';
$BRANCH = 'main';

// Verify GitHub signature
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

if (!hash_equals('sha256=' . hash_hmac('sha256', $payload, $SECRET), $signature)) {
    http_response_code(403);
    die('Unauthorized');
}

// Parse payload
$data = json_decode($payload, true);

// Cek apakah push ke branch main
if ($data['ref'] !== "refs/heads/$BRANCH") {
    die("Push to {$data['ref']}, ignoring");
}

echo "🚀 GitHub Webhook received for branch: $BRANCH\n";
echo "📝 Commit: {$data['head_commit']['message']}\n";
echo "👤 Author: {$data['head_commit']['author']['name']}\n\n";

// Function untuk jalankan command
function runCommand($cmd) {
    echo "➤ $cmd\n";
    $output = shell_exec($cmd . ' 2>&1');
    echo $output . "\n";
    return $output;
}

// Pindah ke directory project
chdir($PROJECT_PATH);

// Deployment steps
echo "📥 Pulling latest code...\n";
runCommand('git pull origin main');

echo "🛑 Stopping containers...\n";
runCommand('docker compose down');

echo "🧹 Cleaning Docker...\n";
runCommand('docker system prune -f');
runCommand('docker builder prune -f');

echo "🔨 Building and starting...\n";
runCommand('docker compose up -d --build --force-recreate');

echo "🧹 Final cleanup...\n";
runCommand('docker image prune -f');

echo "📊 Container status:\n";
runCommand('docker compose ps');

echo "✅ Deployment complete!\n";

// Log deployment
$log = date('Y-m-d H:i:s') . " - Deployed commit: {$data['head_commit']['id']}\n";
file_put_contents($PROJECT_PATH . '/deployment.log', $log, FILE_APPEND);
?>