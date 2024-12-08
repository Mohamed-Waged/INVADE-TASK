<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Tasks\Entities\Task;
use Modules\Categories\Entities\Category;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate(
            ['id' => 1], 
            ['name' => 'Work']
        );

        Category::firstOrCreate(
            ['id' => 2],
            ['name' => 'Personal']
        );

        Category::firstOrCreate(
            ['id' => 3],
            ['name' => 'Urgent']
        );

        Task::firstOrCreate(
            ['id' => 1],
            [
                'category_id' => 1,
                'title' => "title",
                'description' => "body",
                'status' => "completed",
            ]
        );

        // Seed User
        User::firstOrCreate(
            ['email' => 'user@user.com'], 
            [
                'id'       => 1,
                'name'     => 'user',
                'password' => bcrypt('user'),
                'status'   => true
            ]
        );
    }
}
