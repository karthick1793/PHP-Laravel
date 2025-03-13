<?php

namespace App\Http\Controllers\Admin\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Activity\DownloadFileRequest;
use App\Http\Requests\Admin\Activity\ListTransactionRequest;
use App\Http\Resources\Admin\Activity\TokenTransactionResource;
use App\Interfaces\ProfessorLogsRepositoryInterface;
use App\Services\FileDownloadService;

class TokenTransactionController extends Controller
{
    public function __construct(
        protected ProfessorLogsRepositoryInterface $professorLogsRepository,
        protected FileDownloadService $fileDownloadService
    ) {}

    public function listTransactionHistory(ListTransactionRequest $request)
    {
        try {
            $page = $request->page; //starting count = 1
            $perPage = $request->per_page;
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $searchValue = $request->search_value;
            $quarterToken = $request->quarter_token;
            $professorToken = $request->professor_token;

            $skip = ($page - 1) * $perPage;
            $take = $perPage;

            $data = $this->professorLogsRepository->getAllProfessorLogs($skip, $take, $searchValue, $fromDate, $toDate, $quarterToken, $professorToken, orderBy: 'DESC');
            $totalCount = $this->professorLogsRepository->getAllProfessorLogCount(searchValue: $searchValue, fromDate: $fromDate, toDate: $toDate, quarterToken: $quarterToken, professorToken: $professorToken);
            $displayCount = $data->count();
            $data = $displayCount > 0 ? TokenTransactionResource::collection($data) : [];

            $result = [
                'page' => $page,
                'iTotalRecords' => $totalCount,
                'iTotalDisplayRecords' => $displayCount,
                'aaData' => $data,
            ];

            return $this->success('Professor list', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function downloadTokenTransactions(DownloadFileRequest $request)
    {
        try {
            $fileType = $request->type;
            $page = $request->page; //starting count = 1
            $perPage = $request->per_page;
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $searchValue = $request->search_value;
            $quarterToken = $request->quarter_token;
            $professorToken = $request->professor_token;

            $skip = null; // ($page - 1) * $perPage;
            $take = null; // $perPage;

            $data = $this->professorLogsRepository->getAllProfessorLogs($skip, $take, $searchValue, $fromDate, $toDate, $quarterToken, $professorToken);
            $data = $data->count() > 0 ? TokenTransactionResource::collection($data) : [];
            $resultant = [];
            $count = 1;
            foreach ($data as $datum) {
                $datum = $datum->toArray($request);
                $resultant[] = [
                    'sl_no' => $count++,
                    'name' => $datum['name'],
                    'quarter_name' => $datum['quarter_name'],
                    'room_number' => $datum['room_number'],
                    'date' => $datum['date'],
                    'time' => $datum['time'],
                    'tokens' => $datum['tokens'],
                ];
            }

            $fileName = 'TokenTransactionHistory';
            $columns = ['Sl No', 'Name', 'Quarters Name', 'Room Number', 'Date', 'Time', 'Added Tokens'];
            if ($fileType == 'csv') {
                return $this->fileDownloadService->generateCsv($resultant, $columns, $fileName);
            } elseif ($fileType == 'xlsx') {
                return $this->fileDownloadService->generateXlsx($resultant, $columns, $fileName);
            } elseif ($fileType == 'pdf') {
                $viewFile = 'pdf.admin.activity.professor';

                return $this->fileDownloadService->generatePdf($resultant, $viewFile, $fileName);
            }
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
