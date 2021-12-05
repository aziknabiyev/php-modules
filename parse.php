<?php
require_once('./simplehtmldom/simple_html_dom.php');
require_once('./vendor/phpoffice/phpexcel/Classes/PHPExcel.php');
require_once('./vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/Excel5.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');



// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Milli Qəhrəman')
            ->setCellValue('B1', 'Həyat dövrü');
     

// Miscellaneous glyphs, UTF-8
// $objPHPExcel->setActiveSheetIndex(0)
//             ->setCellValue('A4', 'Miscellaneous glyphs')
//             ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getColumnDimension('A')->setWidth(22);
$sheet->getColumnDimension('B')->setWidth(22);





$my=file_get_html('https://az.wikipedia.org/wiki/Az%C9%99rbaycan%C4%B1n_Milli_Q%C9%99hr%C9%99manlar%C4%B1n%C4%B1n_siyah%C4%B1s%C4%B1#M%C9%99hrum_edil%C9%99nl%C9%99r');
$n=0;

foreach($my->find('tr') as $element)
{
    if($element->find('img'))
    {
        $my_check=true;
        $v=0;
        $row_number=$n+2;
        $n++;

        foreach($element->find('td') as $k=>$el)
        {

           if($k==2)
           {
               $txt=$el->find('a');

               $string=$txt[0]->text();     
               $sheet->setCellValueByColumnAndRow(0,$row_number,$string);
               $v++;
          
           }
           if(isset($element->find('td')[5]) && $my_check && $k>2)
           {
            $my_check=false;

               $txt=$el->find('a');
               $string=$element->find('td')[5]->text();     
               $sheet->setCellValueByColumnAndRow(1,$row_number,$string);
               $v++;

               //echo ' '.$element->find('td')[5]->text() . '<br>';               
            }
           if($k==4 && $my_check)
           {
            $my_check=false;
            $string=$element->find('td')[4]->text();     
            $sheet->setCellValueByColumnAndRow(1,$row_number,$string);
            $v++;

               // echo ' '.$element->find('td')[4]->text() . '<br>';               
           }
        
      
        }
        $sheet->getStyleByColumnAndRow(0,$n)->getAlignment()->
        setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    }

}

   
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
// header ( "Cache-Control: no-cache, must-revalidate" );
// header ( "Pragma: no-cache" );
// header ( "Content-type: application/vnd.ms-excel" );
// header ( "Content-Disposition: attachment; filename=Milli Qəhrəmanlar.xls" );

// // Выводим содержимое файла
// $objWriter = new \PHPExcel_Writer_Excel5($xls);
// $objWriter->save('php://output');
?>