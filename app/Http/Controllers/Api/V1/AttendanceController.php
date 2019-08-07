<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepository;
use App\Transformers\AttendanceTransformer;

class AttendanceController extends Controller
{
	/**
     * Instance of AttendanceRepository
     *
     * @var AttendanceRepository
     */
    private $attendanceRepository;

    /**
     * Instanceof AttendanceTransformer
     *
     * @var AttendanceTransformer
     */
    private $attendanceTransformer;

    /**
     * Constructor
     *
     * @param AttendanceRepository $attendanceRepository
     * @param AttendanceTransformer $attendanceTransformer
     */
    public function __construct(AttendanceRepository $attendanceRepository, AttendanceTransformer $attendanceTransformer) {
        $this->attendanceRepository = $attendanceRepository;
        $this->attendanceTransformer = $attendanceTransformer;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function index(Request $request) {
		$attendances = $this->attendanceRepository->findBy($request->all());
        return $this->respondWithCollection($attendances, $this->attendanceTransformer);
	}

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $attendance = $this->attendanceRepository->findOne($id);

        if (!$attendance instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
        }

        return $this->respondWithItem($attendance, $this->attendanceTransformer);
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

        $attendance = $this->attendanceRepository->save($request->all(), false);

        if (!$attendance instanceof Attendance) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Attendance');
        }

        return $this->setStatusCode(201)->respondWithItem($attendance, $this->attendanceTransformer);
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

        $attendance = $this->attendanceRepository->findOne($id);

        if (!$attendance instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
        }

        $attendance = $this->attendanceRepository->update($attendance, $request->all());

        return $this->respondWithItem($attendance, $this->attendanceTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $attendance = $this->attendanceRepository->findOne($id);

        if (!$attendance instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
        }

        $this->attendanceRepository->delete($attendance);

        return response()->json(null, 204);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request) {
        $rules = [
            'uid'                   => 'string|required|max:36',
            // 'uid'                   => 'uid|required|unique:users',
            'remark'                => '',
            'position'              => 'string|max:255',
            'purpose'               => 'integer|max:1',
            'check_in_at'           => 'max:11',
            'source'                => 'integer|max:1',
            'source_flag'           => 'integer|max:1',
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
            'uid'                   => '',
            'remark'                => '',
            'position'              => '',
            'purpose'               => '',
            'check_in_at'           => '',
            'source'                => '',
            'source_flag'           => '',
        ];

        return $rules;
    }
}