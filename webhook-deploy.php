<?php
/**
 * Webhook Deploy Script
 * Place this on your server and configure GitHub/GitLab webhook to call it
 * URL: https://yourdomain.com/webhook-deploy.php
 */

// Security: Verify webhook secret (recommended)
$secret = 'your-webhook-secret-here'; // Change this!
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$payload = file_get_contents('php://input');

if (!hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature)) {
    http_response_code(403);
    die('Unauthorized');
}

// Log deployment
$logFile = __DIR__ . '/deployment.log';
$timestamp = date('Y-m-d H:i:s');

function logMessage($message) {
    global $logFile, $timestamp;
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    echo "$message\n";
}

logMessage("🚀 Webhook deployment started");

// Change to project directory
$projectDir = __DIR__;
chdir($projectDir);

// Execute deployment commands
$commands = [
    'git pull origin main',
    'docker compose down',
    'docker system prune -f',
    'docker builder prune -f',
    'docker compose up -d --build --force-recreate',
    'docker image prune -f'
];

foreach ($commands as $command) {
    logMessage("Executing: $command");
    
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        logMessage("❌ Command failed: $command");
        logMessage("Output: " . implode("\n", $output));
        http_response_code(500);
        die("Deployment failed");
    }
    
    logMessage("✅ Command completed: $command");
}

logMessage("✅ Deployment completed successfully");

// Return success response
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deployment completed',
    'timestamp' => $timestamp
]);
?>