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

    public function test_set_full_name(): void
    {
        $this->user1->full_name = 'Updated Name';
        $this->assertEquals('Updated Name', $this->user1->full_name);
    }

    public function test_find_by_cpf_should_return_the_user(): void
    {
        $this->assertEquals($this->user1->id, User::findByCpf($this->user1->cpf)->id);
    }

}
