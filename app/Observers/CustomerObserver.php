<?php

namespace App\Observers;

use App\Models\Customer;
use Carbon\Carbon;

class CustomerObserver {
    public function saving(Customer $customer): void {
        if ($customer->birthday) {
            $customer->birthday_month_day = (int) Carbon::parse($customer->birthday)->format('md');
        }
    }
}
