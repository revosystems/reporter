<?php

namespace BadChoice\Reports\Exporters;

use BadChoice\Reports\Exporters\BaseExporter2;
use Maatwebsite\Excel\Facades\Excel;

class XlsExporter extends BaseExporter {

    private $file;
    private $excel;

    public function download($title){
        return $this->excel->setFilename($title)->download('xlsx');
    }

    public function init(){
        $name = str_random(25);
        $this->file = Excel::create( $name, function($excel) {
            $excel->sheet('report', function($sheet) {});
        })->store('xls', false, true);
    }

    public function finalize(){
        unlink( $this->file["full"] );
    }

    public function generate(){
        $this->excel = Excel::load($this->file["full"], function($excel){
            $excel->sheet('report', function($sheet){
                $this->writeHeader($sheet);
                $rowPointer = 2;
                $this->forEachRecord( function($newRow) use($sheet, &$rowPointer) {
                    $this->writeRecordToSheet($rowPointer, $newRow, $sheet);
                    $rowPointer++;
                });
            });
        });
    }

    private function writeHeader($sheet){
        $letter = "A";
        foreach($this->getExportFields() as $field){
            $sheet->setCellValue($letter++ . 1, $field->getTitle() );
        }
    }

    private function writeRecordToSheet($rowPointer, $record, $sheet){
        $letter = "A";
        foreach($this->getExportFields() as $field){
            $sheet->setCellValue($letter++ . $rowPointer, $field->getValue( $record ) );
        }
    }

    protected function getType(){
        return "csv";
    }
}