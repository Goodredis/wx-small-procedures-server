<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepository;
use Illuminate\Http\Request;
use App\Transformers\SupplierTransformer;

class SupplierController extends BaseController
{
    public function index(){
        return str_random('32');exit;
    }

    public function create(){
        return str_random('32');exit;
    }

    public function show($id){
        return str_random('32');exit;
    }

    public function store(){
        return str_random('32');exit;
    }

    public function edit($id){
        return str_random('32');exit;
    }

    public function update($id){
        return str_random('32');exit;
    }

    public function destroy($id){
        return str_random('32');exit;
    }
}