<?php
use Illuminate\Support\Facades\DB;

if (!function_exists('generateDocumentNumber')) {

    function generateDocumentNumber($table, $prefix)
    {
        $month = date('m');
        $year = date('Y');

        $romanMonths = [
            '01' => 'I',
            '02' => 'II',
            '03' => 'III',
            '04' => 'IV',
            '05' => 'V',
            '06' => 'VI',
            '07' => 'VII',
            '08' => 'VIII',
            '09' => 'IX',
            '10' => 'X',
            '11' => 'XI',
            '12' => 'XII'
        ];

        $romanMonth = $romanMonths[$month];

        $count = DB::table($table)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return $nextNumber . '/' . $prefix . '/' . $romanMonth . '/' . $year;
    }
}
