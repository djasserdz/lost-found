<?php

namespace Database\Seeders;

use App\Models\category;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Post::factory(10)->create([
            'user_id' => User::inRandomOrder()->first()->id,
        ])->each(function ($post) {
            $post->categories()->attach(category::inRandomOrder()->limit(rand(1, 2))->pluck('id'));
        });
    }
}
