<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\WebdiskTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\AuthTransformer;
use App\Transformers\UserTransformer;
use App\Repositories\Contracts\UserRepository;
use Ixudra\Curl\Facades\Curl;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Authorization;
use App\Models\User;


class AuthController extends Controller
{
    use WebdiskTrait;
    /**
     * Instanceof JWTAuth 
     *
     * @var jwt 
     */
    protected $jwt;

    /**
     * Instanceof FrameworkTransformer
     *
     * @var FrameworkTransformer
     */
    private $authTransformer;
    private $userRepository;
    private $userTransformer;
    /**
     * Constructor
     *
     * @param JWTAuth $jwt
     * @param AuthTransformer $authTransformer
     */
    public function __construct(JWTAuth $jwt, AuthTransformer $authTransformer, UserRepository $userRepository, UserTransformer $userTransformer) {

        $this->jwt = $jwt;
        $this->authTransformer = $authTransformer;
        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;

        parent::__construct();
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
        // 访问微信接口 获取 session_key openid
        $wx_data = ['appid' => $request->input('appid'),
            'secret'  => $request->input('secret'),
            'js_code' => $request->input('code'),
            'grant_type' => 'authorization_code',
        ];
        $result = $this -> curl_get('https://api.weixin.qq.com/sns/jscode2session', $wx_data, '10.2.3.63', '3128');
        $model_data = json_decode($result, true);
        if (isset($model_data['errcode'])) {
            return $this->sendInvalidFieldResponse('user create fail!');
        }

        if (!(User::where('openid', $model_data['openid'])->first())) {
            $user = $this->userRepository->save($model_data);
        } else {
            $user = $this->userRepository->findOne($model_data['openid']);
            $user = $this->userRepository->update($user, $model_data);
        }

        if (!$user instanceof User) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }

        if (!$user instanceof User) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }

        //  认证Ok后创建token
        if (!($user = User::where('openid', $model_data['openid'])->first())) {
            return response()->json([ 'message' => '该用户不存在。', 'status_code' => 404 ], 404);
        }
//        echo '<pre>';var_dump($user);exit;
        $token = $this->jwt->fromUser($user);
//        echo '<pre>';var_dump($this->authTransformer);exit;
        return $this->setStatusCode(201)->respondWithItem(new Authorization($token), $this->authTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $user = $this->userRepository->findOne(intval($id));

        if (!$user instanceof User) {
            return $this->sendNotFoundResponse("The user with id {$id} doesn't exist");
        }

        return $this->respondWithItem($user, $this->userTransformer);
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
            'code'         => 'required',
            'appid'        => 'required',
            'secret'       => 'required',
        ];

        return $rules;
    }

    /**
     * refresh the token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function update(Request $request)
    {
        $authorization = new Authorization($this->jwt->parseToken()->refresh());
        return $this->respondWithItem($authorization, $this->authTransformer);
    }

    /**
     *  the token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function delete($id)
    {
        $authorization = new Authorization(\Auth::refresh());
        return $this->response->item($authorization, $this->authTransformer);
    }
}
