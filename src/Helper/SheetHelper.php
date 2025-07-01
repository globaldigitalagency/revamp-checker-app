<?php

namespace App\Helper;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class SheetHelper
{
    public function getSheetData(string $filePath): array
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \RuntimeException("File not found or not readable: $filePath");
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumn = $worksheet->getHighestDataColumn();

        $data = $worksheet->rangesToArray(
            'A1:'.$highestColumn.$highestRow,
            null,
            true,
            true,
            true
        );

        unlink($filePath);

        return $data;
    }
}