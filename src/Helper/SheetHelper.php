<?php

namespace App\Helper;

use PhpOffice\PhpSpreadsheet\IOFactory;

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
            ranges: 'A1:'.$highestColumn.$highestRow,
            returnCellRef: true
        );

        unlink($filePath);

        return $data;
    }
}
