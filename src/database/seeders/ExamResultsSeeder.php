<?php

namespace Database\Seeders;

use App\Models\ExamResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamResultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //mở file csv
        $file_csv = fopen(database_path('data/diem_thi_thpt_2024.csv'),'r');


        fgetcsv($file_csv);

        while (($row = fgetcsv($file_csv, 1000, ',')) != false){
            ExamResult::create([
            'sbd'          => $row[0] ?: null,
            'toan'         => $row[1] ?: null,
            'ngu_van'      => $row[2] ?: null,
            'ngoai_ngu'    => $row[3] ?: null,
            'vat_li'       => $row[4] ?: null,
            'hoa_hoc'      => $row[5] ?: null,
            'sinh_hoc'     => $row[6] ?: null,
            'lich_su'      => $row[7] ?: null,
            'dia_li'       => $row[8] ?: null,
            'gdcd'         => $row[9] ?: null,
            'ma_ngoai_ngu' => $row[10] ?: null,
            ]);
        }
        fclose($file_csv);
    }
}
