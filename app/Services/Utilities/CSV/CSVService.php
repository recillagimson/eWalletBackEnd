<?php


namespace App\Services\Utilities\CSV;

use PDF;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\Response;

class CSVService implements ICSVService
{

    public function __construct()
    {

    }

    //Based on https://codingdriver.com/laravel-8-export-csv-example.html
    public function generateCSV(array $datas, array $columns) {
        $datetimeNow = Carbon::now()->timestamp;
        $fileName = "admin_" . $datetimeNow . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($datas, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($datas as $data) {
                $row = [];
                for($i=0; $i<=sizeof($columns)-1; $i++) {
                    array_push($row, $data[$columns[$i]]);
                }
                fputcsv($file, $row);
                $row = [];
            }

            fclose($file);
        };

        return response()->stream($callback, Response::HTTP_OK, $headers);

    }
}
