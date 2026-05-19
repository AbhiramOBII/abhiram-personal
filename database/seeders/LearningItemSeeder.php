<?php

namespace Database\Seeders;

use App\Models\LearningItem;
use App\Models\SkillDomain;
use Illuminate\Database\Seeder;

class LearningItemSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Founder Branding & Podcast Authority' => [
                ['title' => 'Define Your Founder Positioning Statement', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 10, 'notes' => 'Write a 3-sentence founder bio for LinkedIn, podcast intro, and BNI intro. Refine until it feels 100% true.'],
                ['title' => 'StoryBrand Framework — Donald Miller (Book)', 'type' => 'book', 'source_url' => 'https://buildingastorybrand.com', 'estimated_hours' => 5.0, 'priority' => 9, 'notes' => 'Read chapters 1–5 first. Apply the SB7 framework to your podcast brand and Obii Kriationz immediately after reading.'],
                ['title' => 'How to Build a Personal Brand as a Founder — Dickie Bush (YouTube)', 'type' => 'video', 'source_url' => 'https://www.youtube.com/@DickieBush', 'estimated_hours' => 1.5, 'priority' => 8, 'notes' => 'Focus on the content flywheel model — podcast → clips → LinkedIn posts → newsletter.'],
                ['title' => 'Record and self-review 3 podcast intros — identify verbal tics and energy level', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Record 3 versions of your opening 60 seconds. Note pace, energy, clarity of hook.'],
                ['title' => 'Podcast Growth Masterclass — Buzzsprout Academy', 'type' => 'course', 'source_url' => 'https://www.buzzsprout.com/blog', 'estimated_hours' => 3.0, 'priority' => 7, 'notes' => 'Focus on episode titles that rank, show notes SEO, and guest outreach templates.'],
                ['title' => 'Design your Podcast Content Calendar for 45 days', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 10, 'notes' => 'Map episode themes to your 7 day themes. Saturday = Podcast Day — use that block for recordings.'],
            ],
            'Sales Closure & Revenue Discipline' => [
                ['title' => 'Never Split the Difference — Chris Voss (Book)', 'type' => 'book', 'source_url' => 'https://www.blackswanltd.com/never-split-the-difference', 'estimated_hours' => 5.0, 'priority' => 10, 'notes' => 'Master tactical empathy and mirroring. Practice mirroring in your next 3 client calls.'],
                ['title' => 'Build your personal Sales Script — Discovery → Proposal → Close', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 10, 'notes' => 'Stage 1: pain discovery questions. Stage 2: solution framing. Stage 3: close with two options, never one.'],
                ['title' => '$100M Offers — Alex Hormozi (Book)', 'type' => 'book', 'source_url' => 'https://www.acquisition.com/books', 'estimated_hours' => 4.0, 'priority' => 10, 'notes' => 'Apply the Grand Slam Offer formula to Obii Kriationz service packages immediately.'],
                ['title' => 'The Challenger Sale — Matthew Dixon (Key chapters)', 'type' => 'book', 'source_url' => 'https://www.challengerinc.com', 'estimated_hours' => 3.0, 'priority' => 8, 'notes' => 'Teach clients something new rather than just responding to stated needs. Relevant for BNI and agency pitches.'],
                ['title' => 'Run a Revenue Audit — map every income stream, identify top 20% driving 80% revenue', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'List every client/project/product. Mark revenue, hours, energy cost. Double down on best revenue-per-hour.'],
                ['title' => 'Build a 5-touch follow-up sequence for cold and warm leads', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Day 1: pitch. Day 3: value add. Day 7: case study. Day 14: check-in. Day 21: break-up email.'],
            ],
            'Product Management & SaaS Thinking' => [
                ['title' => 'Continuous Discovery Habits — Teresa Torres (Book)', 'type' => 'book', 'source_url' => 'https://www.producttalk.org/2021/05/continuous-discovery-habits', 'estimated_hours' => 5.0, 'priority' => 10, 'notes' => 'Apply opportunity solution trees to DayOS — map user outcomes and solutions before building any new feature.'],
                ['title' => 'Shape Up — Ryan Singer / Basecamp (Free online book)', 'type' => 'book', 'source_url' => 'https://basecamp.com/shapeup', 'estimated_hours' => 4.0, 'priority' => 9, 'notes' => 'Build cycles, pitches, and appetite — better than Scrum for a solo/small team SaaS build.'],
                ['title' => 'Write 3 Product Pitches using Shape Up format for DayOS next features', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 9, 'notes' => 'Pick 3 upcoming modules. Write a pitch for each: problem, appetite, solution sketch, rabbit holes, no-gos.'],
                ['title' => 'Lenny\'s Newsletter — read last 8 issues on retention and activation', 'type' => 'article', 'source_url' => 'https://www.lennysnewsletter.com', 'estimated_hours' => 3.0, 'priority' => 8, 'notes' => 'Focus on activation metrics and habit loop design in products.'],
                ['title' => 'Map the full DayOS user journey — first login to power user state', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 10, 'notes' => 'Onboarding → first plan → first task → first practice streak → weekly review. Identify drop-off points.'],
                ['title' => 'SaaS Pricing Strategy — Profitwell / PriceIntelligently free guides', 'type' => 'article', 'source_url' => 'https://www.profitwell.com/recur/all/saas-pricing', 'estimated_hours' => 2.0, 'priority' => 7, 'notes' => 'Value-based vs feature-based pricing. Decide DayOS tiers early — free/solo/team.'],
            ],
            'Digital Marketing Strategy' => [
                ['title' => 'Build a 45-Day Content Distribution System — one podcast episode into 7 content pieces', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 10, 'notes' => 'Episode → transcript → blog → 3 LinkedIn posts → 2 Reels → 1 email. Build once, run every week.'],
                ['title' => 'This Is Marketing — Seth Godin (Book)', 'type' => 'book', 'source_url' => 'https://seths.blog/tim', 'estimated_hours' => 4.0, 'priority' => 9, 'notes' => 'The smallest viable audience concept. Applies directly to your podcast and Obii Kriationz positioning.'],
                ['title' => 'Google Search Ads — Skillshop Certification (Free)', 'type' => 'course', 'source_url' => 'https://skillshop.withgoogle.com', 'estimated_hours' => 6.0, 'priority' => 8, 'notes' => 'Get certified. Directly applicable to client campaigns at Obii Kriationz. Validates expertise for BNI referrals.'],
                ['title' => 'LinkedIn Personal Brand Audit — optimize profile, post 3 founder-led posts', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Headline, banner, about, featured section. Use your founder positioning statement. Post: insight, story, opinion.'],
                ['title' => 'Build a Marketing Funnel Audit Template for Obii Kriationz client onboarding', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 8, 'notes' => 'Repeatable audit document for every new client. Covers awareness, consideration, conversion, retention gaps.'],
                ['title' => 'Email Marketing Mastery — ConvertKit free course', 'type' => 'course', 'source_url' => 'https://convertkit.com/resources', 'estimated_hours' => 3.0, 'priority' => 7, 'notes' => 'Build your first sequence: welcome → value → pitch → community. 200 subscribers compound over time.'],
            ],
            'AI & LLM Product Building' => [
                ['title' => 'OpenAI API Quickstart — build a working Laravel integration in one session', 'type' => 'experiment', 'source_url' => 'https://platform.openai.com/docs/quickstart', 'estimated_hours' => 3.0, 'priority' => 10, 'notes' => 'Get GPT-4o-mini responding inside a Laravel controller. Foundation for DayOS Module 11 AI layer.'],
                ['title' => 'Prompt Engineering Guide — DAIR.AI (Free)', 'type' => 'article', 'source_url' => 'https://www.promptingguide.ai', 'estimated_hours' => 4.0, 'priority' => 9, 'notes' => 'Learn chain-of-thought, few-shot, role prompting, structured output. Write 10 prompts for DayOS AI briefing.'],
                ['title' => 'Build a Daily AI Briefing prototype for DayOS — 5-sentence morning summary', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 4.0, 'priority' => 10, 'notes' => 'Feed: today\'s theme, task count, rollover count, practice streaks → GPT returns motivational briefing.'],
                ['title' => 'openai-php/laravel — install and build a DayOS AI service class', 'type' => 'experiment', 'source_url' => 'https://github.com/openai-php/laravel', 'estimated_hours' => 2.0, 'priority' => 8, 'notes' => 'Wrap common DayOS prompts in a clean service class. Keeps AI calls testable and maintainable.'],
                ['title' => 'Andrej Karpathy — Intro to Large Language Models (YouTube, 1hr)', 'type' => 'video', 'source_url' => 'https://www.youtube.com/watch?v=zjkBMFhNj_g', 'estimated_hours' => 1.5, 'priority' => 7, 'notes' => 'Best non-technical LLM explainer. Gives you vocabulary to talk intelligently with AI engineers.'],
                ['title' => 'Build an AI task suggestion feature for DayOS — suggest what to do next based on theme', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 9, 'notes' => 'GPT-4o-mini with today\'s context as system prompt. Return top 3 suggested tasks with reasoning.'],
            ],
            'Team Leadership & Delegation' => [
                ['title' => 'The E-Myth Revisited — Michael Gerber (Book)', 'type' => 'book', 'source_url' => 'https://www.e-myth.com', 'estimated_hours' => 4.0, 'priority' => 10, 'notes' => 'Work ON the business, not just IN it. Read before hiring anyone new.'],
                ['title' => 'Build a Team Responsibility Matrix (RACI) for Obii Kriationz', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 10, 'notes' => 'List every recurring task. Assign Responsible, Accountable, Consulted, Informed. Reveals what only you can do.'],
                ['title' => 'High Output Management — Andy Grove (Key chapters)', 'type' => 'book', 'source_url' => null, 'estimated_hours' => 4.0, 'priority' => 8, 'notes' => '1-on-1 structure, leverage points, training as highest ROI activity.'],
                ['title' => 'Write SOPs for your top 5 most repeated business tasks', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 4.0, 'priority' => 9, 'notes' => 'Client onboarding, project delivery, social media, invoice follow-up, podcast production. Each = Loom video + checklist.'],
                ['title' => 'Radical Candor — Kim Scott (Chapters 1–4)', 'type' => 'book', 'source_url' => 'https://www.radicalcandor.com', 'estimated_hours' => 3.0, 'priority' => 7, 'notes' => 'Care personally, challenge directly. Applicable to giving feedback to creative team members.'],
                ['title' => 'Design a Weekly Team Rhythm — standup format, async updates, review cadence', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 1.5, 'priority' => 8, 'notes' => 'Monday: align. Daily: async update. Friday: wins + blockers. Prevents ad-hoc status chasing.'],
            ],
            'Finance & Cash Flow Management' => [
                ['title' => 'Profit First — Mike Michalowicz (Book)', 'type' => 'book', 'source_url' => 'https://mikemichalowicz.com/profit-first', 'estimated_hours' => 4.0, 'priority' => 10, 'notes' => 'Set up 5 bank accounts: Income, Profit, Owner Pay, Tax, Opex. Allocate percentages on every payment received.'],
                ['title' => 'Build a Monthly P&L Tracker for Obii Kriationz in Google Sheets', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 10, 'notes' => 'Revenue by client, fixed costs, variable costs, net profit, owner salary. Run on the 1st every month.'],
                ['title' => 'Zerodha Varsity — Financial Statements module (Free)', 'type' => 'course', 'source_url' => 'https://zerodha.com/varsity/module/financial-statement-analysis/', 'estimated_hours' => 3.0, 'priority' => 8, 'notes' => 'Learn to read a P&L, balance sheet, and cash flow statement. Prevents expensive surprises.'],
                ['title' => 'Set up a 12-Week Cash Flow Forecast for Obii Kriationz', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Week by week: expected income, expected expenses, net position. Shows danger months 90 days ahead.'],
                ['title' => 'Tax Planning for Indian Agencies — ClearTax guides', 'type' => 'article', 'source_url' => 'https://cleartax.in/s/tax-for-freelancers', 'estimated_hours' => 2.0, 'priority' => 7, 'notes' => 'GST thresholds, advance tax, presumptive taxation under 44ADA. Do not leave this to panic in March.'],
                ['title' => 'Redesign your service pricing using value-based pricing — retire hourly billing', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Price at 10–20% of value delivered to the client. Write new pricing page for Obii Kriationz.'],
            ],
            'Public Speaking, Networking & BNI Influence' => [
                ['title' => 'Write and rehearse a 60-second BNI commercial — new version every 2 weeks', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 1.5, 'priority' => 10, 'notes' => 'Hook → problem you solve → ideal referral → memory hook. Record yourself. Tighten every word.'],
                ['title' => 'Talk Like TED — Carmine Gallo (Book)', 'type' => 'book', 'source_url' => 'https://www.carminegallo.com/books/talk-like-ted', 'estimated_hours' => 4.0, 'priority' => 9, 'notes' => 'Three secrets: emotional, novel, memorable. Applicable to BNI featured speaker slots and podcast hosting.'],
                ['title' => 'Build your BNI Referral Partner Target List — top 10 categories that send you work', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 1.5, 'priority' => 10, 'notes' => 'Who in BNI has clients needing websites, digital marketing, branding? Schedule 1-2-1s. Give referrals first.'],
                ['title' => 'Toastmasters Pathways — complete 5 prepared speeches', 'type' => 'experiment', 'source_url' => 'https://www.toastmasters.org', 'estimated_hours' => 8.0, 'priority' => 7, 'notes' => 'Record 5 speeches at home to the Pathways brief and self-evaluate. Repetition is the only path to fluency.'],
                ['title' => 'Never Eat Alone — Keith Ferrazzi (Key chapters)', 'type' => 'book', 'source_url' => null, 'estimated_hours' => 3.5, 'priority' => 8, 'notes' => 'Generosity as networking strategy. Give before you ask. Deepen BNI relationships beyond transactional exchange.'],
                ['title' => 'Design a 45-Day BNI Influence Plan — 1-2-1s, referrals given, featured speaker goal', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 1.5, 'priority' => 9, 'notes' => 'Targets: 2 one-2-ones per week, 4 referrals in 45 days, 1 featured speaker slot booked. Track in DayOS.'],
            ],
            'Content Systems & Personal Knowledge Management' => [
                ['title' => 'Building a Second Brain — Tiago Forte (Book)', 'type' => 'book', 'source_url' => 'https://www.buildingasecondbrain.com', 'estimated_hours' => 5.0, 'priority' => 10, 'notes' => 'PARA system: Projects, Areas, Resources, Archive. Set up in Notion. Connect the system to DayOS.'],
                ['title' => 'Set up PARA in Notion for Obii Kriationz + Personal life', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 3.0, 'priority' => 10, 'notes' => '4 top-level databases: Projects (active), Areas (responsibilities), Resources (reference), Archive. Migrate existing notes.'],
                ['title' => 'Build a Content Capture System — one inbox for all ideas', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Every idea goes into one place immediately — Notion inbox, voice note, or Telegram saved messages. Review weekly.'],
                ['title' => 'Write 5 Atomic Essays this week — Ship 30 for 30 method', 'type' => 'experiment', 'source_url' => 'https://www.ship30for30.com', 'estimated_hours' => 3.0, 'priority' => 8, 'notes' => '250 words, one idea, written in 30 minutes. Post on LinkedIn. Builds thought leadership content engine.'],
                ['title' => 'Design a Weekly Content System — creation to distribution workflow', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 9, 'notes' => 'Wednesday: create. Friday: shoot. Saturday: podcast. Content produced once, distributed everywhere.'],
                ['title' => 'Obsidian for Networked Thinking — set up a personal knowledge vault', 'type' => 'experiment', 'source_url' => 'https://obsidian.md', 'estimated_hours' => 2.0, 'priority' => 7, 'notes' => 'Link ideas across podcasts, books, and conversations. Graph view reveals surprising connections over time.'],
            ],
            'Spiritual Discipline & Astrological Self-Alignment' => [
                ['title' => 'Design a Morning Silence Practice — 10 minutes of no-input stillness', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 0.5, 'priority' => 10, 'notes' => 'Before the morning stack — just sit. Hear your own thoughts before the world fills them. Do this 21 days straight.'],
                ['title' => 'The Bhagavad Gita — Eknath Easwaran translation (Book)', 'type' => 'book', 'source_url' => 'https://www.easwaran.org/the-bhagavad-gita.html', 'estimated_hours' => 6.0, 'priority' => 9, 'notes' => 'One chapter per day. Do your duty without attachment to results — the ultimate antidote to founder anxiety.'],
                ['title' => 'Map your Vedic birth chart — Lagna, Moon sign, and current Dasha period', 'type' => 'experiment', 'source_url' => 'https://www.astrosage.com/free-kundli.asp', 'estimated_hours' => 2.0, 'priority' => 8, 'notes' => 'Generate your Kundli. Identify Mahadasha and Antardasha. Use as directional guidance, not determinism.'],
                ['title' => 'Miracle Morning — Hal Elrod (Book)', 'type' => 'book', 'source_url' => 'https://miraclemorning.com', 'estimated_hours' => 3.0, 'priority' => 7, 'notes' => 'SAVERS: Silence, Affirmations, Visualization, Exercise, Reading, Scribing. Design a 20-minute version for 7AM.'],
                ['title' => 'Write your Personal Mission Statement', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 2.0, 'priority' => 10, 'notes' => 'One paragraph. Read every Sunday during Vision Block. Update quarterly. Your north star when decisions get hard.'],
                ['title' => 'Design a Weekly Spiritual Anchor — one practice per day\'s theme energy', 'type' => 'experiment', 'source_url' => null, 'estimated_hours' => 1.5, 'priority' => 8, 'notes' => 'Sunday: journaling. Monday: intention. Wednesday: deep silence. Saturday: gratitude. Tied to DayOS day themes.'],
            ],
        ];

        foreach ($data as $domainName => $items) {
            $domain = SkillDomain::where('name', $domainName)->first();
            if (!$domain) {
                continue;
            }

            foreach ($items as $index => $item) {
                LearningItem::updateOrCreate(
                    ['skill_domain_id' => $domain->id, 'title' => $item['title']],
                    array_merge($item, [
                        'skill_domain_id' => $domain->id,
                        'sort_order' => $index,
                    ])
                );
            }
        }
    }
}
