<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Modern Minimalist',
                'slug' => 'modern-minimalist',
                'description' => 'Clean, modern design with lots of white space and bold typography.',
                'settings' => [
                    'primary_color' => '#2563eb',
                    'font_family' => 'Inter',
                    'layout' => 'single-column',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Classic Business',
                'slug' => 'classic-business',
                'description' => 'Traditional professional invoice with structured layout.',
                'settings' => [
                    'primary_color' => '#1e3a8a',
                    'font_family' => 'Times New Roman',
                    'layout' => 'two-column',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Creative Agency',
                'slug' => 'creative-agency',
                'description' => 'Bold and colorful design perfect for creative professionals.',
                'settings' => [
                    'primary_color' => '#7c3aed',
                    'font_family' => 'Poppins',
                    'layout' => 'asymmetric',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Corporate Blue',
                'slug' => 'corporate-blue',
                'description' => 'Professional blue-themed invoice for corporate environments.',
                'settings' => [
                    'primary_color' => '#0891b2',
                    'font_family' => 'Arial',
                    'layout' => 'two-column',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($templates as $template) {
            Template::create($template);
        }
    }
}
