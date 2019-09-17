<?php
/**
 * Created by PhpStorm.
 * User: w17600101602
 * Date: 2019/9/17
 * Time: 10:57
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Instance of OrderRepository
     *
     * @var UserRepository
     */
    private $orderRepository;
    private $orderTransformer;

    /**
     * Constructor
     *
     * @param OrderRepository $orderRepository
     *  @param OrderTransformer $orderTransformer
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    // 储存数据
    public function store(Request $request) {
        $data = $request->all();
        //echo '<pre>';var_dump($data);exit;
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));
        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }
        $order = $this->orderRepository->save($data);
        if (!$order instanceof Order) {
            return $this->sendCustomResponse(500, 'Error occurred on creating User');
        }
        return $this->respondWithArray($order->toArray());
//        return $this->setStatusCode(201)->respondWithItem($order);
    }

    // 验证规则
    private function storeRequestValidationRules(Request $request)
    {
        $rules = [
            'oid'                 => 'required',
            'status'              => 'required',
        ];

        return $rules;
    }

    // 验证数据
    protected function validateRequest(Request $request, array $rules)
    {
        // Perform Validation
        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->messages();

            // crete error message by using key and value
            foreach ($errorMessages as $key => $value) {
                $errorMessages[$key] = $value[0];
            }

            return $errorMessages;
        }

        return true;
    }

}