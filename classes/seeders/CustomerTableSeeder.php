<?php

namespace OFFLINE\Mall\Classes\Seeders;

use October\Rain\Database\Updates\Seeder;
use OFFLINE\Mall\Models\Address;
use OFFLINE\Mall\Models\Customer;
use RainLab\Location\Models\Country;
use RainLab\Location\Models\State;
use RainLab\User\Models\User;

class CustomerTableSeeder extends Seeder
{
    public function run()
    {
        if (app()->environment() === 'testing') {
            $user                        = new User();
            $user->email                 = 'test@test.com';
            $user->password              = 'abcd';
            $user->password_confirmation = 'abcd';
            $user->is_activated          = true;
            $user->save();

            $customer          = new Customer();
            $customer->name    = 'Floaty McFloatface';
            $customer->user_id = $user->id;
            $customer->save();

            $shippingAddress             = new Address();
            $shippingAddress->name       = 'Float McFloatface';
            $shippingAddress->lines      = 'Street 12';
            $shippingAddress->zip        = '6000';
            $shippingAddress->city       = 'Lucerne';
            $shippingAddress->state_id   = State::where('name', 'Luzern')->first()->id;
            $shippingAddress->country_id = Country::where('code', 'CH')->first()->id;

            $customer->addresses()->save($shippingAddress);

            $billingAddress             = new Address();
            $billingAddress->name       = 'Float McFloatface';
            $billingAddress->lines      = 'Billing 12';
            $billingAddress->zip        = '6000';
            $billingAddress->city       = 'Lucerne';
            $billingAddress->state_id   = State::where('name', 'Luzern')->first()->id;
            $billingAddress->country_id = Country::where('code', 'CH')->first()->id;

            $customer->addresses()->save($billingAddress);
        }
    }
}
