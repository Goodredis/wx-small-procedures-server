<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contractorder;
use App\Repositories\Contracts\ContractorderRepository;
use App\Transformers\ContractorderTransformer;

class ContractorderController extends Controller
{
	/**
     * Instance of ContractorderRepository
     *
     * @var ContractorderRepository
     */
    private $contractorderRepository;

    /**
     * Instanceof ContractorderTransformer
     *
     * @var ContractorderTransformer
     */
    private $contractorderTransformer;

    /**
     * Constructor
     *
     * @param ContractorderRepository $contractorderRepository
     * @param ContractorderTransformer $contractorderTransformer
     */
    public function __construct(ContractorderRepository $contractorderRepository, ContractorderTransformer $contractorderTransformer) {
        $this->contractorderRepository = $contractorderRepository;
        $this->contractorderTransformer = $contractorderTransformer;
        
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
        $staffs = $this->contractorderRepository->findBy($queryString);
        $output = isset($queryString['output']) ? $queryString['output'] : 'json';
        if ($output == 'json') {
            return $this->respondWithCollection($staffs, $this->contractorderTransformer);
        } elseif ($output == 'excel') {
            $this->contractorderRepository->exportContractorders($staffs->toArray());
        }
	}

}