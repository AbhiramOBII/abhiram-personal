<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use App\Models\WorkingDay;
use Illuminate\Database\Seeder;

class TimeSlotsSeeder extends Seeder
{
    public function run(): void
    {
        $schedule = [
            'Monday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '08:00', 'label' => 'Exercise', 'p' => 'Personal'],
                ['t1' => '08:00', 't2' => '09:30', 'label' => 'Family Time', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '10:00', 'label' => 'Bath + Breakfast + Daily Planning (3 Must-Dos)', 'p' => 'Planning'],
                ['t1' => '10:00', 't2' => '10:30', 'label' => 'Travel / Settle In + Morning Scan', 'p' => 'Admin'],
                ['t1' => '10:30', 't2' => '10:50', 'label' => 'Weekly Cash Flow Check + Outstanding Invoices', 'p' => 'Services'],
                ['t1' => '10:50', 't2' => '12:30', 'label' => 'Client Delivery — SETU / Kavya / Active Projects', 'p' => 'Services'],
                ['t1' => '12:30', 't2' => '13:30', 'label' => 'Proposal Writing / Milestone Documentation', 'p' => 'Services'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Lunch Reset (No Phone)', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '15:30', 'label' => 'Team Syncs / Client Review Calls (Max 2)', 'p' => 'Services'],
                ['t1' => '15:30', 't2' => '16:30', 'label' => 'PM Review — Update Project Trackers', 'p' => 'Services'],
                ['t1' => '16:30', 't2' => '17:00', 'label' => 'Follow-Up Queue — Emails, Pending Decisions', 'p' => 'Services'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'Services Team Catch-Up — Delivery, Blockers', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — WhatsApp, Invoices, Approvals, Admin', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:00', 'label' => 'Weekly Strategic Plan — What Must Ship This Week?', 'p' => 'Strategy'],
                ['t1' => '22:00', 't2' => '23:00', 'label' => 'Planning + Calendar Alignment for Next Day', 'p' => 'Strategy'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Tuesday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '08:00', 'label' => 'Exercise', 'p' => 'Personal'],
                ['t1' => '08:00', 't2' => '09:30', 'label' => 'Family Time', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '10:00', 'label' => 'Bath + Breakfast + Daily Planning', 'p' => 'Planning'],
                ['t1' => '10:00', 't2' => '10:30', 'label' => 'Travel / Settle In + Morning Scan', 'p' => 'Admin'],
                ['t1' => '10:30', 't2' => '12:00', 'label' => 'Funnel Building / Webinar Scripting / Offer Positioning', 'p' => 'ObiiNxt'],
                ['t1' => '12:00', 't2' => '13:00', 'label' => 'Lead Magnet Creation / Landing Page Copy', 'p' => 'ObiiNxt'],
                ['t1' => '13:00', 't2' => '13:30', 'label' => 'LinkedIn Draft — Founder Positioning Post', 'p' => 'Founder-Brand'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Lunch Reset (No Phone)', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '15:00', 'label' => 'Ad Review / WhatsApp Campaign Coordination', 'p' => 'ObiiNxt'],
                ['t1' => '15:00', 't2' => '16:30', 'label' => 'CRM Follow-Up — Warm Leads, Pipeline Movement', 'p' => 'ObiiNxt'],
                ['t1' => '16:30', 't2' => '17:00', 'label' => 'Schedule / Queue This Week\'s Posts', 'p' => 'Founder-Brand'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'ObiiNxt Team Catch-Up — Funnel, Campaign, Leads', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — WhatsApp, Invoices, Admin', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:30', 'label' => 'Write 1 Thought Leadership Post / Email Thread', 'p' => 'Founder-Brand'],
                ['t1' => '22:30', 't2' => '23:00', 'label' => 'Refine Core Offer Messaging', 'p' => 'ObiiNxt'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Wednesday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '08:00', 'label' => 'Exercise', 'p' => 'Personal'],
                ['t1' => '08:00', 't2' => '09:30', 'label' => 'Family Time', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '10:00', 'label' => 'Bath + Breakfast + Daily Planning', 'p' => 'Planning'],
                ['t1' => '10:00', 't2' => '10:30', 'label' => 'Travel / Settle In + Morning Scan', 'p' => 'Admin'],
                ['t1' => '10:30', 't2' => '12:30', 'label' => 'Build 1 SOP / Template / Reusable Framework', 'p' => 'Systems'],
                ['t1' => '12:30', 't2' => '13:30', 'label' => 'Course Curriculum / ObiiNxt Product Structure Work', 'p' => 'ObiiNxt'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Lunch Reset (No Phone)', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '15:30', 'label' => 'Internal Tooling / Automation / Documentation', 'p' => 'Systems'],
                ['t1' => '15:30', 't2' => '17:00', 'label' => 'Fix 1 Broken System — Onboarding, Billing, Delivery', 'p' => 'Systems'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'Systems/Ops Catch-Up — SOP Handoffs, Docs Progress', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — WhatsApp, Invoices, Admin', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:30', 'label' => 'Reading / Research / Industry Trend Study', 'p' => 'Learning'],
                ['t1' => '22:30', 't2' => '23:00', 'label' => 'Ideation — No Execution, Only Inputs', 'p' => 'Strategy'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Thursday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '11:00', 'label' => 'BNI Meeting (Off-Site)', 'p' => 'Networking'],
                ['t1' => '11:00', 't2' => '11:30', 'label' => 'BNI Debrief + New Contacts Logged (CRM / Notes)', 'p' => 'Networking'],
                ['t1' => '11:30', 't2' => '12:30', 'label' => '1-2-1 Meeting #1 — BNI Member / Strategic Contact', 'p' => 'Networking'],
                ['t1' => '12:30', 't2' => '13:30', 'label' => 'Lunch + New Contact Follow-Up (WhatsApp / LinkedIn)', 'p' => 'Networking'],
                ['t1' => '13:30', 't2' => '14:30', 'label' => '1-2-1 Meeting #2 — Partnership / Collaboration', 'p' => 'Networking'],
                ['t1' => '14:30', 't2' => '15:30', 'label' => 'Podcast Guest Pre-Call / Collaboration Discussion', 'p' => 'Founder-Brand'],
                ['t1' => '15:30', 't2' => '16:00', 'label' => 'LinkedIn Outreach — 5 Personalized Messages', 'p' => 'Founder-Brand'],
                ['t1' => '16:00', 't2' => '16:30', 'label' => 'Travel Back to Office', 'p' => 'Personal'],
                ['t1' => '16:30', 't2' => '17:00', 'label' => 'Settle In — Quick Email + Message Scan', 'p' => 'Admin'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'Founder Brand Catch-Up — Podcast, Content Pipeline', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — WhatsApp, Invoices, Approvals', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:30', 'label' => 'Relationship Nurturing — Voice Notes, Replies, Check-ins', 'p' => 'Networking'],
                ['t1' => '22:30', 't2' => '23:00', 'label' => 'Log 3 People to Deepen Relationship With Next Week', 'p' => 'Strategy'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Friday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '08:00', 'label' => 'Exercise', 'p' => 'Personal'],
                ['t1' => '08:00', 't2' => '09:30', 'label' => 'Family Time', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '10:00', 'label' => 'Bath + Breakfast + Daily Planning', 'p' => 'Planning'],
                ['t1' => '10:00', 't2' => '10:30', 'label' => 'Travel / Settle In + Morning Scan', 'p' => 'Admin'],
                ['t1' => '10:30', 't2' => '11:30', 'label' => 'Script 2-3 Reels + 1 Long-Form Piece', 'p' => 'Founder-Brand'],
                ['t1' => '11:30', 't2' => '12:00', 'label' => 'Shot List + Setup — Props, Background, Lighting', 'p' => 'Founder-Brand'],
                ['t1' => '12:00', 't2' => '13:30', 'label' => 'Shoot — Reels, Talking Head Videos, Brand Assets', 'p' => 'Founder-Brand'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Lunch Reset (No Phone)', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '16:00', 'label' => 'Podcast Episode Shoot', 'p' => 'Founder-Brand'],
                ['t1' => '16:00', 't2' => '17:00', 'label' => 'Review Raw Footage, Hand Off to Editor, Label Files', 'p' => 'Founder-Brand'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'Production Debrief — Shoot Output, Editor Handoff Check', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — WhatsApp, Invoices, Admin', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:00', 'label' => 'Content Review — Captions, Thumbnails, Titles', 'p' => 'Founder-Brand'],
                ['t1' => '22:00', 't2' => '23:00', 'label' => 'Queue Next Week\'s Content in Scheduler', 'p' => 'Founder-Brand'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Saturday' => [
                ['t1' => '07:00', 't2' => '07:30', 'label' => 'Wake Up + Ablutions', 'p' => 'Personal'],
                ['t1' => '07:30', 't2' => '08:00', 'label' => 'Exercise', 'p' => 'Personal'],
                ['t1' => '08:00', 't2' => '09:30', 'label' => 'Family Time', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '10:00', 'label' => 'Bath + Breakfast + Planning', 'p' => 'Planning'],
                ['t1' => '10:00', 't2' => '10:30', 'label' => 'Travel / Settle In + Morning Scan', 'p' => 'Admin'],
                ['t1' => '10:30', 't2' => '13:30', 'label' => 'Podcast Recording / Guest Interviews', 'p' => 'Founder-Brand'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Lunch Reset (No Phone)', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '16:00', 'label' => 'Webinar / Live Session / ObiiNxt Masterclass', 'p' => 'ObiiNxt'],
                ['t1' => '16:00', 't2' => '17:00', 'label' => 'Community DMs, Engagement, Responses', 'p' => 'ObiiNxt'],
                ['t1' => '17:00', 't2' => '17:30', 'label' => 'Community + Podcast Team Catch-Up', 'p' => 'Catch-Up'],
                ['t1' => '17:30', 't2' => '18:00', 'label' => 'Buffer — Admin, Approvals', 'p' => 'Admin'],
                ['t1' => '18:00', 't2' => '21:00', 'label' => 'Family Time (Phone on DND)', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:00', 'label' => 'Audience Insight Review — Questions, Comments, Trends', 'p' => 'Strategy'],
                ['t1' => '22:00', 't2' => '23:00', 'label' => 'Reflection Journal — What Worked, What Broke?', 'p' => 'Strategy'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
            'Sunday' => [
                ['t1' => '07:00', 't2' => '09:30', 'label' => 'Rest + Spiritual Reset + Family', 'p' => 'Personal'],
                ['t1' => '09:30', 't2' => '13:30', 'label' => 'Family Time — Zero Work', 'p' => 'Personal'],
                ['t1' => '13:30', 't2' => '14:00', 'label' => 'Light Lunch — No Screens', 'p' => 'Break'],
                ['t1' => '14:00', 't2' => '16:00', 'label' => 'Light Week Review + Vision Thinking + Journaling', 'p' => 'Strategy'],
                ['t1' => '16:00', 't2' => '21:00', 'label' => 'Family Time / Leisure', 'p' => 'Personal'],
                ['t1' => '21:00', 't2' => '22:00', 'label' => 'Weekly Planning — 3 Must-Dos Per Day Next Week', 'p' => 'Strategy'],
                ['t1' => '22:00', 't2' => '22:30', 'label' => 'Calendar Alignment + Confirm Shoots/Calls', 'p' => 'Planning'],
                ['t1' => '22:30', 't2' => '23:00', 'label' => 'CEO Check-In: What to Ship? Which Pillar Neglected? What to Remove?', 'p' => 'Strategy'],
                ['t1' => '23:00', 't2' => '23:30', 'label' => 'Wind Down + Sleep Prep', 'p' => 'Personal'],
            ],
        ];

        // Also update working day focus/description
        $focusMap = [
            'Monday'    => 'Client delivery, Services, PM reviews, Cash flow tracking',
            'Tuesday'   => 'ObiiNxt funnel, Webinar strategy, LinkedIn positioning, Ads & campaigns',
            'Wednesday' => 'SOPs & systems, Productization, Course curriculum, Automation',
            'Thursday'  => 'BNI off-site 7:30–11:00, 1-2-1 meetings, New contacts, Podcast guest outreach',
            'Friday'    => 'Content production, Podcast shoots, Reels & brand assets, ObiiNxt media',
            'Saturday'  => 'Business Giseness, Community, ObiiNxt engagement, Webinars/masterclasses',
            'Sunday'    => 'Rest & spiritual reset, Vision journaling, Light week review, CEO check-in',
        ];

        foreach ($schedule as $dayName => $blocks) {
            $day = WorkingDay::where('day_name', $dayName)->first();

            if (!$day) {
                continue;
            }

            // Update description with focus areas
            if (isset($focusMap[$dayName])) {
                $day->update(['description' => $focusMap[$dayName]]);
            }

            // Clear existing slots and re-seed
            $day->timeSlots()->delete();

            foreach ($blocks as $order => $block) {
                $day->timeSlots()->create([
                    'start_time'  => $block['t1'],
                    'end_time'    => $block['t2'],
                    'description' => $block['label'],
                    'pillar'      => $block['p'],
                    'sort_order'  => $order,
                ]);
            }
        }
    }
}
