<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Str;

class SendMessageToTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    public function __construct(
        public string $text,
        public array $buttons,
        public string $token,
        public string $chatId,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
        $inlineButtons = [];
        if (!empty($this->buttons)) {
            foreach ($this->buttons as $button) {
                $buttonUrl = data_get($button, 'url');
                if ($buttonUrl && Str::contains($buttonUrl, 'http://localhost')) {
                    $buttonUrl = str_replace('http://localhost', config('app.url'), $buttonUrl);
                }
                $inlineButtons[] = [
                    'text' => $button['text'],
                    'url' => $buttonUrl,
                ];
            }
        }
        $payload = [
            'parse_mode' => 'markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [...$inlineButtons],
                ],
            ]),
            'chat_id' => $this->chatId,
            'text' => $this->text,
        ];
        ray($payload);
        $response = Http::post($url, $payload);
        if ($response->failed()) {
            throw new \Exception('Telegram notification failed with ' . $response->status() . ' status code.' . $response->body());
        }
    }
}
