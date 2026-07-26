<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_computes_days_until_the_next_birthday_wrapping_to_next_year_if_it_already_passed(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-06-01'));

        $upcoming = new Customer(['birthday' => '1990-06-10']);
        $this->assertSame(9, $upcoming->days_until_birthday);

        $passed = new Customer(['birthday' => '1990-05-10']);
        $this->assertSame(343, $passed->days_until_birthday);
    }
}
