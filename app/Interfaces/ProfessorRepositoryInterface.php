<?php

namespace App\Interfaces;

use App\Models\Professor;

interface ProfessorRepositoryInterface
{
    public function getAllProfessor();

    public function getAllProfessorWithRelation($skip = null, $take = null, $searchValue = null, $quarterToken = null);

    public function getAllProfessorCount($skip = null, $take = null, $searchValue = null, $quarterToken = null);

    public function getProfessor($professorToken);

    public function checkIfProfessorExists(string $countryCode, string $mobileNumber): bool;

    public function professorMobileUpdateOTPExpiry(string $mobile): bool;

    public function getProfessorWithMobile(string $countryCode, string $mobileNumber): ?Professor;

    public function decrementTokenIfAvailable($professorToken): bool;

    public function incrementToken($professorToken, $tokens);
}
