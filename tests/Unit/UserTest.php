<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_admin_helper(): void
    {
        $this->assertTrue((new User(['role' => User::ROLE_ADMIN]))->isAdmin());
        $this->assertFalse((new User(['role' => User::ROLE_SALES]))->isAdmin());
    }

    public function test_sales_helper_defines_restricted_visibility(): void
    {
        $this->assertTrue((new User(['role' => User::ROLE_SALES]))->isSales());
        $this->assertFalse((new User(['role' => User::ROLE_MANAGER]))->isSales());
        $this->assertFalse((new User(['role' => User::ROLE_ADMIN]))->isSales());
    }
}
