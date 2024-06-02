<?php

namespace App\Imports;

use Hash;
use App\Models\User;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithValidation, WithHeadingRow, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        $user = new User([
            'company_id'     => company()->company_id,
            'jwt'            => str_random(25),
            'active'         => 1,
            'sub_company_id' => $row['sub_company_id'],
            'badge_id'       => $row['badge_id'],
            'en_name'        => $row['en_name'],
            'ar_name'        => $row['ar_name'],
            'email'          => $row['email'],
            'phone'          => $row['phone'],
            'password'       => Hash::make($row['password']),
            'camp'           => $row['camp'],
            'street'         => $row['street'],
            'plot_no'        => $row['plot_no'],
            'block_no'       => $row['block_no'],
            'building_no'    => $row['building_no'],
            'apartment_no'   => $row['apartment_no'],
            'house_type'     => $row['house_type'],
        ]);

        return $user;
    }

    public function rules(): array
    {
        return [
            'sub_company_id' => 'required|exists:sub_companies,id,parent_id,' .company()->company_id,
            'badge_id'       => 'required|unique:users,badge_id,NULL,id,company_id,' . company()->company_id,
            'en_name'        => 'required',
            'ar_name'        => 'required',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'required|unique:users,phone',
            'password'       => 'required',
            'camp'           => 'required',
            'street'         => 'required',
            'plot_no'        => 'required',
            'block_no'       => 'required',
            'building_no'    => 'required',
            'apartment_no'   => 'required',
            'house_type'     => 'required',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'sub_company_id' => 'Sub Company ID',
            'badge_id'       => 'Badge ID',
            'en_name'        => 'EN Name',
            'ar_name'        => 'AR Name',
            'email'          => 'Email',
            'phone'          => 'Phone',
            'password'       => 'Password',
            'camp'           => 'Camp',
            'street'         => 'Street',
            'plot_no'        => 'Plot No',
            'block_no'       => 'Block No',
            'building_no'    => 'Building No',
            'apartment_no'   => 'Apartment No',
            'house_type'     => 'House Type',
        ];
    }

    public function chunkSize(): int
    {
        return 400;
    }

}
