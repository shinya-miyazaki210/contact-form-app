<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        // すでに問い合わせがあれば、重複登録しない
        if (Contact::exists()) {
            return;
        }

        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty() || $tags->isEmpty()) {
            throw new \RuntimeException(
                'カテゴリとタグを先に登録してください。'
            );
        }

        $faker = \Faker\Factory::create('ja_JP');

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'category_id' => $categories->random()->id,
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->unique()->safeEmail(),
                'tel' => $faker->numerify('##########'),
                'address' => $faker->prefecture()
                    . $faker->city()
                    . $faker->streetAddress(),
                'building' => $faker->optional()->secondaryAddress(),
                'detail' => mb_substr(
                    $faker->realText(200),
                    0,
                    120
                ),
            ]);

            $contact->tags()->attach(
                $tags->random(
                    rand(1, min(3, $tags->count()))
                )->pluck('id')->all()
            );
        }
    }
}