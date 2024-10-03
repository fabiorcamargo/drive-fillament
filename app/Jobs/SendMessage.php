<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $body;

    public function __construct($phone, $body)
    {
        $this->phone = $phone;
        $this->body = $body;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $url = env('EVO_URL') . '/message/sendText/' . env('EVO_NAME');
            $apikey = env('EVO_APIKEY');

            $headers = [
                'Content-Type' => 'application/json',
                'apikey' => $apikey
            ];

            $body = [
                "number" => $this->phone,
                "text" => $this->body
            ];

            Http::withHeaders($headers)
                ->post($url, $body);
            
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error('Failed to send message: ' . $e->getMessage());
        }
    }
}
