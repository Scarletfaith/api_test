<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name'          => 'Username 1',
                'email'         => 'un1@localhost',
                'password'      => '$2y$10$gVeHvqzAEZrGwrYmUbSD2u6AnW9jXcz.Pod6x3QuuyVFZ8CH0As1y',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'name'          => 'Username 2',
                'email'         => 'un2@localhost',
                'password'      => '$2y$10$gVeHvqzAEZrGwrYmUbSD2u6AnW9jXcz.Pod6x3QuuyVFZ8CH0As1y',
                'created_at'    => now(),
                'updated_at'    => now()
            ],
            [
                'name'          => 'Username 3',
                'email'         => 'un3@localhost',
                'password'      => '$2y$10$gVeHvqzAEZrGwrYmUbSD2u6AnW9jXcz.Pod6x3QuuyVFZ8CH0As1y',
                'created_at'    => now(),
                'updated_at'    => now()
            ]
        ];

        User::insert($users);
    }
}
