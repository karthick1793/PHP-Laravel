<?php

namespace App\Http\Controllers\Admin\ManageToken;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageToken\AddTokenRequest;
use App\Http\Requests\Admin\ManageToken\DownloadFileRequest;
use App\Http\Requests\Admin\ManageToken\ListProfessorRequest;
use App\Http\Resources\Admin\ManageTokens\ProfessorWithTokenResource;
use App\Interfaces\ProfessorLogsRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Services\FileDownloadService;

class ManageTokenController extends Controller
{
    public function __construct(
        protected ProfessorRepositoryInterface $professorRepository,
        protected ProfessorLogsRepositoryInterface $professorLogsRepository,
        protected FileDownloadService $fileDownloadService,
    ) {}

    public function professorList(ListProfessorRequest $request)
    {
        try {
            $page = $request->page; //starting count = 1
            $perPage = $request->per_page;
            $searchValue = $request->search_value;
            $quarterToken = $request->quarter_token;

            $skip = ($page - 1) * $perPage;
            $take = $perPage;

            $data = $this->professorRepository->getAllProfessorWithRelation($skip, $take, $searchValue, $quarterToken);
            $displayCount = $data->count();
            $totalRecords = $this->professorRepository->getAllProfessorCount(searchValue: $searchValue);
            $data = $displayCount > 0 ? ProfessorWithTokenResource::collection($data) : [];

            $result = [
                'page' => $page,
                'iTotalRecords' => $totalRecords,
                'iTotalDisplayRecords' => $displayCount,
                'aaData' => $data,
            ];

            return $this->success('Token transaction list', $result);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function updateProfessorToken(AddTokenRequest $request)
    {
        try {
            $professorToken = $request->professor_token;
            $tokens = $request->tokens;

            $professor = $this->professorRepository->getProfessor($professorToken);
            $oldTokenCount = $professor->available_coin_count;
            \DB::beginTransaction();
            $this->professorRepository->incrementToken($professorToken, $tokens);
            $data = [
                'professor_token' => $professorToken,
                'old_coin_count' => $oldTokenCount,
                'added_coin_count' => $tokens,
                'total_coin_count' => $tokens + $oldTokenCount,
            ];
            $this->professorLogsRepository->create($data);
            \DB::commit();

            return $this->success('Token has been updated');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function downloadProfessorWithTokens(DownloadFileRequest $request)
    {
        try {
            $fileType = $request->type;
            $searchValue = $request->search_value;
            $quartersToken = $request->quarter_token;

            $data = $this->professorRepository->getAllProfessorWithRelation(searchValue: $searchValue, quarterToken: $quartersToken);
            $data = $data->count() > 0 ? ProfessorWithTokenResource::collection($data) : [];
            $resultant = [];
            $count = 1;
            foreach ($data as $datum) {
                $datum = $datum->toArray($request);
                $resultant[] = [
                    'sl_no' => $count++,
                    'name' => $datum['name'],
                    'number' => $datum['number'],
                    'quarters_name' => $datum['quarters_name'],
                    'room_number' => $datum['room_number'],
                    'tokens' => $datum['tokens'],
                ];
            }

            $fileName = 'professors';
            $columns = ['Sl No', 'Name', 'Mobile Number', 'Quarters Name', 'Room Number', 'Balance Tokens'];
            if ($fileType == 'csv') {
                return $this->fileDownloadService->generateCsv($resultant, $columns, $fileName);
            } elseif ($fileType == 'xlsx') {
                return $this->fileDownloadService->generateXlsx($resultant, $columns, $fileName);
            } elseif ($fileType == 'pdf') {
                $viewFile = 'pdf.admin.manageTokens.professor';

                return $this->fileDownloadService->generatePdf($resultant, $viewFile, $fileName);
            }
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
