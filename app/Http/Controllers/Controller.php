<?php //app/Http/Controllers/Controller.php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use App\Foundations\Fractal\NoDataArraySerializer;
use Ixudra\Curl\Facades\Curl;

class Controller extends BaseController
{
    use ResponseTrait;
    /**
     * the current login user 
     */
    protected $user;

    /**
     * Constructor
     *
     * @param Manager|null $fractal
     */
    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal === null ? new Manager() : $fractal;
        $fractal->setSerializer(new NoDataArraySerializer);//设置序列化器
        $this->setFractal($fractal);

        $this->user = \Auth::user();
    }

    /**
     * Validate HTTP request against the rules
     *
     * @param Request $request
     * @param array $rules
     * @return bool|array
     */
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

trait WebdiskTrait
{

    /*
        用途
            仿照webdisk_model的curl方法，用Ixudra\Curl重新封装的curl方法
        参数
            $url：curl要访问的地址（字符串）
            $postdata：POST发送的数据（数组）
            $headers：同时发送的HTTP请求头（数组）
         返回值
            $response：结果（json字符串）
    */
    public function curl_post($url, $postdata, $proxy='', $proxyport='', $headers=[])
    {
        $response = Curl::to($url)
            ->withOption('RETURNTRANSFER', 1)
            ->withOption('PROXY', $proxy)
            ->withOption('PROXYPORT', $proxyport)
            ->withData(json_encode($postdata))
            ->withHeaders($headers)
            ->post();
        return $response;
    }

    public function curl_get($url, $data, $proxy='', $proxyport='', $headers=[])
    {
        $response = Curl::to($url)
            ->withOption('RETURNTRANSFER', 1)
            ->withOption('PROXY', $proxy)
            ->withOption('PROXYPORT', $proxyport)
            ->withData($data)
            ->withHeaders($headers)
            ->get();
        return $response;
    }
}
