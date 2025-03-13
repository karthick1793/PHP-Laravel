<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\UploadProfessorCsvRequest;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\QuarterRepositoryInterface;
use App\Models\Professor;
use App\Models\ProfessorLog;
use App\Models\Quarter;
use App\Models\QuarterRoom;

class UploadProfessorController extends Controller
{
    public function __construct(
        protected QuarterRepositoryInterface $quarterRepository,
        protected ProfessorRepositoryInterface $professorRepository
    ) {}

    public function uploadCsv(UploadProfessorCsvRequest $request)
    {
        try {
            $csvFilePath = $request->professor_csv;

            //get data
            $quartersData = $this->quarterRepository->getAllQuatersWithRooms()->mapWithKeys(function ($item) {
                $rooms = $item->rooms->mapWithKeys(function ($room) {
                    return [$room->name => $room->token];
                });

                return [$item->name => [
                    'token' => $item->token,
                    'rooms' => $rooms,
                ]];
            })->toArray();
            $professorData = $this->professorRepository->getAllProfessor()->mapWithKeys(function ($professor) {
                return [$professor->mobile_number => ''];
            });

            $quarters = [];
            $quartersRooms = [];
            $professors = [];
            $professorLogs = [];
            $dateTime = now();

            $file = fopen($csvFilePath, 'r');
            $rowCount = 0;
            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($row == [null]) {
                    $rowCount++;

                    continue;
                }
                if ($rowCount != 0) {
                    $professorName = trim($row[0]);
                    $countryCode = trim($row[1]);
                    $mobileNumber = trim($row[2]);
                    $quatersName = trim($row[3]);
                    $roomNumber = trim(ceil($row[4]));
                    $availableCoin = trim(ceil($row[5]));
                    if (! $professorName) {
                        return $this->error('Professor code should not be empty!', code: 400);
                    }
                    if (! $countryCode) {
                        return $this->error('Country Code should not be empty!', code: 400);
                    }
                    if (! $mobileNumber) {
                        return $this->error('Mobile Number should not be empty!', code: 400);
                    }
                    if (! $quatersName) {
                        return $this->error('Quarters Name should not be empty!', code: 400);
                    }
                    if (! is_numeric($roomNumber)) {
                        return $this->error('Quarters should contain only digits!', 400);
                    }
                    if (! is_numeric($availableCoin)) {
                        return $this->error('Available Coin should contain only digits!', 400);
                    }

                    //prepare quarters & room arrays
                    if (isset($quartersData[$quatersName])) {
                        if (! isset($quartersData[$quatersName]['rooms'][$roomNumber])) {
                            $quarterToken = $quartersData[$quatersName]['token'];
                            $roomToken = $this->generateUlid();
                            $quartersRooms[] = [
                                'token' => $roomToken,
                                'quarter_token' => $quarterToken,
                                'name' => $roomNumber,
                                'created_at' => $dateTime,
                                'updated_at' => $dateTime,
                            ];
                            $quartersData[$quatersName]['rooms'][$roomNumber] = $roomToken;
                        }
                    } else {
                        $quarterToken = $this->generateUlid();
                        $quarters[] = [
                            'token' => $quarterToken,
                            'name' => $quatersName,
                            'created_at' => $dateTime,
                            'updated_at' => $dateTime,
                        ];
                        $roomToken = $this->generateUlid();
                        $quartersRooms[] = [
                            'token' => $roomToken,
                            'quarter_token' => $quarterToken,
                            'name' => $roomNumber,
                            'created_at' => $dateTime,
                            'updated_at' => $dateTime,
                        ];
                        $quartersData[$quatersName] = [
                            'token' => $quarterToken,
                            'rooms' => [
                                $roomNumber => $roomToken,
                            ],
                        ];
                    }
                    $quarterToken = $quartersData[$quatersName]['token'];
                    $roomToken = $quartersData[$quatersName]['rooms'][$roomNumber];

                    //prepare professors & professor logs(coin addition log) arrays
                    if (! isset($professorData[$mobileNumber])) {
                        $professorToken = $this->generateUlid();
                        $professors[] = [
                            'token' => $professorToken,
                            'name' => $professorName,
                            'country_code' => $countryCode,
                            'mobile_number' => $mobileNumber,
                            'available_coin_count' => $availableCoin,
                            'room_token' => $roomToken,
                            'created_at' => $dateTime,
                            'updated_at' => $dateTime,
                        ];
                        if ($availableCoin > 0) {
                            $professorLogs[] = [
                                'token' => $this->generateUlid(),
                                'professor_token' => $professorToken,
                                'old_coin_count' => 0,
                                'added_coin_count' => $availableCoin,
                                'total_coin_count' => $availableCoin,
                                'created_at' => $dateTime,
                                'updated_at' => $dateTime,
                            ];
                        }
                        $professorData[$mobileNumber] = '';
                    }
                }
                $rowCount++;
            }
            fclose($file);
            unset($quartersData);
            unset($professorData);

            \DB::beginTransaction();
            if (count($quarters) > 0) {
                $quarters = array_chunk($quarters, 500);
                foreach ($quarters as $quartersInfo) {
                    Quarter::insert($quartersInfo);
                }
                unset($quarters);
            }
            if (count($quartersRooms) > 0) {
                $quartersRooms = array_chunk($quartersRooms, 500);
                foreach ($quartersRooms as $quartersRoomsInfo) {
                    QuarterRoom::insert($quartersRoomsInfo);
                }
                unset($quartersRooms);
            }
            if (count($professors) > 0) {
                $professors = array_chunk($professors, 500);
                foreach ($professors as $professorsInfo) {
                    Professor::insert($professorsInfo);
                }
                unset($professors);
            }
            if (count($professorLogs) > 0) {
                $professorLogs = array_chunk($professorLogs, 500);
                foreach ($professorLogs as $professorLogsInfo) {
                    ProfessorLog::insert($professorLogsInfo);
                }
                unset($professorLogs);
            }
            \DB::commit();

            return $this->success('Data has been created');
        } catch (\Exception $e) {
            return $this->error('Something went wrong', $e->getMessage());
        }
    }
}
