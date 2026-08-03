<?php

namespace App\Observers;

use App\Models\Customer;
use Carbon\Carbon;

class CustomerObserver {
    public function saving(Customer $customer): void {
        if ($customer->birthday) {
            /** @var int<0, max> $monthDay */
            $monthDay = (int) Carbon::parse($customer->birthday)->format('md');

            $customer->birthday_month_day = $monthDay;
        }
    }
}
