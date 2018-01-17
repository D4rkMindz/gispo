<?php

namespace App\Service;

class CSVGenerator
{
    /**
     * Generate CSV File
     *
     * @param array $actions
     * @return string $path Path to CSV file
     */
    public function generate(array $actions)
    {
        $directory = __DIR__ . '/../../resources/csv/';
        rrmdir($directory);
        mkdir($directory);
        $time = date('Y-m-d_H-i-s');
        $path = $directory . $time . '_export.csv';
        $handle = fopen($path, 'w+');

        $header = [
            'id',
            'first_name',
            'last_name',
            'name',
            'time',
        ];
        fputcsv($handle, $header, ';', '"', '\\');

        $actions = encode_iso($actions);

        foreach ($actions as $action) {
            fputcsv($handle, $action, ';', '"', '\\');
        }
        fclose($handle);

        return $path;
    }
}
