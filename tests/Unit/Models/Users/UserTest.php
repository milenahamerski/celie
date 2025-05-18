<?php

namespace Tests\Unit\Models\Users;

use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user1;
    private User $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->user1 = new User([
            'cpf' => '12345678901',
            'full_name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => 'password123',
        ]);
        $this->user1->save();

        $this->user2 = new User([
            'cpf' => '10987654321',
            'full_name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => 'password456',
        ]);
        $this->user2->save();
    }

    public function test_should_create_new_user(): void
    {
        $this->assertCount(2, User::all());
    }

    public function test_all_should_return_all_users(): void
    {
        $users = [$this->user1->id, $this->user2->id];
        $all = array_map(fn($user) => $user->id, User::all());

        $this->assertCount(2, $all);
        $this->assertEquals($users, $all);
    }

    public function test_destroy_should_remove_the_user(): void
    {
        $this->user1->destroy();
        $this->assertCount(1, User::all());
    }

    public function test_set_full_name(): void
    {
        $this->user1->full_name = 'Updated Name';
        $this->assertEquals('Updated Name', $this->user1->full_name);
    }

    public function test_set_email(): void
    {
        $this->user1->email = 'new_email@example.com';
        $this->assertEquals('new_email@example.com', $this->user1->email);
    }

    public function test_find_by_cpf_should_return_the_user(): void
    {
        $this->assertEquals($this->user1->id, User::findByCpf($this->user1->cpf)->id);
    }

    public function test_find_by_cpf_should_return_null(): void
    {
        $this->assertNull(User::findByCpf('00000000000'));
    }

    public function test_authenticate_should_return_true(): void
    {
        $this->assertTrue($this->user1->authenticate('password123'));
    }

    public function test_authenticate_should_return_false(): void
    {
        $this->assertFalse($this->user1->authenticate('wrongpassword'));
    }
}
