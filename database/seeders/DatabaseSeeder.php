<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const NUMBER_OF_BOOKS = 33;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(self::NUMBER_OF_BOOKS)->create()->each(function($book) {
            $numberOfReviews = random_int(5, 30);

            Review::factory($numberOfReviews)
                ->good()
                ->for($book)
                ->create();
        });

        Book::factory(self::NUMBER_OF_BOOKS)->create()->each(function($book) {
            $numberOfReviews = random_int(5, 30);

            Review::factory($numberOfReviews)
                ->average()
                ->for($book)
                ->create();
        });

        Book::factory(self::NUMBER_OF_BOOKS)->create()->each(function($book) {
            $numberOfReviews = random_int(5, 30);

            Review::factory($numberOfReviews)
                ->bad()
                ->for($book)
                ->create();
        });
    }
}
