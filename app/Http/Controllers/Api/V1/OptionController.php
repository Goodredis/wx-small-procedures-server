<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Repositories\Contracts\OptionRepository;
use Illuminate\Http\Request;
use App\Transformers\OptionTransformer;
use League\Fractal\Manager;

class OptionController extends Controller
{
    /**
     * Instance of OptionRepository
     *
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * Instanceof OptionTransformer
     *
     * @var OptionTransformer
     */
    private $optionTransformer;

    /**
     * Constructor
     *
     * @param OptionRepository $optionRepository
     * @param OptionTransformer $optionTransformer
     */
    public function __construct(OptionRepository $optionRepository, OptionTransformer $optionTransformer){
        $this->optionRepository = $optionRepository;
        $this->optionTransformer = $optionTransformer;

        parent::__construct();
    }

    /**
     * 获取配置字典的信息
     * 默认有分页：
     * per_page 每页的页数默认为15
     * page 页数
     * @param Request $request
     * 可以检索的字段有：
     * key：配置的英文字符串
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
       $options = $this->optionRepository->findBy($request->all());
        return $this->respondWithCollection($options, $this->optionTransformer);
    }

    /**
     * 根据配置字典表的id获取某一字典的信息
     * @param str $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id){
        $option = $this->optionRepository->findOne($id);
        if (!$option instanceof Option) {
            return $this->sendNotFoundResponse("The option with id {$id} doesn't exist");
        }

        return $this->respondWithItem($option, $this->optionTransformer);
    }

    /**
     * 添加配置字典信息
     * @param Request $request
     * array(
     *     'key' => xxx,
     *     'value' => xxx，//多个值的时候用英文逗号，隔开
     *     'description' => xxx //配置字典说明
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
        $option = $this->optionRepository->save($request->all());

        if (!$option instanceof Option) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Option');
        }

        return $this->setStatusCode(201)->respondWithItem($option, $this->optionTransformer);
    }

    /**
     * 修改配置字典信息
     *
     * @param Request $request
     * array(
     *     'key' => xxx,
     *     'value' => xxx，//多个值的时候用英文逗号，隔开
     *     'description' => xxx //配置字典说明
     * )
     * @param $id,配置字典表的id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        // 检查参数是否合法
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // 返回参数不合法错误
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $option = $this->optionRepository->findOne($id);

        if (!$option instanceof Option) {
            return $this->sendNotFoundResponse("The option with id {$id} doesn't exist");
        }

        $option = $this->optionRepository->update($option, $request->all());

        return $this->respondWithItem($option, $this->optionTransformer);
    }

    /**
     * 删除配置字典
     *
     * @param str $id，配置字典表的id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id){
        $option = $this->optionRepository->findOne($id);

        if (!$option instanceof Option) {
            return $this->sendNotFoundResponse("The Option with id {$id} doesn't exist");
        }
        $this->optionRepository->delete($option);
        return response(null, 204);
    }

    /**
     * 批量操作配置字典
     *
     * @param Request $request
     * array(
     *     'method' => 'delete',//批量删除
     *     'data'=>array(
     *        'xxx','xxxx'//被删除的配置字典的id
     *     )
     *)
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function batch(Request $request){
        $params = $request->all();
        $method = $params['method'];
        $data = $params['data'];
        if(empty($data)){
            return response()->json(['status' => 400, 'message' => '参数错误'], 400);
        }
        switch ($method) {
            case 'delete'://批量删除
                $this->optionRepository->destroy($data);
                return response(null, 204);

            default:
                return response()->json(['status' => 400, 'message' => '参数错误'], 400);
        }
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request){
        $rules = [
            'key'                  => 'required|unique:option|string|max:64',
            'value'                => 'required|string|max:64',
            'description'          => 'string'
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
            'key'                  => 'required|string|max:64',
            'value'                => 'required|string|max:64',
            'description'          => 'string'
        ];
        return $rules;
    }
}