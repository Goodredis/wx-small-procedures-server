<?php

namespace App\Utils;

use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Excel class
 * @author wanggang
 * @version 1.0
 * @time 2019-08-21
 */
class Excel
{

	/**
	 * Excel 导入
     * Support Excel 2007、Excel 2003、Excel 97、Excel 95、Gnumeric、HTML、SYLK、CSV
     * Or browse the Official API documentation.
	 * @param   string   文件路径 
	 * @param   array    格式化keys
	 * @return  array
	 */
	public static function import($filePath, array $format_column) {
        
        // load import file
        $spreadsheet = IOFactory::load($filePath);
        // get sheet number
        $current_sheet   = $spreadsheet->getActiveSheet(); 

        $highestRow         = $current_sheet->getHighestRow();
        $highestColumn      = $current_sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); 

        $import_data = $column_data = [];
        $lines       = $highestRow - 1;
        if ($lines <= 0) {
            // var_dump('Excel表格中没有数据');exit;
            throw new Exception(trans('errorCode.110003'), 110003);
        }

        for ($col = 1; $col <= $highestColumnIndex; ++$col) //列数是以A列开始
        {
            $col_name = $current_sheet->getCellByColumnAndRow($col, 1)->getFormattedValue();
            if(in_array($col_name,array_keys($format_column))){
                $column_data[] = ['clo' => $col,'key' => $format_column[$col_name]];
            }
        }

        for ($row = 2; $row <= $highestRow; ++$row)
        {
            $row_data = [];
            foreach ($column_data as $item){
                switch ($item['key']) {
                    /*case "mobile" :
                        $content = intval($current_sheet->getCellByColumnAndRow($item['clo'], $row)->getCalculatedValue(true));
                        break;
                    case "tax":
                        $value = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getFormattedValue();
                        if(strpos($value, "%") > 0) $value = (float)$value/100;
                        $content = $value;
                        break; 
                    case "money":
                        $res  = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getFormattedValue();
                        $content =  str_replace(['￥', ','], '', $res);//一般先取出结果，然后自己处理比较方便。
                        break;
                    case "time":
                        $date = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getValue();
                        if(!$date){
                            $content = null;
                        }else{
                            $content = gmdate('Y-m-d', ($date - 25569) * 24 * 3600); //gmdate返回UTC的时间
                        }
                        break;*/
                    case "start_date":
                    case "end_date":
                        $date = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getValue();
                        if(!$date){
                            $content = null;
                        }else{
                            $content = strtotime(gmdate('Y-m-d', ($date - 25569) * 24 * 3600)); //gmdate返回UTC的时间
                        }
                        break;
                    default :
                        $content= trim($current_sheet->getCellByColumnAndRow($item['clo'], $row)->getFormattedValue());
                }
                $row_data[$item['key']] = $content;
            }

            if(count($row_data) != count($format_column)){
                throw new Exception(trans('errorCode.110004'), 110004);
            }
            //空数据过滤
            $import_data[] = $row_data;
        }
        return self::filterData($import_data);
    }

    /**
	 * Excel 导出
     * Support Excel 2007、HTML、CSV
     * Or browse the Official API documentation.
	 * @param   array   导出信息
	 * @param   array   格式化keys
	 * @param   string  导出的文件名称(可选)
	 * @return  output
	 */
    public static function export(array $export_data, array $format_column, $filename = '') {

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // set default style
        $spreadsheet->getDefaultStyle()->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
        // set default font
        $spreadsheet->getDefaultStyle()->getFont()->setName('宋体');
        // set default font size
        $spreadsheet->getDefaultStyle()->getFont()->setSize(12);

        $current_sheet = $spreadsheet->getActiveSheet();
        $current_sheet->setTitle('sheet');
        // set default width
        $current_sheet->getDefaultColumnDimension()->setWidth(20);
        // set default rowheight
        $current_sheet->getDefaultRowDimension()->setRowHeight(15);
        // $current_sheet->getColumnDimensionByColumn(2)->setWidth(100);

        $column_marks = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

        for ($col = 1; $col <= count($format_column); ++$col) {
            // set the first line to bold
            $current_sheet->getStyle('A1:'.$column_marks[count($format_column)-1].'1')->getFont()->setBold(true);
            $current_sheet->setCellValueByColumnAndRow($col, 1, $format_column[$col-1]);
        }
        foreach ($export_data as $i => $row) {
            $row = array_values($row);
            foreach ($row as $j => $v) {
                $current_sheet -> setCellValue($column_marks[$j].($i+2), $v);
            }
        }
    
        //文件名以及兼容性
        $filename = !empty($filename) ? $filename : date('YmdHis', time());
        if(preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT']) || preg_match("/Trident\/7.0/", $_SERVER['HTTP_USER_AGENT'])){
            $filename = urlencode($filename);
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');
         
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
         
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public static function filterData($import_data)
    {
        foreach ($import_data as $key => $item){
            $is_null = true;
            foreach ($item as $value) {
                if(!empty($value)) {
                    $is_null = false;break;
                }
            }
            if($is_null == true ) unset($import_data[$key]);
        }
        return array_values($import_data);
    }

}
