<?php
/**
 * 合同框架详情
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Framework;
use App\Models\Frameworkdetails;
use App\Repositories\Contracts\FrameworkRepository;
use App\Repositories\Contracts\FrameworkdetailsRepository;
use Illuminate\Http\Request;
use App\Transformers\FrameworkdetailsTransformer;

class FrameworkdetailsController extends Controller
{
    private $frameworkdetailsRepository;
    private $frameworkdetailsTransformer;

    /**
     * Constructor
     *
     * @param FrameworkdetailsRepository $frameworkdetailsRepository
     * @param FrameworkdetailsTransformer $frameworkdetailsTransformer
     */
    public function __construct(FrameworkdetailsRepository $frameworkdetailsRepository, FrameworkRepository $frameworkRepository,FrameworkdetailsTransformer $frameworkdetailsTransformer)
    {
        $this->frameworkdetailsRepository = $frameworkdetailsRepository;
        $this->frameworkRepository = $frameworkRepository;
        $this->frameworkdetailsTransformer = $frameworkdetailsTransformer;

        parent::__construct();
    }

    /**
     * 获取合同框架详情信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        $frameworkdetails = $this->frameworkdetailsRepository->findBy($request->all());
        return $this->respondWithCollection($frameworkdetails, $this->frameworkdetailsTransformer);
    }

    /**
     * 获取某一合同框架的详情
     *
     * @param $framework_id，合同框架详情表的framework_id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show(Request $request,$framework_id){
        $frameworkdetails = $this->frameworkdetailsRepository->findBy(['framework_id' => $framework_id]);
        return $this->respondWithCollection($frameworkdetails, $this->frameworkdetailsTransformer);
    }

    /**
     * 新建某一合同框架的详情
     *
     * @param Request $request
     * array(
     *     0 => array(
     *         'framework_id' => xxx
     *         "tax_ratio"=>0.93,
     *         "price"=>55555,
     *         "price_with_tax"=>33333,
     *         "level"=>3,
     *         "type"=>2
     *      )
     * )
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request){
        $data = $request->all();

        //先检查详情所属的框架是否存在
        $framework_id = $data[0]['framework_id'];
        $framework = $this->frameworkRepository->findOne($framework_id);
        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("合同框架不存在，不能添加详情信息");
        }

        //循环检查、添加每一项详情
        foreach ($data as $key => $value) {
            $this->frameworkdetailsRepository->save($value);
        }

        //查询出合同框架的详情信息，然后返回
        $frameworkdetails = $this->frameworkdetailsRepository->findBy(['framework_id' => $framework_id]);
        return $this->setStatusCode(201)->respondWithCollection($frameworkdetails, $this->frameworkdetailsTransformer);
    }

    /**
     * 更新某一个合同框架的详情信息
     * array(
     *     0 => array(
     *         'id' => xxx,
     *         'framework_id' => xxx
     *         "tax_ratio"=>0.93,
     *         "price"=>55555,
     *         "price_with_tax"=>33333,
     *         "level"=>3,
     *         "type"=>2
     *      )
     * )
     * @param Request $request
     *
     * @param $framework_id，合同框架详情表的framework_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $framework_id)
    {
        //检查详情所属的合同框架是否存在
        $framework = $this->frameworkRepository->findOne($framework_id);
        if (!$framework instanceof Framework) {
            return $this->sendNotFoundResponse("合同框架不存在，不能修改详情信息");
        }

        $data = $request->all();

        //循环修改每一条详情信息
        foreach ($data as $key => $value) {
            $rameworkdetails = $this->frameworkdetailsRepository->findOne($value['id']);

            if (!$rameworkdetails instanceof Frameworkdetails) {
                return $this->sendNotFoundResponse("The frameworkdetails with id {$value['id']} doesn't exist");
            }

            $this->frameworkdetailsRepository->update($rameworkdetails, $value);
        }

        //查询出合同框架的详情信息，然后返回
        $frameworkdetails = $this->frameworkdetailsRepository->findBy(['framework_id' => $framework_id]);
        return $this->respondWithCollection($frameworkdetails, $this->frameworkdetailsTransformer);

    }

    /**
     * 删除某一条详情信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $frameworkdetails = $this->frameworkdetailsRepository->findOne($id);

        if (!$frameworkdetails instanceof Frameworkdetails) {
            return $this->sendNotFoundResponse("The Frameworkdetails with id {$id} doesn't exist");
        }
        //传false是为了做逻辑删除,即将del_flag位置为1
        $this->frameworkdetailsRepository->delete($frameworkdetails);
        return response()->json(null, 204);
    }

    /**
     * 导入合同框架的详情信息
     * @param Request $request
     * 如果文件名带append则是增量导入
     */
    public function import(Request $request){
        $file = $request->file('file');
        $res = $this->frameworkdetailsRepository->importDetailInfo($file);
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
    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'framework_id' => 'required|max:64',
            'type'         => 'integer|in:1,2',
            'level'        => 'integer|in:1,2,3'
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
        $userId = $request->segment(2);
        $rules = [
            'type'         => 'integer|in:1,2',
            'level'        => 'integer|in:1,2,3'
        ];
        return $rules;
    }
}