<?php

namespace Tests\Unit\Observers;

use App\Models\Customer;
use App\Observers\CustomerObserver;
use PHPUnit\Framework\TestCase;

class CustomerObserverTest extends TestCase
{
    public function test_derives_birthday_month_day_from_the_birthday_on_saving(): void
    {
        $customer = new Customer(['birthday' => '1990-03-15']);

        (new CustomerObserver())->saving($customer);

        $this->assertSame(315, $customer->birthday_month_day);
    }

    public function test_does_not_touch_birthday_month_day_when_birthday_is_not_set(): void
    {
        $customer = new Customer();

        (new CustomerObserver())->saving($customer);

        $this->assertNull($customer->birthday_month_day);
    }
}
