<?php

namespace App\Http\Controllers\Telegram\Commands;

use App\Http\Controllers\Telegram\Handler;
use App\Models\AllData\Gibdd2Full;
use App\Models\TelegramAccessChatId;
use Illuminate\Support\Str;

class Number extends Handler
{
    /**
     * Запуск обработки команды
     * 
     * @param  string $text
     * @param  int|string $chat_id
     * @return string
     */
    public function run($text, $chat_id)
    {
        if (!TelegramAccessChatId::where('chat_id', $chat_id)->first())
            return "Access denied chat_id:{$chat_id}";

        $text = explode(" ", preg_replace('/\s/', ' ', trim($text)));

        $number = "";

        foreach ($text as $key => $word) {

            if ($key == 0)
                continue;

            $number .= $word;
        }

        $number = Str::upper($number);

        $messages = [];

        $rows = Gibdd2Full::where('gibdd2_car_plate_number', $number)
            ->get()
            ->each(function ($row) use (&$messages) {

                $message = "*Номер телефона* {$row->phone_number}\n";

                if ((bool) $row->gibdd2_car_plate_number)
                    $message .= "*Гос. номер* `{$row->gibdd2_car_plate_number}`\n";

                if ((bool) $row->gibdd2_old_car_plate_number)
                    $message .= "*Старый гос. номер* `{$row->gibdd2_old_car_plate_number}`\n";

                if ((bool) $row->gibdd2_car_model)
                    $message .= "*Марка, модель* `{$row->gibdd2_car_model}`\n";

                if ((bool) $row->gibdd2_car_color)
                    $message .= "*Цвет* `{$row->gibdd2_car_color}`\n";

                if ((bool) $row->gibdd2_car_year)
                    $message .= "*Год выпуска* `{$row->gibdd2_car_year}`\n";

                if ((bool) $row->gibdd2_car_vin)
                    $message .= "*WIN* `{$row->gibdd2_car_vin}`\n";

                if ((bool) $row->gibdd2_name)
                    $message .= "*Имя* {$row->gibdd2_name}\n";

                if ((bool) $row->gibdd2_base_name)
                    $message .= "*ФИО* `{$row->gibdd2_base_name}`\n";

                if ((bool) $row->gibdd2_dateofbirth)
                    $message .= "*Дата рождения* `{$row->gibdd2_dateofbirth}`\n";

                if ((bool) $row->gibdd2_address)
                    $message .= "*Адрес* `{$row->gibdd2_address}`\n";

                if ((bool) $row->gibdd2_passport)
                    $message .= "*Паспорт* `{$row->gibdd2_passport}`\n";

                if ((bool) $row->gibdd2_passport_address)
                    $message .= "*Адрес по паспорту* `{$row->gibdd2_passport_address}`\n";

                $messages[] = $message;
            });

        if ($count = count($rows) > 1) {
            $this->sendMessage([
                'chat_id' => $chat_id,
                'text' => "Найдено строк {$count}",
            ]);
        }

        foreach ($messages as $message) {
            $this->sendMessage([
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => "Markdown",
            ]);
        }

        return "Success";
    }
}
