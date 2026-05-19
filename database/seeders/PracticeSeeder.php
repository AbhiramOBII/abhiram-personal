<?php

namespace Database\Seeders;

use App\Models\Practice;
use Illuminate\Database\Seeder;

class PracticeSeeder extends Seeder
{
    public function run(): void
    {
        $practices = [
            // ─── Morning Stack (1–5) ───
            [
                'name' => 'Morning Intention',
                'cue' => 'Right after waking up',
                'reward' => 'Start the day with direction, not drift',
                'identity_statement' => 'I am someone who designs my day before it designs me',
                'two_minute_version' => 'Write one sentence about what winning today looks like',
                'pillar' => 'vision',
                'hex_color' => '#4f98a3',
                'icon_emoji' => '🌅',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Hydrate First',
                'cue' => 'Right after Morning Intention',
                'reward' => 'Body awake, mind clear',
                'identity_statement' => 'I am someone who fuels their body before fueling their phone',
                'two_minute_version' => 'Drink one glass of water',
                'pillar' => 'health',
                'hex_color' => '#5591c7',
                'icon_emoji' => '💧',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Morning Intention',
                'stack_trigger' => 'After Morning Intention',
                'is_two_minute_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Review Today\'s Theme',
                'cue' => 'After Hydrate First',
                'reward' => 'Know the day\'s energy and intent before touching work',
                'identity_statement' => 'I am someone who works with intention, not reaction',
                'two_minute_version' => 'Read today\'s theme name and one pillar aloud',
                'pillar' => 'vision',
                'hex_color' => '#a86fdf',
                'icon_emoji' => '🗓️',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Hydrate First',
                'stack_trigger' => 'After Hydrate First',
                'is_two_minute_enabled' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Movement',
                'cue' => 'Before the first work block',
                'reward' => 'Energy and focus for deep work',
                'identity_statement' => 'I am someone who moves their body every single day',
                'two_minute_version' => '10 jumping jacks or a 2-minute walk',
                'pillar' => 'health',
                'hex_color' => '#6daa45',
                'icon_emoji' => '🏃',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Review Today\'s Theme',
                'stack_trigger' => 'After Review Today\'s Theme',
                'is_two_minute_enabled' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Set Focus Intention',
                'cue' => 'Before opening any work app',
                'reward' => 'One clear target for the day',
                'identity_statement' => 'I am someone who finishes what they start',
                'two_minute_version' => 'Type one sentence in the Focus Intention field',
                'pillar' => 'vision',
                'hex_color' => '#e8af34',
                'icon_emoji' => '🎯',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Movement',
                'stack_trigger' => 'After Movement',
                'is_two_minute_enabled' => true,
                'sort_order' => 5,
            ],

            // ─── Work Block Practices (6–8) ───
            [
                'name' => 'BNI Prep',
                'cue' => 'Every Thursday morning before the meeting',
                'reward' => 'Walk in confident, not caught off guard',
                'identity_statement' => 'I am someone who respects other people\'s time by preparing well',
                'two_minute_version' => 'Read the member names and your referral update',
                'pillar' => 'networking',
                'hex_color' => '#5591c7',
                'icon_emoji' => '🤝',
                'frequency_type' => 'specific_days',
                'frequency_days' => [4], // Thursday
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Content Idea Capture',
                'cue' => 'During or after any creative session',
                'reward' => 'Never lose a good idea again',
                'identity_statement' => 'I am someone who captures sparks before they fade',
                'two_minute_version' => 'Write one headline or topic in the notes',
                'pillar' => 'content',
                'hex_color' => '#fdab43',
                'icon_emoji' => '💡',
                'frequency_type' => 'specific_days',
                'frequency_days' => [1, 2, 3, 4, 5], // Mon–Fri
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Upskill Session',
                'cue' => 'During Morning Free Zone or Evening Free Zone',
                'reward' => '1% better every day compounds into mastery',
                'identity_statement' => 'I am someone who invests in their own growth daily',
                'two_minute_version' => 'Watch 2 minutes of the current course video',
                'pillar' => 'learning',
                'hex_color' => '#a86fdf',
                'icon_emoji' => '🧠',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 8,
            ],

            // ─── Evening Stack (9–11) ───
            [
                'name' => 'Work Block Close',
                'cue' => 'At the end of Work Block 2 (6 PM)',
                'reward' => 'Clean mental shutdown — work stays at work',
                'identity_statement' => 'I am someone who finishes strong and clocks out completely',
                'two_minute_version' => 'Write one thing completed today',
                'pillar' => 'operations',
                'hex_color' => '#e8af34',
                'icon_emoji' => '🔒',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'Tomorrow Prep',
                'cue' => 'During Night Session (9 PM)',
                'reward' => 'Wake up knowing exactly what to do',
                'identity_statement' => 'I am someone who never starts a day from zero',
                'two_minute_version' => 'Write 3 tasks for tomorrow',
                'pillar' => 'vision',
                'hex_color' => '#4f98a3',
                'icon_emoji' => '📋',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Work Block Close',
                'stack_trigger' => 'After Work Block Close',
                'is_two_minute_enabled' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Gratitude Log',
                'cue' => 'Right before closing the app at night',
                'reward' => 'Sleep with a full heart, not a busy mind',
                'identity_statement' => 'I am someone who finds the good in every day',
                'two_minute_version' => 'Name one good thing that happened today',
                'pillar' => 'recovery',
                'hex_color' => '#6daa45',
                'icon_emoji' => '🙏',
                'frequency_type' => 'daily',
                'frequency_days' => null,
                'stack_after' => 'Tomorrow Prep',
                'stack_trigger' => 'After Tomorrow Prep',
                'is_two_minute_enabled' => true,
                'sort_order' => 11,
            ],

            // ─── Weekly Practices (12–14) ───
            [
                'name' => 'Weekly Review',
                'cue' => 'Every Sunday morning during Vision Block',
                'reward' => 'See the full picture, course correct early',
                'identity_statement' => 'I am someone who learns from every week, not just survives it',
                'two_minute_version' => 'Answer one question: what was this week\'s biggest win?',
                'pillar' => 'vision',
                'hex_color' => '#4f98a3',
                'icon_emoji' => '🔭',
                'frequency_type' => 'specific_days',
                'frequency_days' => [0], // Sunday
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Podcast Prep',
                'cue' => 'Every Saturday morning before recording',
                'reward' => 'A tight, valuable episode every time',
                'identity_statement' => 'I am someone who respects their audience by showing up prepared',
                'two_minute_version' => 'Read the guest bio and write one key question',
                'pillar' => 'podcast',
                'hex_color' => '#6daa45',
                'icon_emoji' => '🎙️',
                'frequency_type' => 'specific_days',
                'frequency_days' => [6], // Saturday
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Referral Follow-Up',
                'cue' => 'Every Thursday after BNI',
                'reward' => 'Referrals that actually close',
                'identity_statement' => 'I am someone who follows through every single time',
                'two_minute_version' => 'Send one WhatsApp check-in to a pending referral',
                'pillar' => 'networking',
                'hex_color' => '#5591c7',
                'icon_emoji' => '📞',
                'frequency_type' => 'specific_days',
                'frequency_days' => [4], // Thursday
                'stack_after' => null,
                'stack_trigger' => null,
                'is_two_minute_enabled' => true,
                'sort_order' => 14,
            ],
        ];

        foreach ($practices as $data) {
            $stackAfterName = $data['stack_after'];
            unset($data['stack_after']);

            $data['stack_after_practice_id'] = $stackAfterName
                ? Practice::where('name', $stackAfterName)->value('id')
                : null;

            $data['is_active'] = true;

            Practice::updateOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
