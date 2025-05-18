<?php

namespace Database\Populate;

use App\Models\User;

class UsersPopulate
{
    public static function populate()
    {
        // MEMBERS
        for ($i = 1; $i <= 5; $i++) {
            $user = new User([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'role' => 'member'
            ]);
            $user->password = '123456';
            $user->password_confirmation = '123456';

            $user->save();
        }

        // ADMINS
        for ($i = 1; $i <= 3; $i++) {
            $user = new User([
                'name' => 'Admin ' . $i,
                'email' => 'admin' . $i . '@example.com',
                'role' => 'admin'
            ]);
            $user->password = '123456';
            $user->password_confirmation = '123456';

            $user->save();
        }

        echo "Users, members and admins populated!\n";
    }
}
