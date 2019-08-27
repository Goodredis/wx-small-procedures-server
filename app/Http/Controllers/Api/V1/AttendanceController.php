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
     * @param AttendanceviewTransformer $attendanceviewTransformer
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
        $requestData = $request->all();
        $output = 'json';
        if (isset($requestData['output']) && !empty($requestData['output'])) {
            $output = $requestData['output'];
            unset($requestData['output']);
        }
        $attendances = $this->attendanceviewRepository->getAttendanceviewList($requestData);
        if ($output == 'json') {
            return $this->respondWithCollection($attendances, $this->attendanceviewTransformer);
        } elseif ($output == 'excel') {
            $this->attendanceRepository->exportAttendances($attendances->toArray());
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
        $attendance = $this->attendanceRepository->getAttendanceItemById(intval($id));

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

        $attendance = $this->attendanceRepository->save($request->all());

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
        $attendance = $this->attendanceRepository->getAttendanceItemById($id);
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
        $attendance = $this->attendanceRepository->getAttendanceItemById($id);

        if (!$attendance instanceof Attendance) {
            return $this->sendNotFoundResponse("The attendance with id {$id} doesn't exist");
        }

        $this->attendanceRepository->delete($attendance);

        return response(null, 204);
    }

    public function batch(Request $request){
        $method = $request->post('method');
        $data = $request->post('data');
        if (empty($data) || !is_array($data)) {
            return $this->sendCustomResponse(400, 'Error bad parameter format on batch of Attendance');
        }
        switch ($method) { 
            case 'create':
                # code...
            case 'update':
                # code...
            case 'delete':
                $this->attendanceRepository->destroy(array_values($data));
                return response(null, 204);
            default:
                return $this->sendCustomResponse(500, 'Error This method is not supported on batch of Attendance');
        }
        exit;
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
            'remark'                => 'string|max:255',
            'position'              => 'string|max:255',
            'purpose'               => 'integer|required|in:1,2',
            'check_at'              => 'max:10',
            'source'                => 'integer|required|in:1,2,3',
            'source_flag'           => 'integer|required|in:1,2',
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
            'uid'                   => 'string|required|max:36',
            'remark'                => 'string|required|max:255',
            'position'              => 'string|max:255',
            'purpose'               => 'integer|required|in:1,2',
            'check_at'              => 'max:10',
            'source'                => 'integer|required|in:1,2,3',
            'source_flag'           => 'integer|required|in:1,2',
        ];
        return $rules;
    }
}