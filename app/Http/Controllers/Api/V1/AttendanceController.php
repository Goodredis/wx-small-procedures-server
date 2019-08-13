<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Attendanceview;
use App\Repositories\Contracts\AttendanceRepository;
use App\Repositories\Contracts\AttendanceviewRepository;
use App\Transformers\AttendanceTransformer;
use App\Transformers\AttendanceviewTransformer;

class AttendanceController extends Controller
{
	/**
     * Instance of AttendanceRepository
     *
     * @var AttendanceRepository
     */
    private $attendanceRepository;

    /**
     * Instance of AttendanceviewRepository
     *
     * @var AttendanceviewRepository
     */
    private $attendanceviewRepository;


    /**
     * Instanceof AttendanceTransformer
     *
     * @var AttendanceTransformer
     */
    private $attendanceTransformer;

    /**
     * Instanceof AttendanceviewTransformer
     *
     * @var AttendanceviewTransformer
     */
    private $attendanceviewTransformer;

    /**
     * Constructor
     *
     * @param AttendanceRepository $attendanceRepository
     * @param AttendanceviewRepository $attendanceviewRepository
     * @param AttendanceTransformer $attendanceTransformer
     */
    public function __construct(AttendanceRepository $attendanceRepository, AttendanceviewRepository $attendanceviewRepository, AttendanceTransformer $attendanceTransformer, AttendanceviewTransformer $attendanceviewTransformer) {
        $this->attendanceRepository = $attendanceRepository;
        $this->attendanceviewRepository = $attendanceviewRepository;
        $this->attendanceTransformer = $attendanceTransformer;
        $this->attendanceviewTransformer = $attendanceviewTransformer;
        
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
     * Display the specified rpesource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $attendances = $this->attendanceRepository->findOne(intval($id));

        if (!$attendances instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendances with id {$id} doesn't exist");
        }

        return $this->respondWithItem($attendances, $this->attendanceTransformer);
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
    public function destroy($id) {
        $attendance = $this->attendanceRepository->findOne($id);

        if (!$attendance instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
        }

        $this->attendanceRepository->delete($attendance);

        return response()->json(null, 204);
    }

    public function list(Request $request) {
        $lists = $this->attendanceviewRepository->findBy($request->all());
        return $this->respondWithCollection($lists, $this->attendanceviewTransformer);
    }

    public function export(Request $request) {
        $export_data = $this->attendanceviewRepository->findBy($request->all());
        $attendances = $this->attendanceRepository->exportAttendance($export_data->toArray());
        return $this->respondWithCollection($attendances, $this->attendanceTransformer);
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
            'position'              => 'max:255',
            'purpose'               => 'max:1',
            'workdate'              => 'max:8',
            'check_at'              => 'max:11',
            'source'                => 'max:1',
            'source_flag'           => 'max:1',
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
            // 'uid'                   => 'uid|required|unique:users',
            'remark'                => 'max:255',
            'position'              => 'max:255',
            'purpose'               => 'max:1',
            'workdate'              => '',
            'check_at'              => 'max:11',
            'source'                => 'max:1',
            'source_flag'           => 'max:1',
        ];

        return $rules;
    }
}