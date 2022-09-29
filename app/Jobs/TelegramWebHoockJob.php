<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kolgaev\TelegramBot\Telegram;

class TelegramWebHoockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected \App\Models\TelegramIncoming $income
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = decrypt($this->income->request_data);

        $telegram = new Telegram(env("TELEGRAM_BOT_TOKEN_FOR_DATA"));
        $telegram->sendMessage([
            'chat_id' => $this->income->from_id,
            'text' => $this->income->id,
        ]);
    }
}
