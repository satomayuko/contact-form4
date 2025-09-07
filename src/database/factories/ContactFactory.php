<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        // 既存カテゴリからランダム（Seederで先に投入する想定）
        $categoryId = Category::inRandomOrder()->value('id');

        // 念のため、無ければ1件作る（開発用の保険）
        if (!$categoryId) {
            $categoryId = Category::create(['content' => 'その他'])->id;
        }

        return [
            'category_id' => $categoryId,
            'last_name'   => $this->faker->lastName(),
            'first_name'  => $this->faker->firstName(),
            'gender'      => $this->faker->numberBetween(1, 3), // 1:男 2:女 3:その他
            'email'       => $this->faker->safeEmail(),
            // 0から始まる日本の電話風（ハイフン無し）
            'tel'         => '0' . $this->faker->numerify('#########'),
            'address'     => $this->faker->address(),
            'building'    => $this->faker->optional()->secondaryAddress(),
            'detail'      => $this->faker->realText(80),
        ];
    }
}