<?php

namespace App\Interfaces;

interface ProfessorMilkBookingRepositoryInterface
{
    public function create($array);

    public function getById($id);

    public function getAllBookings($skip = null, $take = null, $date = null, $slot = null, $searchValue = null, $orderBy = 'DESC', $withCount = true, $download = false);

    public function getProfessorBookings($professorToken, $skip = null, $take = null, $slot = null, $orderBy = 'DESC', $withCount = true);

    public function getBookingsGroupedByDate($skip = null, $take = null, $searchValue = null, $withCount = true);

    public function updateStatus($id, $status);

    public function getRecordForProfessor($professorToken, $deliveryDate, $slot);
}
