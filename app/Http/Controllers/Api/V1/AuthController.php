<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\AuthTransformer;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Authorization;
use App\Models\User;
use App\Models\Staff;

class AuthController extends Controller
{
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

    /**
     * Constructor
     *
     * @param JWTAuth $jwt
     * @param AuthTransformer $authTransformer
     */
    public function __construct(JWTAuth $jwt, AuthTransformer $authTransformer) {

        $this->jwt = $jwt;
        $this->authTransformer = $authTransformer;

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

        $email = $request->input('email');
        $password = $request->input('password'); 

        // ldap 认证逻辑 fix me
        if (false) {
            return response()->json([ 'message' => '用户名或密码错误，登录失败。', 'status_code' => 401 ], 401);
        }

        // ldap 认证Ok后创建token
        if (!($user = User::where('email', $email)->first()) && !($user = Staff::where('email', $email)->first())) {
            return response()->json([ 'message' => '该用户不存在。', 'status_code' => 404 ], 404);
        }

        $token = $this->jwt->fromUser($user);

        return $this->setStatusCode(201)->respondWithItem(new Authorization($token), $this->authTransformer);
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
            'email'                  => 'required|email',
            'password'               => 'required',
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
