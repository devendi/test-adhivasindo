<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalDataService
{
    private const URL = 'https://ogienurdiana.com/career/ecc694ce4e7f6e45a5a7912cde9fe131';
    private const CACHE_FILE = 'app/external_data_cache.json';

    public function fetch(): array
    {
        try {
            $res = Http::connectTimeout(5)->timeout(20)->retry(2, 300)->get(self::URL);

            if (!$res->successful()) {
                throw new \RuntimeException('Failed fetching external data');
            }

            $rows = $this->parsePayload($res->body());
            $this->writeCache($rows);

            return $rows;
        } catch (\Throwable $e) {
            $cached = $this->readCache();
            if (!empty($cached)) {
                Log::warning('External source failed, using cached dataset', [
                    'error' => $e->getMessage(),
                ]);

                return $cached;
            }

            throw $e;
        }
    }

    private function parsePayload(string $body): array
    {
        $body = trim($body);

        // JSON
        $json = json_decode($body, true);
        if (is_array($json)) {
            if (isset($json['data']) && is_array($json['data'])) {
                return $json['data'];
            }

            if (isset($json['DATA']) && is_string($json['DATA'])) {
                return $this->parseDelimitedText($json['DATA']);
            }

            return [$json];
        }

        return $this->parseDelimitedText($body);
    }

    private function parseDelimitedText(string $text): array
    {
        $lines = preg_split("/\\r\\n|\\n|\\r/", trim($text));
        if (count($lines) < 2) {
            throw new \RuntimeException('Unknown external data format');
        }

        $headerLine = trim($lines[0]);
        $delim = str_contains($headerLine, '|')
            ? '|'
            : (str_contains($headerLine, ';') ? ';' : ',');

        $header = array_map('trim', str_getcsv($headerLine, $delim));
        $rows = [];

        for ($i = 1; $i < count($lines); $i++) {
            if (trim($lines[$i]) === '') {
                continue;
            }

            // CSV fallback
            return $rows;
        }

        throw new \RuntimeException('Unknown external data format');
    }
}