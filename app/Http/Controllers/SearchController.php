<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExternalDataService;

class SearchController extends Controller
{
    public function __construct(private ExternalDataService $svc) {}

    public function byName(Request $request)
    {
        $v = $request->validate(['value' => ['required','string']])['value'];
        return $this->search('nama', $v, true);
    }

    public function byNim(Request $request)
    {
        $v = $request->validate(['value' => ['required','string']])['value'];
        return $this->search('nim', $v, false);
    }

    public function byYmd(Request $request)
    {
        $v = $request->validate(['value' => ['required','string']])['value'];
        return $this->search('ymd', $v, false);
    }

    private function search(string $key, string $value, bool $ci)
    {
        $rows = $this->svc->fetch();

        $matches = array_values(array_filter($rows, function ($row) use ($key, $value, $ci) {
            if (!is_array($row)) return false;

            $found = null;
            foreach ($row as $k => $v) {
                if (mb_strtolower((string)$k) === mb_strtolower($key)) { $found = $v; break; }
            }
            if ($found === null) return false;

            $a = (string)$found;
            $b = (string)$value;

            return $ci ? (mb_strtolower($a) === mb_strtolower($b)) : ($a === $b);
        }));

        return response()->json([
            'query_key' => $key,
            'query_value' => $value,
            'count' => count($matches),
            'data' => $matches,
        ]);
    }
}