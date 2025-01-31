<?php

namespace HeadlessLaravel\Formations\Tests\Fixtures\Database\Factories;

use HeadlessLaravel\Formations\Tests\Fixtures\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence
        ];
    }
}
