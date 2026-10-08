<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'     => 'Removal',
                'slug'     => 'removal',
                'keywords' => ['removal', 'remov', 'removals'],
                'color'    => '#6366f1',
            ],
            [
                'name'     => 'Man & Van',
                'slug'     => 'man-van',
                'keywords' => ['manvan', 'man-van', 'man_van', 'vanman', 'van-man'],
                'color'    => '#f59e0b',
            ],
            [
                'name'     => 'Cleaning',
                'slug'     => 'cleaning',
                'keywords' => ['clean', 'cleaning', 'maid', 'housekeep', 'janitorial'],
                'color'    => '#10b981',
            ],
            [
                'name'     => 'Plumbing',
                'slug'     => 'plumbing',
                'keywords' => ['plumb', 'plumber', 'drain', 'boiler', 'heating'],
                'color'    => '#3b82f6',
            ],
            [
                'name'     => 'Electrical',
                'slug'     => 'electrical',
                'keywords' => ['electr', 'sparks', 'rewire', 'electrician'],
                'color'    => '#f97316',
            ],
            [
                'name'     => 'Roofing',
                'slug'     => 'roofing',
                'keywords' => ['roof', 'roofer', 'roofing', 'tiling'],
                'color'    => '#ef4444',
            ],
            [
                'name'     => 'Gardening',
                'slug'     => 'gardening',
                'keywords' => ['garden', 'landscap', 'lawn', 'hedge', 'gardening'],
                'color'    => '#22c55e',
            ],
            [
                'name'     => 'Skip Hire',
                'slug'     => 'skip-hire',
                'keywords' => ['skip', 'waste', 'rubbish', 'junk', 'recycl'],
                'color'    => '#8b5cf6',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name'     => $cat['name'],
                    'keywords' => $cat['keywords'],
                    'color'    => $cat['color'],
                    'active'   => true,
                ]
            );
        }
    }
}
