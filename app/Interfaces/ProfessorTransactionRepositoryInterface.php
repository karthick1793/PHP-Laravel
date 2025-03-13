<?php

namespace App\Interfaces;

interface ProfessorTransactionRepositoryInterface
{
    public function getAllProfessorTransaction($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC');

    public function getAllProfessorTransactionCount($skip = null, $take = null, $searchValue = null, $fromDate = null, $toDate = null, $quarterToken = null, $professorToken = null, $orderBy = 'ASC');

    public function getProfessorTransaction($professorToken, $skip = null, $take = null);

    public function createOrUpdate($professorToken, $data, $date);

    public function getCurrentDayRecord($professorToken);

    public function getGivenDayRecord($professorToken, $date);

    public function getLastFiveRecords($professorToken);
}
