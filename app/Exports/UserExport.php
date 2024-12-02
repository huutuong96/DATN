<?php

namespace App\Exports;
use App\Models\AddressModel;
use App\Models\UsersModel;
use Maatwebsite\Excel\Concerns\FromCollection;

class UserExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    public function collection()
    {
        if ($this->request->role == 1) {
            $users = UsersModel::where('role_id', 1)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
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
        }
        if ($this->request->role == 2) {
            $users = UsersModel::where('role_id', 2)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
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
        }
        if ($this->request->role == 3) {
            $users = UsersModel::where('role_id', 3)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->role == 4) {
            $users = UsersModel::where('role_id', 4)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->status == 101) {
            $users = UsersModel::where('status', 101)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->status == 1) {
            $users = UsersModel::where('status', 1)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->point >= 1000) {
            $users = UsersModel::where('point', '>=', 1000)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->point >= 2000) {
            $users = UsersModel::where('point', '>=', 2000)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->point >= 3000) {
            $users = UsersModel::where('point', '>=', 3000)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->point >= 5000) {
            $users = UsersModel::where('point', '>=', 5000)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        if ($this->request->point >= 10000) {
            $users = UsersModel::where('point', '>=', 10000)->select('id','fullname','phone', 'email','point','genre','datebirth','rank_id','role_id','status')->get();
        }
        return $users;
    }
}
