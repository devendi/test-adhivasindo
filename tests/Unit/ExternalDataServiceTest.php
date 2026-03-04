<?php

namespace Tests\Unit;

use App\Services\ExternalDataService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalDataServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        $cache = storage_path('app/external_data_cache.json');
        if (file_exists($cache)) {
            unlink($cache);
        }

        parent::tearDown();
    }

    public function test_it_parses_uppercase_data_pipe_format(): void
    {
        Http::fake([
            '*' => Http::response([
                'RC' => 200,
                'RCM' => 'OK',
                'DATA' => "NIM|NAMA|YMD\n9352078461|Turner Mia|20220713",
            ], 200),
        ]);

        $rows = app(ExternalDataService::class)->fetch();

        $this->assertCount(1, $rows);
        $this->assertSame('9352078461', $rows[0]['NIM']);
        $this->assertSame('Turner Mia', $rows[0]['NAMA']);
        $this->assertSame('20220713', $rows[0]['YMD']);
    }

    public function test_it_uses_cached_rows_when_source_times_out(): void
    {
        $cache = storage_path('app/external_data_cache.json');
        file_put_contents($cache, json_encode([
            ['NIM' => '9352078461', 'NAMA' => 'Turner Mia', 'YMD' => '20220713'],
        ]));

        Http::fake(function () {
            throw new \RuntimeException('timeout');
        });

        $rows = app(ExternalDataService::class)->fetch();

        $this->assertCount(1, $rows);
        $this->assertSame('Turner Mia', $rows[0]['NAMA']);
    }
}
