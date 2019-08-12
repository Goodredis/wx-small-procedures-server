<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

abstract class AbstractEloquentRepository implements BaseRepository
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Instance that extends Illuminate\Database\Eloquent\Model
     *
     * @var Model
     */
    protected $model;

    /**
     * get logged in user
     *
     * @var User $loggedInUser
     */
    protected $loggedInUser;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        //$this->loggedInUser = $this->getLoggedInUser();
    }

    /**
     * Get Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @inheritdoc
     */
    public function findOne($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public function findOneBy(array $criteria)
    {
        return $this->model->where($criteria)->first();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $searchCriteria = [], $operatorCriteria = [])
    {
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15; // it's needed for pagination
        $page = !empty($searchCriteria['page']) ? (int)$searchCriteria['page'] : 1; //默认为第一页

        $columns = ['*'];
        if(!empty($searchCriteria['columns'])) {
            $columns = explode(',', $searchCriteria['columns']);
            unset($searchCriteria['columns']);;
        }
        $orderby = '';
        if(!empty($searchCriteria['orderby'])) {
            $orderby = trim($searchCriteria['orderby']);
            unset($searchCriteria['orderby']);
        }

        $queryBuilder = $this->model->where(function ($query) use ($searchCriteria, $operatorCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria, $operatorCriteria);
        });

        $queryBuilder = $this->applyOrderCriteriaInQueryBuilder($queryBuilder, $orderby);

        return $queryBuilder->paginate($limit, $columns, 'page', $page);
    }


    /**
     * Apply condition on query builder based on search criteria
     *
     * @param Object $queryBuilder
     * @param array $searchCriteria
     * @return mixed
     */
    protected function applySearchCriteriaInQueryBuilder($queryBuilder, array $searchCriteria = [], array $operatorCriteria = [])
    {

        foreach ($searchCriteria as $key => $value) {

            //skip pagination related query params
            if (in_array($key, ['page', 'per_page'])) {
                continue;
            }

            //we can pass multiple params for a filter with commas
            $allValues = explode(',', $value);
            $betValues = explode('~', $value);

            if (count($allValues) > 1) {
                $queryBuilder->whereIn($key, $allValues);
            } elseif (count($betValues) > 1 && $operatorCriteria[$key] == 'between') {
                $queryBuilder->whereBetween($key, $betValues);
            } else {
                $operator = array_key_exists($key, $operatorCriteria) ? $operatorCriteria[$key] : '=';
                $queryBuilder->where($key, $operator, $value);
            }
        }

        return $queryBuilder;
    }

    protected function applyOrderCriteriaInQueryBuilder($queryBuilder, $orderby) {
        if (!empty($orderby)) {
            if (strrpos($orderby, ',') === false) {
                $tmp = explode(' ', trim($orderby));
                $field = count($tmp) > 1 ? current($tmp) : $orderby;
                $seqence = count($tmp) > 1 ? end($tmp) : 'ASC';
                $queryBuilder = $queryBuilder->orderBy($field, $seqence);
            } else {
                $sections = explode(',', trim($orderby));
                foreach ($sections as $section) {
                    $section = trim($section);
                    if ($section) {
                        $tmp = explode(' ', $section);
                        $queryBuilder = $queryBuilder->orderBy(trim($tmp[0]), isset($tmp[1]) ? $tmp[1] : 'ASC');
                    }
                }
            }
        }
        return $queryBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(array $data, $generateUidFlag = true)
    {
        // generate uid
        if($generateUidFlag === true) $data['id'] = Uuid::uuid4();

        return $this->model->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update(Model $model, array $data)
    {
        $fillAbleProperties = $this->model->getFillable();

        foreach ($data as $key => $value) {

            // update only fillAble properties
            if (in_array($key, $fillAbleProperties)) {
                $model->$key = $value;
            }
        }

        // update the model
        $model->save();

        // get updated model from database
        $model = $this->findOne($model->id);

        return $model;
    }

    /**
     * @inheritdoc
     */
    public function findIn($key, array $values)
    {
        return $this->model->whereIn($key, $values)->get();
    }

    /**
     * @inheritdoc
     */
    public function delete(Model $model){
        return $model->delete();
    }

    /**
     * 批量删除
     * @param array $ids，注意id必须是数组，即使只有一个元素也得是数组格式
    */
    public function destroy($ids){
        return $this->model->destroy($ids);
    }

    public function import($filePath, array $format_column) {
        
        // load import file
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        // get sheet number
        $current_sheet   = $spreadsheet->getActiveSheet(); 

        $highestRow         = $current_sheet->getHighestRow();
        $highestColumn      = $current_sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn); 

        $import_data = $column_data = [];
        $lines       = $highestRow - 2;
        if ($lines <= 0) {
            var_dump('Excel表格中没有数据');exit;
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
                        $row_data[$item['key']] = $value;
                        break; 
                    case "money":
                        $res  = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getFormattedValue();
                        $row_data[$item['key']] =  str_replace(['￥', ','], '', $res);//一般先取出结果，然后自己处理比较方便。
                        break;
                    case "time":
                        $date = $current_sheet->getCellByColumnAndRow($item['clo'], $row)->getValue();
                        if(!$date){
                            $row_data[$item['key']] = null;
                        }else{
                            $row_data[$item['key']] = gmdate('Y-m-d', ($date - 25569) * 24 * 3600); //gmdate返回UTC的时间
                        }
                        break;*/
                    default :
                        $content= trim($current_sheet->getCellByColumnAndRow($item['clo'], $row)->getFormattedValue());
                }
                $row_data[$item['key']] = $content;
            }
            if(count($row_data) != count($format_column))
                throw new ApiException(Code::EXCEL_FORMAT_ERROR);
            //空数据过滤
            $import_data[] = $row_data;
        }
        return $this -> filterData($import_data);
    }

    public function export(array $export_data, array $format_column, $filename = '') {

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

    /**
     * get loggedIn user
     *
     * @return User
     */
    protected function getLoggedInUser()
    {
        $user = \Auth::user();

        if ($user instanceof User) {
            return $user;
        } else {
            return new User();
        }
    }

    protected function filterData($import_data)
    {
        foreach ($import_data as $key => $item){
            $is_null = false;
            foreach ($item as $value) {
                if(!empty($value)) {
                    $is_null = true;break;
                }
            }
            if($is_null == false ) unset($import_data[$key]);
        }
        return array_values($import_data);
    }
}
