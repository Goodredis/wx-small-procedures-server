<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Attendanceview;
use App\Repositories\Contracts\StaffRepository;
use App\Repositories\Contracts\AttendanceviewRepository;
use App\Transformers\StaffTransformer;
use App\Transformers\AttendanceviewTransformer;

class StaffController extends Controller
{
	/**
     * Instance of StaffRepository
     *
     * @var StaffRepository
     */
    private $staffRepository;

    /**
     * Instanceof StaffTransformer
     *
     * @var StaffTransformer
     */
    private $staffTransformer;

    /**
     * Instance of AttendanceviewRepository
     *
     * @var AttendanceviewRepository
     */
    private $attendanceviewRepository;

    /**
     * Instanceof AttendanceviewTransformer
     *
     * @var AttendanceviewTransformer
     */
    private $attendanceviewTransformer;

    /**
     * Constructor
     *
     * @param StaffRepository $staffRepository
     * @param StaffTransformer $staffTransformer
     * @param AttendanceviewRepository $attendanceviewRepository
     * @param AttendanceviewTransformer $attendanceviewTransformer
     */
    public function __construct(StaffRepository $staffRepository, AttendanceviewRepository $attendanceviewRepository, StaffTransformer $staffTransformer, AttendanceviewTransformer $attendanceviewTransformer) {
        $this->staffRepository = $staffRepository;
        $this->staffTransformer = $staffTransformer;
        $this->attendanceviewRepository = $attendanceviewRepository;
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
        $queryString = $request->all();
        $staffs = $this->staffRepository->findBy($queryString);
        $output = isset($queryString['output']) ? $queryString['output'] : 'json';
        if ($output == 'json') {
            return $this->respondWithCollection($staffs, $this->staffTransformer);
        } elseif ($output == 'excel') {
            $this->staffRepository->exportAttendance($staffs->toArray());
        }
	}

    /**
     * Display the specified rpesource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id) {
        $staff = $this->staffRepository->findOne(intval($id));

        if (!$staff instanceof Staff) {
            return $this->sendNotFoundResponse("The staff with id {$id} doesn't exist");
        }

        return $this->respondWithItem($staff, $this->staffTransformer);
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

        $staff = $this->staffRepository->save($request->all(), false);

        if (!$staff instanceof Staff) {
            return $this->sendCustomResponse(500, 'Error occurred on creating staff');
        }

        return $this->setStatusCode(201)->respondWithItem($staff, $this->staffTransformer);
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
        $staff = $this->staffRepository->findOne($id);
        if (!$staff instanceof Staff) {
            return $this->sendNotFoundResponse("The staff with id {$id} doesn't exist");
        }

        $staff = $this->staffRepository->update($staff, $request->all());

        return $this->respondWithItem($staff, $this->staffTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id) {
        $staff = $this->staffRepository->findOne($id);

        if (!$staff instanceof Staff) {
            return $this->sendNotFoundResponse("The staff with id {$id} doesn't exist");
        }

        $this->staffRepository->delete($staff);

        return response()->json(null, 204);
    }

    public function batch(Request $request){
        $queryString = $request->all();
        foreach ($queryString as $key => $item) {
            switch ($key) { 
                case 'create':
                    # code...
                    break;
                case 'update':
                    # code...
                    break;
                case 'delete':
                    if(!empty($item)){
                        $this->staffRepository->destroy(array_values($item));
                    }
                    return response()->json(null, 204);
                    break;
                default:
                    return $this->sendCustomResponse(500, 'Error queryString format on batch of Attendance');
                    break;
            }
        }
        exit;
    }

    /**
     * @brief  获取人员考勤列表
     * @param  string
     * @return array
     */
    public function attendances($uid) {
        $attendances = $this->attendanceviewRepository->getAttendanceviewList(array('uid' => $uid));
        return $this->respondWithCollection($attendances, $this->attendanceviewTransformer);
    }

    public function import(Request $request){
        $file = $request->file('file');
        $res = $this->staffRepository->importStaffInfos($file);
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
    private function storeRequestValidationRules(Request $request) {
        $rules = [
            'uid'                   => '',
            'name'                  => 'max:255',
            'gender'                => 'max:1',
            'level'                 => 'max:1',
            'mobile'                => 'max:11',
            'email'                 => 'max:255',
            'birthday'              => 'max:11',
            'idcard'                => 'max:18',
            'password'              => 'max:64',
            'employee_number'       => 'max:12',
            'company'               => 'max:64',
            'position'              => 'max:64',
            'type'                  => 'max:1',
            'label'                 => 'max:64',
            'status'                => 'max:1',
            'highest_education'     => 'max:255',
            'university'            => 'max:255',
            'major'                 => 'max:255',
            'major_type'            => 'max:255',
            'major_level'           => 'max:255'
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
            'name'                  => 'max:255',
            'gender'                => 'max:1',
            'level'                 => 'max:1',
            'mobile'                => 'max:11',
            'email'                 => 'max:255',
            'birthday'              => 'max:11',
            'idcard'                => 'max:18',
            'password'              => 'max:64',
            'employee_number'       => 'max:12',
            'company'               => 'max:64',
            'position'              => 'max:64',
            'type'                  => 'max:1',
            'label'                 => 'max:64',
            'status'                => 'max:1',
            'highest_education'     => 'max:255',
            'university'            => 'max:255',
            'major'                 => 'max:255',
            'major_type'            => 'max:255',
            'major_level'           => 'max:255'
        ];

        return $rules;
    }
}