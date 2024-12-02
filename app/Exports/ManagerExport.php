<?php

namespace App\Exports;
use App\Models\AddressModel;
use App\Models\UsersModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class ManagerExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $users = UsersModel::where('role_id', 3)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        $users->each(function($user) {
            $address = AddressModel::where('user_id', $user->id)->select('address','ward', 'district','province','default','name','phone')->first();
            if ($address) {
            $user->address = $address->address;
            $user->ward = $address->ward;
            $user->district = $address->district;
            $user->province = $address->province;
            $user->default = $address->default;
            $user->address_name = $address->name;
            $user->address_phone = $address->phone;
            } else {
            $user->address = null;
            $user->ward = null;
            $user->district = null;
            $user->province = null;
            $user->default = null;
            $user->address_name = null;
            $user->address_phone = null;
            }
        });
        return $users;
    }
}
