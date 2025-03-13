<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Reports\DownloadFileRequest;
use App\Http\Requests\Admin\Reports\ListMilkTransactionReport;
use App\Http\Resources\Admin\Reports\MilkTransactionResource;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Services\FileDownloadService;

class MilkTransactionController extends Controller
{
    public function __construct(
        protected ProfessorTransactionRepositoryInterface $professorTransactionRepository,
        protected FileDownloadService $fileDownloadService
    ) {}

    public function listMilkTransactionHistory(ListMilkTransactionReport $request)
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

            $data = $this->professorTransactionRepository->getAllProfessorTransaction($skip, $take, $searchValue, $fromDate, $toDate, $quarterToken, $professorToken, orderBy: 'DESC');
            $totalCount = $this->professorTransactionRepository->getAllProfessorTransactionCount(searchValue: $searchValue, fromDate: $fromDate, toDate: $toDate, quarterToken: $quarterToken, professorToken: $professorToken);
            $displayCount = $data->count();
            $data = $displayCount > 0 ? MilkTransactionResource::collection($data) : [];

            $result = [
                'page' => $page,
                'iTotalRecords' => $totalCount,
                'iTotalDisplayRecords' => $displayCount,
                'aaData' => $data,
            ];

            return $this->success('Milk Transaction list', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function downloadMilkTransactions(DownloadFileRequest $request)
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

            $data = $this->professorTransactionRepository->getAllProfessorTransaction($skip, $take, $searchValue, $fromDate, $toDate, $quarterToken, $professorToken, orderBy: 'DESC');
            $data = $data->count() > 0 ? MilkTransactionResource::collection($data) : [];
            $resultant = [];
            $count = 1;
            foreach ($data as $datum) {
                $datum = $datum->toArray($request);
                $resultant[] = [
                    'sl_no' => $count++,
                    'name' => $datum['name'],
                    'quarter_name' => $datum['quarter_name'],
                    'room_number' => 'No. '.$datum['room_number'],
                    'date' => $datum['date'],
                    'time' => $datum['time'],
                    'moring_slot' => $datum['morning_slot'] == 'true' ? 'Yes' : 'No',
                    'evening_slot' => $datum['evening_slot'] == 'true' ? 'Yes' : 'No',
                    'litre' => $datum['litre'].' Litre',
                ];
            }

            $fileName = 'MilkTransactionHistory';
            $columns = ['Sl No', 'Name', 'Quarters Name', 'Room Number', 'Date', 'Time', 'Morning Slot', 'Evening Slot', 'Milk Distributed'];
            if ($fileType == 'csv') {
                return $this->fileDownloadService->generateCsv($resultant, $columns, $fileName);
            } elseif ($fileType == 'xlsx') {
                return $this->fileDownloadService->generateXlsx($resultant, $columns, $fileName);
            } elseif ($fileType == 'pdf') {
                $viewFile = 'pdf.admin.reports.milkTransaction';

                return $this->fileDownloadService->generatePdf($resultant, $viewFile, $fileName);
            }
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
