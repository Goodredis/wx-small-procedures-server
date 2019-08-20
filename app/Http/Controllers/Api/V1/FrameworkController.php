<?php
/**
 * 合同框架
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Framework;
use App\Repositories\Contracts\FrameworkRepository;
use Illuminate\Http\Request;
use App\Transformers\FrameworkTransformer;

class FrameworkController extends Controller
{
    /**
     * Instance of FrameworkRepository
     *
     * @var FrameworkRepository
     */
    private $frameworkRepository;

    /**
     * Instanceof FrameworkTransformer
     *
     * @var FrameworkTransformer
     */
    private $frameworkTransformer;

    /**
     * Constructor
     *
     * @param FrameworkRepository $frameworkRepository
     * @param FrameworkTransformer $frameworkTransformer
     */
    public function __construct(FrameworkRepository $frameworkRepository, FrameworkTransformer $frameworkTransformer){
        $this->frameworkRepository = $frameworkRepository;
        $this->frameworkTransformer = $frameworkTransformer;

        parent::__construct();
    }

    /**
     * 获取合同框架信息列表，同时返回了合同框架的详情信息
     * 默认有分页：
     * per_page 每页的页数默认为15
     * page 页数
     * @param Request $request
     * 可以检索的字段有：
     * name：名称
     * code：合同框架编号
     * start_date：开始日期，例如2019-08-07
     * end_date：结束日期，例如2019-08-07
     * type：合同框架类型，1开发，2测试
     * supplier_code: 供应商code
     * status：合同框架状态，1执行中，2已完成
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $frameworks = $this->frameworkRepository->findBy($request->all());
        return $this->respondWithCollection($frameworks, $this->frameworkTransformer);
    }

    /**
     * 根据合同框架表的id获取某一合同框架的信息
     * @param str $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request,$id){
        $framework = $this->frameworkRepository->findOne($id);
        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("The framework with id {$id} doesn't exist");
        }

        return $this->respondWithItem($framework, $this->frameworkTransformer);
    }

    /**
     * 添加合同框架信息，可同时添加框架详情信息
     * @param Request $request
     * array(
     *     'name' => xxx,
     *     'code' => xxx,
     *      'start_date' => 2019-08-07,
     *      'end_data' => 2020-08-07,
     *      'type' => 1,//合同框架类型，1开发，2测试
     *      'supplier_code' => xxxx.//供应商code
     *      'status'=> 1, //合同框架状态，1执行中，2已完成
     *      'frameworkdetails' => array( //如果传了此参数就是有详情一起添加
     *           0 => array(
     *              "tax_ratio"=>0.93,
     *              "price"=>55555,
     *              "price_with_tax"=>33333,
     *              "level"=>3,
     *              "type"=>2
     *          )
     *       )
     * )
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request){
        // 检查参数是否合法
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        // 返回参数不合法错误
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }
        $framework = $this->frameworkRepository->save($request->all());

        if (!$framework instanceof Framework) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Framework');
        }

        return $this->setStatusCode(201)->respondWithItem($framework, $this->frameworkTransformer);
    }

    /**
     * 修改合同框架信息
     *
     * @param Request $request
     * array(
     *      'name' => xxx,
     *      'code' => xxx,
     *      'start_date' => 2019-08-07,
     *      'end_data' => 2020-08-07,
     *      'type' => 1,//合同框架类型，1开发，2测试
     *      'supplier_code' => xxxx.//供应商code
     *      'status'=> 1, //合同框架状态，1执行中，2已完成
     *      'frameworkdetails' => array( //如果传了此参数就是有详情一起添加
     *           0 => array(
     *              "tax_ratio"=>0.93,
     *              "price"=>55555,
     *              "price_with_tax"=>33333,
     *              "level"=>3,
     *              "type"=>2
     *          )
     *       )
     * )
     * @param $id,合同框架表的id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        // 检查参数是否合法
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // 返回参数不合法错误
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $framework = $this->frameworkRepository->findOne($id);

        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("The framework with id {$id} doesn't exist");
        }

        $framework = $this->frameworkRepository->update($framework, $request->all());

        return $this->respondWithItem($framework, $this->frameworkTransformer);
    }

    /**
     * 删除合同框架
     *
     * @param str $id，合同框架表的id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id){
        $framework = $this->frameworkRepository->findOne($id);

        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("The Framework with id {$id} doesn't exist");
        }
        $this->frameworkRepository->delete($framework);
        return response()->json(null, 204);
    }

    /**
     * 批量操作合同框架
     *
     * @param Request $request
     * array(
     *     'method' => 'delete',//批量删除
     *     'data'=>array(
     *        'xxx','xxxx'//被删除的合同框架的id
     *     )
     *)
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function batch(Request $request){
        $params = $request->all();
        $method = $params['method'];
        $data = $params['data'];
        switch ($method) {
            case 'delete'://批量删除
                if(!empty($data)){
                    $this->frameworkRepository->destroy($data);
                }
                return response()->json(null, 204);

            default:
                return response()->json(['status' => 404, 'message' => '参数错误'], 404);
        }
    }

    /**
     * 导入合同框架信息
     * @param Request $request
     * 如果文件名带append则是增量导入
     */
    public function import(Request $request){
        $file = $request->file('file');
        $res = $this->frameworkRepository->importFrameworkBasicInfo($file);
        if(isset($res['err_code'])){
            $res['message'] = trans('errorCode.' . $res['err_code']);
            return response()->json($res, 415);
        }
        return response()->json(['result'=>'ok']);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request){
        $rules = [
            'name'                  => 'required|max:255',
            'code'                  => 'required|max:64',
            'tax_ratio'             => 'required',
            'type'                  => 'integer|in:1,2',
            'supplier_code'         => 'required|max:64',
            'frameworkdetails'      => 'array'
        ];

        return $rules;
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function updateRequestValidationRules(Request $request){
        $rules = [
            'name'                  => 'max:255',
            'code'                  => 'max:64',
            'type'                  => 'integer|in:1,2',
            'supplier_code'         => 'max:64',
            'frameworkdetails'      => 'array'
        ];
        
        return $rules;
    }
}