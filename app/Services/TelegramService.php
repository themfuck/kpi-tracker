<?php

namespace App\Services;

use App\Models\LiveSession;
use App\Models\Host;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramService
{
    /**
     * Process incoming webhook from Telegram
     *
     * @param array $data
     * @return array
     */
    public function processWebhook(array $data): array
    {
        // Check if this is a message update
        if (!isset($data['message'])) {
            return [
                'processed' => false,
                'reason' => 'Not a message update'
            ];
        }

        $message = $data['message'];
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'] ?? null;

        // Check if message starts with a command
        if (str_starts_with($text, '/')) {
            return $this->handleCommand($text, $chatId, $message);
        }

        // Parse data input (format: HOST_NAME|DATE|HOURS|GMV|ORDERS|VIEWERS|LIKES)
        return $this->handleDataInput($text, $chatId, $message);
    }

    /**
     * Handle Telegram commands
     *
     * @param string $text
     * @param int|null $chatId
     * @param array $message
     * @return array
     */
    protected function handleCommand(string $text, ?int $chatId, array $message): array
    {
        $command = strtolower(explode(' ', $text)[0]);

        switch ($command) {
            case '/start':
                return [
                    'processed' => true,
                    'action' => 'send_message',
                    'chat_id' => $chatId,
                    'message' => "Selamat datang di KPI Tracker Bot!\n\n" .
                                "Format input data:\n" .
                                "NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES\n\n" .
                                "Contoh:\n" .
                                "Andi|2026-01-12|3.5|15000000|120|5000|1200\n\n" .
                                "Gunakan /help untuk bantuan lebih lanjut."
                ];

            case '/help':
                return [
                    'processed' => true,
                    'action' => 'send_message',
                    'chat_id' => $chatId,
                    'message' => "📊 KPI Tracker Bot - Panduan\n\n" .
                                "Format input:\n" .
                                "NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES\n\n" .
                                "Keterangan:\n" .
                                "- NAMA_HOST: Nama host (contoh: Andi)\n" .
                                "- TANGGAL: Format YYYY-MM-DD (contoh: 2026-01-12)\n" .
                                "- JAM_LIVE: Durasi live dalam jam (contoh: 3.5)\n" .
                                "- GMV: Gross Merchandise Value (contoh: 15000000)\n" .
                                "- ORDERS: Jumlah pesanan (contoh: 120)\n" .
                                "- VIEWERS: Jumlah penonton (contoh: 5000)\n" .
                                "- LIKES: Jumlah likes (contoh: 1200)\n\n" .
                                "Gunakan tanda | sebagai pemisah."
                ];

            default:
                return [
                    'processed' => false,
                    'reason' => 'Unknown command'
                ];
        }
    }

    /**
     * Handle data input from Telegram
     *
     * @param string $text
     * @param int|null $chatId
     * @param array $message
     * @return array
     */
    protected function handleDataInput(string $text, ?int $chatId, array $message): array
    {
        try {
            // Parse the input (format: HOST_NAME|DATE|HOURS|GMV|ORDERS|VIEWERS|LIKES)
            $parts = explode('|', $text);

            if (count($parts) < 7) {
                return [
                    'processed' => false,
                    'action' => 'send_message',
                    'chat_id' => $chatId,
                    'message' => "❌ Format salah!\n\n" .
                                "Format yang benar:\n" .
                                "NAMA_HOST|TANGGAL|JAM_LIVE|GMV|ORDERS|VIEWERS|LIKES\n\n" .
                                "Gunakan /help untuk bantuan."
                ];
            }

            [$hostName, $date, $hoursLive, $gmv, $orders, $viewers, $likes] = array_map('trim', $parts);

            // Find or create host
            $host = Host::firstOrCreate(
                ['name' => $hostName],
                ['name' => $hostName]
            );

            // Create live session
            $liveSession = LiveSession::create([
                'host_id' => $host->id,
                'date' => Carbon::parse($date),
                'hours_live' => (float) $hoursLive,
                'gmv' => (float) $gmv,
                'orders' => (int) $orders,
                'viewers' => (int) $viewers,
                'likes' => (int) $likes,
                'errors' => null,
            ]);

            // Calculate metrics
            $gmvPerHour = $hoursLive > 0 ? $gmv / $hoursLive : 0;
            $conversionRate = $viewers > 0 ? ($orders / $viewers) * 100 : 0;
            $aov = $orders > 0 ? $gmv / $orders : 0;
            $likesPerMinute = $hoursLive > 0 ? $likes / ($hoursLive * 60) : 0;

            Log::info('Live session created from Telegram', [
                'session_id' => $liveSession->id,
                'host' => $hostName,
                'chat_id' => $chatId
            ]);

            return [
                'processed' => true,
                'action' => 'send_message',
                'chat_id' => $chatId,
                'message' => "✅ Data berhasil disimpan!\n\n" .
                            "📊 Ringkasan:\n" .
                            "Host: {$hostName}\n" .
                            "Tanggal: {$date}\n" .
                            "Durasi: {$hoursLive} jam\n" .
                            "GMV: Rp " . number_format($gmv, 0, ',', '.') . "\n" .
                            "Orders: {$orders}\n" .
                            "Viewers: {$viewers}\n" .
                            "Likes: {$likes}\n\n" .
                            "📈 Metrics:\n" .
                            "GMV/Jam: Rp " . number_format($gmvPerHour, 0, ',', '.') . "\n" .
                            "Conversion Rate: " . number_format($conversionRate, 2) . "%\n" .
                            "AOV: Rp " . number_format($aov, 0, ',', '.') . "\n" .
                            "Likes/Menit: " . number_format($likesPerMinute, 1),
                'data' => [
                    'session_id' => $liveSession->id,
                    'host_id' => $host->id
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process Telegram data input', [
                'error' => $e->getMessage(),
                'text' => $text
            ]);

            return [
                'processed' => false,
                'action' => 'send_message',
                'chat_id' => $chatId,
                'message' => "❌ Gagal menyimpan data!\n\n" .
                            "Error: " . $e->getMessage() . "\n\n" .
                            "Gunakan /help untuk format yang benar."
            ];
        }
    }

    /**
     * Send message back to Telegram (optional - for bot responses)
     *
     * @param int $chatId
     * @param string $message
     * @return bool
     */
    public function sendMessage(int $chatId, string $message): bool
    {
        $botToken = config('services.telegram.bot_token');
        
        if (!$botToken) {
            Log::warning('Telegram bot token not configured');
            return false;
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML'
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            curl_close($ch);

            Log::info('Message sent to Telegram', [
                'chat_id' => $chatId,
                'response' => $response
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);
            return false;
        }
    }
}
