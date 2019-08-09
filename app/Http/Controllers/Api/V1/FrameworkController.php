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
    public function __construct(FrameworkRepository $frameworkRepository, FrameworkTransformer $frameworkTransformer)
    {
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
     * supplier: 供应商code
     * status：合同框架状态，1执行中，2已完成
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $frameworks = $this->frameworkRepository->findBy($request->all());
        return $this->respondWithCollection($frameworks, $this->frameworkTransformer);
    }

    /**
     * 根据合同框架表的id获取某一合同框架的信息
     * @param str $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request,$id)
    {
        $framework = $this->frameworkRepository->findOne($id);
        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("The framework with id {$id} doesn't exist");
        }

        return $this->respondWithItem($framework, $this->frameworkTransformer);
    }

    /**
     * 添加合同框架信息，可同时添加框架详情信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        // Send failed response if validation fails
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // Send failed response if validation fails
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
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $framework = $this->FrameworkRepository->findOne($id);

        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("The Framework with id {$id} doesn't exist");
        }

        $this->FrameworkRepository->delete($framework);

        return response()->json(null, 204);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'name'                  => 'required|max:255',
            'code'                  => 'required|max:64',
            'start_date'            => 'date',
            'end_date'              => 'date',
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
    private function updateRequestValidationRules(Request $request)
    {
        $rules = [
            'name'                  => 'max:255',
            'code'                  => 'max:64',
            'start_date'            => 'date',
            'end_date'              => 'date',
            'type'                  => 'integer|in:1,2',
            'supplier_code'         => 'max:64',
            'frameworkdetails'      => 'array'
        ];
        
        return $rules;
    }
}