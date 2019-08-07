<?php
/**
 * 合同框架
 */
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Framwork;
use App\Repositories\Contracts\FramworkRepository;
use Illuminate\Http\Request;
use App\Transformers\FramworkTransformer;

class FramworkController extends Controller
{
    /**
     * Instance of FramworkRepository
     *
     * @var FramworkRepository
     */
    private $FramworkRepository;

    /**
     * Instanceof FramworkTransformer
     *
     * @var FramworkTransformer
     */
    private $FramworkTransformer;

    /**
     * Constructor
     *
     * @param FramworkRepository $FramworkRepository
     * @param FramworkTransformer $FramworkTransformer
     */
    public function __construct(FramworkRepository $FramworkRepository, FramworkTransformer $FramworkTransformer)
    {
        $this->FramworkRepository = $FramworkRepository;
        $this->FramworkTransformer = $FramworkTransformer;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
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
        $Framworks = $this->FramworkRepository->findBy($request->all());
        $Framworks = $Framworks -> supplier();
        return $this->respondWithCollection($Framworks, $this->FramworkTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $Framwork = $this->FramworkRepository->findOne($id);

        if (!$Framwork instanceof Framwork) {
            return $this->sendNotFoundResponse("The Framwork with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $Framwork);

        return $this->respondWithItem($Framwork, $this->FramworkTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
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

        $Framwork = $this->FramworkRepository->save($request->all());

        if (!$Framwork instanceof Framwork) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Framwork');
        }

        return $this->setStatusCode(201)->respondWithItem($Framwork, $this->FramworkTransformer);
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

        $Framwork = $this->FramworkRepository->findOne($id);

        if (!$Framwork instanceof Framwork) {
            return $this->sendNotFoundResponse("The Framwork with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $Framwork);


        $Framwork = $this->FramworkRepository->update($Framwork, $request->all());

        return $this->respondWithItem($Framwork, $this->FramworkTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $Framwork = $this->FramworkRepository->findOne($id);

        if (!$Framwork instanceof Framwork) {
            return $this->sendNotFoundResponse("The Framwork with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('destroy', $Framwork);

        $this->FramworkRepository->delete($Framwork);

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
            'email'                 => 'email|required|unique:users',
            'firstName'             => 'required|max:100',
            'middleName'            => 'max:50',
            'lastName'              => 'required|max:100',
            'username'              => 'max:50',
            'address'               => 'max:255',
            'zipCode'               => 'max:10',
            'phone'                 => 'max:20',
            'mobile'                => 'max:20',
            'city'                  => 'max:100',
            'state'                 => 'max:100',
            'country'               => 'max:100',
            'password'              => 'min:5'
        ];

        $requestUser = $request->user();

        // Only admin user can set admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

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
            'email'                 => 'email|unique:users,email,'. $userId,
            'firstName'             => 'max:100',
            'middleName'            => 'max:50',
            'lastName'              => 'max:100',
            'username'              => 'max:50',
            'address'               => 'max:255',
            'zipCode'               => 'max:10',
            'phone'                 => 'max:20',
            'mobile'                => 'max:20',
            'city'                  => 'max:100',
            'state'                 => 'max:100',
            'country'               => 'max:100',
            'password'              => 'min:5'
        ];

        $requestUser = $request->user();

        // Only admin user can update admin role.
        if ($requestUser instanceof User && $requestUser->role === User::ADMIN_ROLE) {
            $rules['role'] = 'in:BASIC_USER,ADMIN_USER';
        } else {
            $rules['role'] = 'in:BASIC_USER';
        }

        return $rules;
    }
}