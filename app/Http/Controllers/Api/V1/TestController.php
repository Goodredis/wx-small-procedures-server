<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\Contracts\TestRepository;
use Illuminate\Http\Request;
use App\Transformers\TestTransformer;
use App\Http\Controllers\Controller;
use App\Models\Test;

class TestController extends Controller
{
    /**
     * Instance of TestRepository
     *
     * @var UserRepository
     */
    private $testRepository;

    /**
     * Instanceof TestTransformer
     *
     * @var TestTransformer
     */
    private $testTransformer;

    /**
     * Constructor
     *
     * @param TestRepository $testRepository
     * @param TestTransformer $testTransformer
     */
    public function __construct(TestRepository $testRepository, TestTransformer $testTransformer) {
        $this->testRepository = $testRepository;
        $this->testTransformer = $testTransformer;

        parent::__construct();
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function index(Request $request) {
        $tests = $this->testRepository->findBy($request->all());
        return $this->respondWithCollection($tests, $this->testTransformer);
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

        $test = $this->userRepository->save($request->all());
        if (!$test instanceof Test) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }
        return $this->setStatusCode(201)->respondWithItem($test, $this->testTransformer);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'name'                 => 'max:100|required',
        ];

        return $rules;
     }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $test = $this->testRepository->findOne(intval($id));

        if (!$test instanceof Test) {
            return $this->sendNotFoundResponse("The test with id {$id} doesn't exist");
        }

        return $this->respondWithItem($test, $this->testTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function update(Request $request, $id) {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));
        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }
        $test = $this->testRepository->findOne($id);
        if (!$test instanceof Test) {
            return $this->sendNotFoundResponse("The test with id {$id} doesn't exist");
        }

        $test = $this->testRepository->update($test, $request->all());

        return $this->respondWithItem($user, $this->userTransformer);
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
            'name'                 => '',
        ];
        return $rules;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $test = $this->testRepository->findOne($id);
        if (!$test instanceof Test) {
            return $this->sendNotFoundResponse("The test with id {$id} doesn't exist");
        }
        $this->testRepository->delete($test);
        return response()->json(null, 204);
    }
}
