<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contractorder;
use App\Models\Contractorderquota;
use App\Repositories\Contracts\ContractorderRepository;
use App\Repositories\Contracts\ContractorderquotaRepository;
use App\Transformers\ContractorderTransformer;
use App\Transformers\ContractorderquotaTransformer;

class ContractorderController extends Controller
{
	/**
     * Instance of ContractorderRepository
     *
     * @var ContractorderRepository
     */
    private $contractorderRepository;

    /**
     * Instanceof ContractorderTransformer
     *
     * @var ContractorderTransformer
     */
    private $contractorderTransformer;

    /**
     * Instance of ContractorderquotaRepository
     *
     * @var ContractorderquotaRepository
     */
    private $contractorderquotaRepository;

    /**
     * Instanceof ContractorderquotaTransformer
     *
     * @var ContractorderquotaTransformer
     */
    private $contractorderquotaTransformer;

    /**
     * Constructor
     *
     * @param ContractorderRepository $contractorderRepository
     * @param ContractorderTransformer $contractorderTransformer
     * @param ContractorderquotaRepository $contractorderquotaRepository
     * @param ContractorderquotaTransformer $contractorderquotaTransformer
     */
    public function __construct(ContractorderRepository $contractorderRepository, ContractorderTransformer $contractorderTransformer, ContractorderquotaRepository $contractorderquotaRepository, ContractorderquotaTransformer $contractorderquotaTransformer) {
        $this->contractorderRepository = $contractorderRepository;
        $this->contractorderTransformer = $contractorderTransformer;
        $this->contractorderquotaRepository = $contractorderquotaRepository;
        $this->contractorderquotaTransformer = $contractorderquotaTransformer;
        
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function index(Request $request) {
        $requestData = $request->all();
        $output = 'json';
        if (isset($requestData['output']) && !empty($requestData['output'])) {
            $output = $requestData['output'];
            unset($requestData['output']);
        }
        $orders = $this->contractorderRepository->getContractOrderInfos($requestData);
        if ($output == 'json') {
            return $this->respondWithCollection($orders, $this->contractorderTransformer);
        } elseif ($output == 'excel') {
            # code...
        } else {
            return $this->sendCustomResponse(400, 'Error bad parameter format on batch of Attendance');
        }
	}

    /**
     * Display the specified rpesource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $order = $this->contractorderRepository->getContractOrderInfoById($id);

        if (!$order instanceof Contractorder) {
            return $this->sendNotFoundResponse("The contract order with id {$id} doesn't exist");
        }

        return $this->respondWithItem($order, $this->contractorderTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request) {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $order = $this->contractorderRepository->save($request->all());

        if (!$order instanceof Contractorder) {
            return $this->sendCustomResponse(500, 'Error occurred on creating order');
        }

        return $this->setStatusCode(201)->respondWithItem($order, $this->contractorderTransformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));
        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $order = $this->contractorderRepository->getContractOrderInfoById($id);
        if (!$order instanceof Contractorder) {
            return $this->sendNotFoundResponse("The contract order with id {$id} doesn't exist");
        }

        $order = $this->contractorderRepository->update($order, $request->all());
        return $this->respondWithItem($order, $this->contractorderTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id) {
        $order = $this->contractorderRepository->getContractOrderInfoById($id);

        if (!$order instanceof Contractorder) {
            return $this->sendNotFoundResponse("The contract order with id {$id} doesn't exist");
        }

        $this->contractorderRepository->delete($order);

        return response(null, 204);
    }

    public function getOrderManagers($id) {
        # code...
    }

    public function setOrderManagers($id, $managers) {
        # code...
    }

    /**
     * 获取合同订单项目记录
     * @param Request $request
     * @param string  $id
     * @return collection
     */
    public function getprojectsfromorder(Request $request, $id) {
        $quotas = $this->contractorderquotaRepository->getProjectsFromOrder($id, $request->all());
        return response()->json($quotas);
    }

    /**
     * 合同订单分配到项目
     * @param Request $request
     * @param string  $id
     * @return collection
     */
    public function assignordertoprojects(Request $request, $id) {
        // $requestData = array(
        //     array(
        //         'id'                 =>  30,
        //         'contract_order_id'  =>  'dda62df8-5cff-4574-be1f-e146a902f081',
        //         'signer'             =>  'signer1',
        //         'project_id'         =>  'sub1',
        //         'parent_project_id'  =>  '16632ea1-1758-4533-be27-19765fecefaa',
        //         'tax_ratio'          =>  '6',
        //         'price'              =>  '500000',
        //         'price_with_tax'     =>  '530000',
        //     ),
        //     array(
        //         'id'                 =>  31,
        //         'contract_order_id'  =>  'dda62df8-5cff-4574-be1f-e146a902f081',
        //         'signer'             =>  'signer2',
        //         'project_id'         =>  'sub2',
        //         'parent_project_id'  =>  '16632ea1-1758-4533-be27-19765fecefaa',
        //         'tax_ratio'          =>  '6',
        //         'price'              =>  '200000',
        //         'price_with_tax'     =>  '212000',
        //     ),
        //     array(
        //         'contract_order_id'  =>  'dda62df8-5cff-4574-be1f-e146a902f081',
        //         'signer'             =>  'signer3',
        //         'project_id'         =>  'sub3',
        //         'parent_project_id'  =>  '16632ea1-1758-4533-be27-19765fecefaa',
        //         'tax_ratio'          =>  '6',
        //         'price'              =>  '200000',
        //         'price_with_tax'     =>  '212000',
        //     ),
        // );
        $requestData = $request->all();
        $ret = $this->contractorderquotaRepository->assignOrderToProjects($id, $requestData);
        return isset($ret['err_code']) ? $this->sendCustomResponse(400, $ret['message']) : response()->json($ret);
    }

    /**
     * 导入合同订单
     * @param Request $request
     * 如果文件名带append则是增量导入
     */
    public function import(Request $request){
        $file = $request->file('file');
        $res = $this->contractorderRepository->importContractOrderInfo($file);
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
    private function storeRequestValidationRules(Request $request) {
        $rules = [
            'name'                  => 'string|required|max:255',
            'code'                  => 'string|required|max:64',
            'dept_id'               => 'string|required|max:64',
            'signer'                => 'string|required|max:64',
            'project_id'            => 'string|required|max:644',
            'start_date'            => 'string|required|max:11',
            'end_date'              => 'string|required|max:11',
            'tax_ratio'             => 'integer|required|max:100',
            'price'                 => 'string|required|max:64',
            'price_with_tax'        => 'string|required|max:64',
            'used_price'            => 'max:64',
            'framework_id'          => 'string|required|max:64',
        ];
        return $rules;
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function updateRequestValidationRules(Request $request) {
        $rules = [
            'name'                  => 'string|required|max:255',
            'code'                  => 'string|required|max:64',
            'dept_id'               => 'string|required|max:64',
            'signer'                => 'string|required|max:64',
            'project_id'            => 'string|required|max:644',
            'start_date'            => 'string|required|max:11',
            'end_date'              => 'string|required|max:11',
            'tax_ratio'             => 'integer|required|max:100',
            'price'                 => 'string|required|max:64',
            'price_with_tax'        => 'string|required|max:64',
            'used_price'            => 'max:64',
            'framework_id'          => 'string|required|max:64',
        ];
        return $rules;
    }

}