<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contractorder;
use App\Repositories\Contracts\ContractorderRepository;
use App\Transformers\ContractorderTransformer;

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
     * Constructor
     *
     * @param ContractorderRepository $contractorderRepository
     * @param ContractorderTransformer $contractorderTransformer
     */
    public function __construct(ContractorderRepository $contractorderRepository, ContractorderTransformer $contractorderTransformer) {
        $this->contractorderRepository = $contractorderRepository;
        $this->contractorderTransformer = $contractorderTransformer;
        
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
        $orders = $this->contractorderRepository->getContractOrderInfos($requestData);
        $output = isset($requestData['output']) ? $requestData['output'] : 'json';
        if ($output == 'json') {
            return $this->respondWithCollection($orders, $this->contractorderTransformer);
        } elseif ($output == 'excel') {
            $this->contractorderRepository->exportContractorders($orders->toArray());
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

}