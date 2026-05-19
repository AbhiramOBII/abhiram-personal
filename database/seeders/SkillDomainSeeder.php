<?php

namespace Database\Seeders;

use App\Models\SkillDomain;
use Illuminate\Database\Seeder;

class SkillDomainSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            ['name' => 'Full-Stack Laravel & Architecture', 'icon_emoji' => '⚙️', 'hex_color' => '#4f98a3', 'current_level' => 9, 'target_level' => 10, 'sort_order' => 1],
            ['name' => 'Founder Branding & Podcast Authority', 'icon_emoji' => '�️', 'hex_color' => '#8e44ad', 'current_level' => 7, 'target_level' => 10, 'sort_order' => 2],
            ['name' => 'Sales Closure & Revenue Discipline', 'icon_emoji' => '💰', 'hex_color' => '#27ae60', 'current_level' => 6, 'target_level' => 10, 'sort_order' => 3],
            ['name' => 'Product Management & SaaS Thinking', 'icon_emoji' => '🧩', 'hex_color' => '#2980b9', 'current_level' => 8, 'target_level' => 10, 'sort_order' => 4],
            ['name' => 'Digital Marketing Strategy', 'icon_emoji' => '�', 'hex_color' => '#e67e22', 'current_level' => 8, 'target_level' => 10, 'sort_order' => 5],
            ['name' => 'AI & LLM Product Building', 'icon_emoji' => '🤖', 'hex_color' => '#16a085', 'current_level' => 6, 'target_level' => 10, 'sort_order' => 6],
            ['name' => 'Team Leadership & Delegation', 'icon_emoji' => '🧭', 'hex_color' => '#34495e', 'current_level' => 6, 'target_level' => 10, 'sort_order' => 7],
            ['name' => 'Finance & Cash Flow Management', 'icon_emoji' => '�', 'hex_color' => '#c0392b', 'current_level' => 5, 'target_level' => 9, 'sort_order' => 8],
            ['name' => 'Public Speaking, Networking & BNI Influence', 'icon_emoji' => '🤝', 'hex_color' => '#f39c12', 'current_level' => 8, 'target_level' => 10, 'sort_order' => 9],
            ['name' => 'Content Systems & Personal Knowledge Management', 'icon_emoji' => '🗂️', 'hex_color' => '#9b59b6', 'current_level' => 6, 'target_level' => 9, 'sort_order' => 10],
            ['name' => 'Spiritual Discipline & Astrological Self-Alignment', 'icon_emoji' => '�', 'hex_color' => '#6c5ce7', 'current_level' => 7, 'target_level' => 9, 'sort_order' => 11],
        ];

        foreach ($domains as $domain) {
            SkillDomain::updateOrCreate(
                ['name' => $domain['name']],
                $domain
            );
        }
    }
}
