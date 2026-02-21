<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExternalDataService
{
    private const URL = 'https://bit.ly/48ejMhW';

    public function fetch(): array
    {
        $res = Http::timeout(10)->retry(2, 200)->get(self::URL);

        if (!$res->successful()) {
            throw new \RuntimeException('Failed fetching external data');
        }

        $body = trim($res->body());

        // JSON
        $json = json_decode($body, true);
        if (is_array($json)) {
            return (isset($json['data']) && is_array($json['data'])) ? $json['data'] : $json;
        }

        // CSV fallback
        $lines = preg_split("/\r\n|\n|\r/", $body);
        if (count($lines) >= 2) {
            $delim = str_contains($lines[0], ';') ? ';' : ',';
            $header = str_getcsv($lines[0], $delim);

            $rows = [];
            for ($i = 1; $i < count($lines); $i++) {
                if (trim($lines[$i]) === '') {
                    continue;
                }
                $cols = str_getcsv($lines[$i], $delim);
                $rows[] = array_combine($header, array_pad($cols, count($header), null));
            }

            // CSV fallback
            return $rows;
        }

        throw new \RuntimeException('Unknown external data format');
    }
}