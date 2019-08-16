<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepository;
use Illuminate\Http\Request;
use App\Transformers\SupplierTransformer;

class SupplierController extends Controller
{
    /**
     * Instance of SupplierRepository
     *
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * Instanceof SupplierTransformer
     *
     * @var SupplierTransformer
     */
    private $supplierTransformer;

    /**
     * Constructor
     *
     * @param SupplierRepository $supplierRepository
     * @param SupplierTransformer $supplierTransformer
     */
    public function __construct(SupplierRepository $supplierRepository, SupplierTransformer $supplierTransformer){
        $this->supplierRepository = $supplierRepository;
        $this->supplierTransformer = $supplierTransformer;

        parent::__construct();
    }

    /**
     * 获取厂商的信息，同时返回厂商的框架的信息
     * 默认有分页：
     * per_page 每页的页数默认为15
     * page 页数
     * @param Request $request
     * 可以检索的字段有：
     * name：厂商名称
     * code：厂商编号
     * status：厂商状态，1正常，2禁用
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
       $suppliers = $this->supplierRepository->findBy($request->all());
        return $this->respondWithCollection($suppliers, $this->supplierTransformer);
    }

    /**
     * 根据厂商表的id获取某一厂商的信息
     * @param str $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id){
        $supplier = $this->supplierRepository->findOne($id);
        if (!$supplier instanceof Supplier) {
            return $this->sendNotFoundResponse("The supplier with id {$id} doesn't exist");
        }

        return $this->respondWithItem($supplier, $this->supplierTransformer);
    }

    /**
     * 添加厂商信息，可同时添加框架详情信息
     * @param Request $request
     * array(
     *     'name' => xxx,
     *     'code' => xxx
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
        $supplier = $this->supplierRepository->save($request->all());

        if (!$supplier instanceof Supplier) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Supplier');
        }

        return $this->setStatusCode(201)->respondWithItem($supplier, $this->supplierTransformer);
    }

    /**
     * 修改厂商信息
     *
     * @param Request $request
     * array(
     *      'name' => xxx,
     *      'code' => xxx,
     *      'status'=> 1, //厂商状态，1正常，2禁用
     * )
     * @param $id,厂商表的id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        // 检查参数是否合法
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // 返回参数不合法错误
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $supplier = $this->supplierRepository->findOne($id);

        if (!$supplier instanceof Supplier) {
            return $this->sendNotFoundResponse("The supplier with id {$id} doesn't exist");
        }

        $supplier = $this->supplierRepository->update($supplier, $request->all());

        return $this->respondWithItem($supplier, $this->supplierTransformer);
    }

    public function destroy($id){
        return str_random('32');exit;
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
            'status'                => 'integer|in:1,2'
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
            'status'                => 'integer|in:1,2'
        ];
        return $rules;
    }
}