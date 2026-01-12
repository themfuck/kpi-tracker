<?php
/**
 * Git Hook Deploy Webhook
 * Endpoint untuk menerima trigger deployment dari Git hooks
 * URL: https://yourdomain.com/deploy-webhook.php
 */

header('Content-Type: application/json');

// Configuration
$SECRET_TOKEN = 'your-secret-deploy-token-here'; // Change this!
$PROJECT_DIR = __DIR__;
$ALLOWED_IPS = [
    '127.0.0.1',
    '::1',
    // Add your server IPs here
    // '192.168.1.100',
];

$LOG_FILE = $PROJECT_DIR . '/deployment.log';

// Security checks
function securityCheck() {
    global $SECRET_TOKEN, $ALLOWED_IPS;
    
    // Check IP whitelist
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!empty($ALLOWED_IPS) && !in_array($clientIP, $ALLOWED_IPS)) {
        http_response_code(403);
        die(json_encode(['error' => 'IP not allowed']));
    }
    
    // Check secret token
    $token = $_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '';
    if ($token !== $SECRET_TOKEN) {
        http_response_code(403);
        die(json_encode(['error' => 'Invalid token']));
    }
}

// Logging function
function logMessage($message, $level = 'INFO') {
    global $LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message\n";
    file_put_contents($LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Also output to response
    echo $logEntry;
    if (ob_get_level()) {
        ob_flush();
    }
    flush();
}

// Execute command with logging
function executeCommand($command) {
    logMessage("Executing: $command");
    
    $descriptors = [
        0 => ['pipe', 'r'],  // stdin
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w']   // stderr
    ];
    
    $process = proc_open($command, $descriptors, $pipes);
    
    if (!is_resource($process)) {
        logMessage("Failed to start command: $command", 'ERROR');
        return false;
    }
    
    // Close stdin
    fclose($pipes[0]);
    
    // Read stdout and stderr
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    $returnCode = proc_close($process);
    
    if (!empty($stdout)) {
        logMessage("STDOUT: $stdout");
    }
    
    if (!empty($stderr)) {
        logMessage("STDERR: $stderr", 'WARN');
    }
    
    if ($returnCode !== 0) {
        logMessage("Command failed with exit code: $returnCode", 'ERROR');
        return false;
    }
    
    logMessage("Command completed successfully");
    return true;
}

// Main deployment function
function deploy() {
    global $PROJECT_DIR;
    
    // Change to project directory
    if (!chdir($PROJECT_DIR)) {
        logMessage("Failed to change to project directory: $PROJECT_DIR", 'ERROR');
        return false;
    }
    
    logMessage("🚀 Starting deployment...");
    
    // Deployment commands
    $commands = [
        'git pull origin main',
        'docker compose down',
        'docker system prune -f',
        'docker builder prune -f',
        'docker compose up -d --build --force-recreate',
        'sleep 5', // Wait for services to start
        'docker image prune -f'
    ];
    
    foreach ($commands as $command) {
        if (!executeCommand($command)) {
            logMessage("❌ Deployment failed at command: $command", 'ERROR');
            return false;
        }
    }
    
    // Health check
    logMessage("🏥 Performing health check...");
    if (executeCommand('docker compose ps')) {
        logMessage("✅ Deployment completed successfully!");
        return true;
    } else {
        logMessage("❌ Health check failed", 'ERROR');
        return false;
    }
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    // Security check
    securityCheck();
    
    logMessage("=== Deployment webhook triggered via $method ===");
    logMessage("Client IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    logMessage("User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
    
    // Handle POST data if available
    if ($method === 'POST') {
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            logMessage("POST data received: " . substr($input, 0, 200) . '...');
        }
    }
    
    // Start deployment
    if (deploy()) {
        http_response_code(200);
        $response = [
            'status' => 'success',
            'message' => 'Deployment completed successfully',
            'timestamp' => date('c')
        ];
    } else {
        http_response_code(500);
        $response = [
            'status' => 'error',
            'message' => 'Deployment failed',
            'timestamp' => date('c')
        ];
    }
    
} catch (Exception $e) {
    logMessage("Exception: " . $e->getMessage(), 'ERROR');
    http_response_code(500);
    $response = [
        'status' => 'error',
        'message' => 'Internal server error',
        'timestamp' => date('c')
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>