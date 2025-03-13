<?php

namespace App\Http\Controllers\Admin\DropDown;

use App\Http\Controllers\Controller;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\QuarterRepositoryInterface;

class DropDownController extends Controller
{
    public function __construct(
        protected QuarterRepositoryInterface $quarterRepository,
        protected ProfessorRepositoryInterface $professorRepository
    ) {}

    public function listQuarters()
    {
        try {
            $quarters = $this->quarterRepository->getAllQuatersWithRooms();

            return $this->success('Quarters list', $quarters);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }

    public function listProfessors()
    {
        try {
            $professors = $this->professorRepository->getAllProfessor();

            return $this->success('Professors list', $professors);
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
