<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook {action=info : Action to perform (set, info, delete)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Telegram webhook (set, info, delete)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $botToken = config('services.telegram.bot_token');

        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN is not set in .env file!');
            return 1;
        }

        switch ($action) {
            case 'set':
                return $this->setWebhook($botToken);
            case 'info':
                return $this->getWebhookInfo($botToken);
            case 'delete':
                return $this->deleteWebhook($botToken);
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: set, info, delete");
                return 1;
        }
    }

    /**
     * Set webhook
     */
    protected function setWebhook($botToken)
    {
        $webhookUrl = config('app.url') . '/api/telegram/webhook';
        $webhookToken = config('services.telegram.webhook_token');

        if (!$webhookToken) {
            $this->error('❌ TELEGRAM_WEBHOOK_TOKEN is not set in .env file!');
            return 1;
        }

        $this->info("🔄 Setting webhook...");
        $this->info("   URL: {$webhookUrl}");

        $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
            'url' => $webhookUrl,
            'secret_token' => $webhookToken,
        ]);

        if ($response->successful() && $response->json('ok')) {
            $this->info("✅ Webhook set successfully!");
            $this->newLine();
            return $this->getWebhookInfo($botToken);
        } else {
            $this->error("❌ Failed to set webhook!");
            $this->error("Response: " . $response->body());
            return 1;
        }
    }

    /**
     * Get webhook info
     */
    protected function getWebhookInfo($botToken)
    {
        $this->info("🔍 Getting webhook info...");
        $this->newLine();

        $response = Http::get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");

        if ($response->successful()) {
            $info = $response->json('result');
            
            $this->table(
                ['Property', 'Value'],
                [
                    ['URL', $info['url'] ?? 'Not set'],
                    ['Has Custom Certificate', $info['has_custom_certificate'] ? 'Yes' : 'No'],
                    ['Pending Update Count', $info['pending_update_count'] ?? 0],
                    ['Last Error Date', isset($info['last_error_date']) ? date('Y-m-d H:i:s', $info['last_error_date']) : 'None'],
                    ['Last Error Message', $info['last_error_message'] ?? 'None'],
                    ['Max Connections', $info['max_connections'] ?? 'Default'],
                    ['IP Address', $info['ip_address'] ?? 'Not available'],
                ]
            );

            if (isset($info['last_error_message'])) {
                $this->newLine();
                $this->warn("⚠️  Last error: " . $info['last_error_message']);
            }

            return 0;
        } else {
            $this->error("❌ Failed to get webhook info!");
            return 1;
        }
    }

    /**
     * Delete webhook
     */
    protected function deleteWebhook($botToken)
    {
        if (!$this->confirm('Are you sure you want to delete the webhook?')) {
            $this->info('Cancelled.');
            return 0;
        }

        $this->info("🗑️  Deleting webhook...");

        $response = Http::post("https://api.telegram.org/bot{$botToken}/deleteWebhook");

        if ($response->successful() && $response->json('ok')) {
            $this->info("✅ Webhook deleted successfully!");
            return 0;
        } else {
            $this->error("❌ Failed to delete webhook!");
            $this->error("Response: " . $response->body());
            return 1;
        }
    }
}
