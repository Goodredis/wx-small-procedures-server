<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dept;
use App\Repositories\Contracts\DeptRepository;
use Illuminate\Http\Request;
use App\Transformers\DeptTransformer;

class DeptController extends Controller
{
    /**
     * Instance of DeptRepository
     *
     * @var DeptRepository
     */
    private $deptRepository;

    /**
     * Instanceof DeptTransformer
     *
     * @var DeptTransformer
     */
    private $deptTransformer;

    /**
     * Constructor
     *
     * @param DeptRepository $deptRepository
     * @param DeptTransformer $deptTransformer
     */
    public function __construct(DeptRepository $deptRepository, DeptTransformer $deptTransformer){
        $this->deptRepository = $deptRepository;
        $this->deptTransformer = $deptTransformer;

        parent::__construct();
    }

    /**
     * 获取院内部所的信息
     * 默认有分页：
     * per_page 每页的页数默认为15
     * page 页数
     * @param Request $request
     * 可以检索的字段有：
     * name：部所的名称
     * department_id：部所的id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
       $depts = $this->deptRepository->findBy($request->all());
        return $this->respondWithCollection($depts, $this->deptTransformer);
    }

    /**
     * 根据部所表的id获取某一厂商的信息
     * @param str $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id){
        $dept = $this->deptRepository->findOne($id);
        if (!$dept instanceof Dept) {
            return $this->sendNotFoundResponse("The dept with id {$id} doesn't exist");
        }

        return $this->respondWithItem($dept, $this->deptTransformer);
    }

    /**
     * 导入厂商信息
     * @param Request $request
     * 如果文件名带append则是增量导入
     */
    public function import(Request $request){
        $file = $request->file('file');
        $res = $this->deptRepository->importDeptInfo($file);
        if(isset($res['err_code'])){
            $res['message'] = trans('errorCode.' . $res['err_code']);
            return response()->json($res, 415);
        }
        return response()->json(['result'=>'ok']);
    }

    /**
     * @brief 获取厂商的字典，只包含简单的信息id，name，code
     * @param string name 模糊查询厂商名
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getDeptDictionary(Request $request){
        $params = $request->all();
        $name = isset($params['name']) & !empty($params['name']) ? $params['name'] : '';
        $depts = $this->deptRepository->getDeptDictionary($name);
        return $this->respondWithArray($depts);
    }
}