<?php

namespace Tests\Unit\Lib\Authentication;

use Lib\Authentication\Auth;
use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];

        $this->user = new User([
            'cpf' => '12345678901',
            'password' => '123456',
            'password_confirmation' => '123456',
            'full_name' => 'User Test',
            'email' => 'user@test.com',
            'status' => true,
            'created' => date('Y-m-d H:i:s'),
        ]);

        $this->user->save();

        if (!$this->user->id) {
            $this->fail('User ID not set after save, check your save method.');
        }
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $_SESSION = [];
    }

    public function test_login(): void
    {
        Auth::login($this->user);
        $this->assertEquals($this->user->id, $_SESSION['user']['id']);
    }

    public function test_user(): void
    {
        Auth::login($this->user);
        $userFromSession = Auth::user();
        $this->assertEquals($this->user->id, $userFromSession->id);
    }

    public function test_check(): void
    {
        Auth::login($this->user);
        $this->assertTrue(Auth::check());
    }

    public function test_logout(): void
    {
        Auth::login($this->user);
        Auth::logout();
        $this->assertFalse(Auth::check());
    }
}
