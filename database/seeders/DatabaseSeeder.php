<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Startup;
use App\Models\Inquiry;
use App\Models\Category;
use App\Models\Review;
use App\Models\Investment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ─────────────────────────────────────
        $cats = [
            ['name'=>'AI / ML',     'icon'=>'🤖','slug'=>'ai-ml'],
            ['name'=>'FinTech',     'icon'=>'💸','slug'=>'fintech'],
            ['name'=>'HealthTech',  'icon'=>'🏥','slug'=>'healthtech'],
            ['name'=>'EdTech',      'icon'=>'📚','slug'=>'edtech'],
            ['name'=>'E-commerce',  'icon'=>'🛒','slug'=>'ecommerce'],
            ['name'=>'Dev Tools',   'icon'=>'🛠️','slug'=>'devtools'],
        ];
        foreach ($cats as $cat) {
            Category::create($cat);
        }

        // ── Admin User ─────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@launchpad.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // ── Founders ───────────────────────────────────────
        $rohan = User::create([
            'name'=>'Rohan Verma','email'=>'rohan@verdant.io',
            'password'=>Hash::make('password'),'role'=>'founder',
        ]);
        $karan = User::create([
            'name'=>'Karan Singh','email'=>'karan@pocketledger.in',
            'password'=>Hash::make('password'),'role'=>'founder',
        ]);
        $priya = User::create([
            'name'=>'Priya Nair','email'=>'priya@healthloop.ai',
            'password'=>Hash::make('password'),'role'=>'founder',
        ]);

        // ── Investor ───────────────────────────────────────
        $investor = User::create([
            'name'=>'Arjun Mehta','email'=>'arjun@peakventures.in',
            'password'=>Hash::make('password'),'role'=>'investor',
        ]);

        // ── Startups ───────────────────────────────────────
        $aiCat     = Category::where('slug','ai-ml')->first();
        $finCat    = Category::where('slug','fintech')->first();
        $healthCat = Category::where('slug','healthtech')->first();
        $edCat     = Category::where('slug','edtech')->first();

        $verdant = Startup::create([
            'user_id'=>$rohan->id,'name'=>'Verdant Analytics',
            'tagline'=>'AI-powered supply chain intelligence for manufacturers',
            'description'=>'Verdant Analytics is a B2B SaaS platform using ML to predict supply chain disruptions. 48 paying customers with $7,083 ACV. NRR 118%.',
            'industry'=>'AI','stage'=>'Seed','arr'=>340000,
            'asking_price'=>1200000,'mrr_growth'=>'+18%',
            'status'=>'active','views'=>4823,'category_id'=>$aiCat->id,
            'website'=>'https://verdantanalytics.io',
        ]);

        $pocket = Startup::create([
            'user_id'=>$karan->id,'name'=>'PocketLedger',
            'tagline'=>'GST-compliant micro-accounting for freelancers',
            'description'=>'PocketLedger is a micro-accounting SaaS for freelancers. GST invoicing in 60 seconds. 1,200+ active subscribers.',
            'industry'=>'FinTech','stage'=>'Pre-Series A','arr'=>820000,
            'asking_price'=>3800000,'mrr_growth'=>'+22%',
            'status'=>'active','views'=>6210,'category_id'=>$finCat->id,
        ]);

        $health = Startup::create([
            'user_id'=>$priya->id,'name'=>'HealthLoop AI',
            'tagline'=>'Personalised preventive health backed by clinical data',
            'description'=>'HealthLoop AI builds personalised preventive health protocols using ML trained on clinical datasets.',
            'industry'=>'HealthTech','stage'=>'Seed','arr'=>180000,
            'asking_price'=>900000,'mrr_growth'=>'+31%',
            'status'=>'pending','views'=>3105,'category_id'=>$healthCat->id,
        ]);

        Startup::create([
            'user_id'=>$rohan->id,'name'=>'EduSpark',
            'tagline'=>'Adaptive K-12 learning engine powered by AI',
            'description'=>'EduSpark reduces teacher prep time by 40% through AI-generated lesson plans. Used in 120+ schools.',
            'industry'=>'EdTech','stage'=>'Bootstrapped','arr'=>95000,
            'asking_price'=>450000,'mrr_growth'=>'+14%',
            'status'=>'active','views'=>1980,'category_id'=>$edCat->id,
        ]);

        // ── Inquiries ──────────────────────────────────────
        Inquiry::create([
            'startup_id'=>$verdant->id,'user_id'=>$investor->id,
            'investor_name'=>'Arjun Mehta','organisation'=>'Peak Ventures',
            'interest_type'=>'investment',
            'message'=>'Interested in leading the seed round. Can we schedule a call this week?',
            'status'=>'unread',
        ]);

        Inquiry::create([
            'startup_id'=>$pocket->id,'user_id'=>$investor->id,
            'investor_name'=>'Ananya Patel','organisation'=>'Horizon Capital',
            'interest_type'=>'acquisition',
            'message'=>'We are looking at FinTech acquisitions in the $3-5M range. PocketLedger fits our thesis.',
            'status'=>'replied',
        ]);

        // ── Reviews ────────────────────────────────────────
        Review::create([
            'user_id'=>$investor->id,'startup_id'=>$verdant->id,
            'rating'=>5,'comment'=>'Excellent product with strong unit economics and a clear moat.',
        ]);

        Review::create([
            'user_id'=>$priya->id,'startup_id'=>$pocket->id,
            'rating'=>4,'comment'=>'Very clean UX and strong GST compliance features.',
        ]);

        // ── Investments ────────────────────────────────────
        Investment::create([
            'user_id'            => $investor->id,
            'startup_id'          => $verdant->id,
            'amount'              => 250000,
            'status'              => 'approved',
            'payment_status'      => 'success',
            'payment_id'          => 'pay_' . Str::random(10),
            'payment_method'      => 'stripe',
            'transaction_id'      => 'txn_' . Str::random(10),
            'payment_receipt_no'  => 'REC-' . rand(1000, 9999),
        ]);

        Investment::create([
            'user_id'            => $investor->id,
            'startup_id'          => $pocket->id,
            'amount'              => 450000,
            'status'              => 'approved',
            'payment_status'      => 'success',
            'payment_id'          => 'pay_' . Str::random(10),
            'payment_method'      => 'bank_transfer',
            'transaction_id'      => 'txn_' . Str::random(10),
            'payment_receipt_no'  => 'REC-' . rand(1000, 9999),
        ]);

        Investment::create([
            'user_id'            => $investor->id,
            'startup_id'          => $health->id,
            'amount'              => 120000,
            'status'              => 'approved',
            'payment_status'      => 'success',
            'payment_id'          => 'pay_' . Str::random(10),
            'payment_method'      => 'stripe',
            'transaction_id'      => 'txn_' . Str::random(10),
            'payment_receipt_no'  => 'REC-' . rand(1000, 9999),
        ]);

        $this->command->info('✅ Database seeded!');
        $this->command->info('   Admin : admin@launchpad.com / admin123');
        $this->command->info('   Founder: rohan@verdant.io / password');
        $this->command->info('   Investor: arjun@peakventures.in / password');
    }
}
