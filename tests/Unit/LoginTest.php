<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /**
     * Test login with admin role.
     */
    public function test_login_admin(): void
    {
        // Simulate admin login
        $result = $this->simulateLogin('admin@admin.com', 'admin1234');
        $this->assertTrue($result, 'Admin should be able to login.');
    }

    /**
     * Test login with user role.
     */
    public function test_login_user(): void
    {
        // Simulate user login
        $result = $this->simulateLogin('joe@user.com', 'joe12345');
        $this->assertTrue($result, 'User should be able to login.');
    }

    /**
     * Test invalid login.
     */
    public function test_invalid_login(): void
    {
        // Simulate invalid login
        $result = $this->simulateLogin('invalid@email.com', 'wrong_password');
        $this->assertFalse($result, 'Invalid login should be rejected.');
    }

    /**
     * Simulate a login attempt.
     * This is a placeholder function. Replace it with actual login logic.
     */
    private function simulateLogin(string $email, string $password): bool
    {
        // Replace this with actual login logic
        if ($email === 'admin@admin.com' && $password === 'admin1234') {
            return true;
        } elseif ($email === 'joe@user.com' && $password === 'joe12345') {
            return true;
        }
        return false;
    }
}
