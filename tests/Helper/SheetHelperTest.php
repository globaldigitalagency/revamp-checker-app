<?php

namespace App\Tests\Helper;

use App\Helper\SheetHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\TestCase;

class SheetHelperTest extends TestCase
{
    private SheetHelper $sheetHelper;

    protected function setUp(): void
    {
        $this->sheetHelper = new SheetHelper();
    }

    public function testGetSheetDataThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('File not found or not readable: non_existent.xlsx');
        $this->sheetHelper->getSheetData('non_existent.xlsx');
    }

    public function testGetSheetDataReturnsExpectedData(): void
    {
        $filePath = '/var/www/var/test.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Test');
        $sheet->setCellValue('B1', 'Data');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $data = $this->sheetHelper->getSheetData($filePath);

        $this->assertIsArray($data);
        $this->assertEquals(['1' => ['A' => 'Test', 'B' => 'Data']], $data);
        $this->assertFileDoesNotExist($filePath);
    }
}