<?php

namespace App\Interfaces;

interface ProfessorLogsRepositoryInterface
{
    public function create($data);

    public function getAllProfessorLogs($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC');

    public function getAllProfessorLogCount($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null);
}
