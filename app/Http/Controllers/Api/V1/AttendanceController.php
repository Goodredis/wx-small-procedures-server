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
    public function show($uid, $date) {
        $attendances = $this->attendanceRepository->getAttendancesByDate($uid, $date);
        return $this->respondWithArray([$uid => $attendances]);
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
     * @param $uid
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uid) {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $requestData = $request->all();
        $newAttendances = $this->attendanceRepository->arrangeUpdateCheckinat($requestData);

        if(!is_array($newAttendances)) {
            return $this->sendCustomResponse(400, 'Error occurred on creating Attendance');
        }

        foreach ($newAttendances as $key => $value) {
            $value['remark']   = isset($requestData['remark']) ? $requestData['remark'] : '';
            $value['position'] = isset($requestData['position']) ? $requestData['position'] : '';
            $value['source']   = isset($requestData['source']) ? $requestData['source'] : 2;
            $value['source_flag'] = 2;
            $attendance = $this->attendanceRepository->findOne($value['id']);

            if (!$attendance instanceof Attendance) {
                return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
            }

            $this->attendanceRepository->update($attendance, $value);
        }
        
        $attendances = $this->attendanceRepository->getAttendancesByDate($uid, $requestData['date']);
        return $this->respondWithArray([$uid => $attendances]);
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

    public function export(Request $request) {
        $export_data = $this->attendanceRepository->findBy($request->all());
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
            'position'              => 'string|max:255',
            'purpose'               => 'max:1',
            'check_in_at'           => 'max:11',
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
            'remark'                => 'max:255',
            'position'              => 'max:255',
            'purpose'               => 'max:1',
            'check_in'              => 'max:255',
            'check_out'             => 'max:255',
            'source'                => 'max:1',
            'source_flag'           => 'max:1',
        ];

        return $rules;
    }
}