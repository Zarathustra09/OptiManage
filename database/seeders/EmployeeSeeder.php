<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@example.com',
                'phone_number' => '1234567891',
                'password' => Hash::make('Password123'),
                'role_id' => 0,
                'employee_id' => 'ALICE-12345'
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@example.com',
                'phone_number' => '1234567892',
                'password' => Hash::make('Password123'),
                'role_id' => 0,
                'employee_id' => 'BOB-12345'
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie.brown@example.com',
                'phone_number' => '1234567893',
                'password' => Hash::make('Password123'),
                'role_id' => 0,
                'employee_id' => 'CHARLIE-12345'
            ],
            [
                'name' => 'Diana Prince',
                'email' => 'diana.prince@example.com',
                'phone_number' => '1234567894',
                'password' => Hash::make('Password123'),
                'role_id' => 0,
                'employee_id' => 'DIANA-12345'
            ],
            [
                'name' => 'Eve Adams',
                'email' => 'eve.adams@example.com',
                'phone_number' => '1234567895',
                'password' => Hash::make('Password123'),
                'role_id' => 0,
                'employee_id' => 'EVE-12345'
            ],
        ];

        foreach ($employees as $employee) {
            $user = User::create($employee);

            $shiftTimings = Config::get('shifts.day');
            $shiftType = $shiftTimings['shift_type'];
            unset($shiftTimings['shift_type']);

            foreach ($shiftTimings as $day => $timing) {
                Availability::create([
                    'user_id' => $user->id,
                    'day' => $day,
                    'available_from' => $timing['from'],
                    'available_to' => $timing['to'],
                    'status' => 'active',
                    'shift_type' => $shiftType,
                ]);
            }
        }
    }
}
