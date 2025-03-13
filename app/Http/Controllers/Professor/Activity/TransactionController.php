<?php

namespace App\Http\Controllers\Professor\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Professor\Activity\ListTransactionRequest;
use App\Http\Resources\Professor\Transaction\TransactionHistoryResource;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected ProfessorTransactionRepositoryInterface $professorTransactionRepository
    ) {}

    public function transactionList(Request $request)// ListTransactionRequest
    {
        try {
            $page = $request->page;
            $professorToken = $this->jWTService->getUserToken($request->bearerToken());
            $skip = null; //$page - 1;
            $take = null; //10;

            $data = $this->professorTransactionRepository->getProfessorTransaction($professorToken, $skip, $take);
            $data = $data->count() > 0 ? TransactionHistoryResource::collection($data) : [];
            $result = [
                'page' => $page,
                'data' => $data,
            ];

            return $this->success('Transaction history', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wromg', $e->getMessage());
        }
    }
}
