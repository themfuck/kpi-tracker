<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    /**
     * Handle incoming webhook from Telegram
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            // Log incoming request for debugging
            Log::info('Telegram webhook received', [
                'data' => $request->all()
            ]);

            // Validate webhook token (optional but recommended)
            $webhookToken = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if ($webhookToken !== config('services.telegram.webhook_token')) {
                Log::warning('Invalid webhook token', [
                    'received_token' => $webhookToken
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Process the webhook
            $result = $this->telegramService->processWebhook($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Webhook processed successfully',
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process webhook',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
