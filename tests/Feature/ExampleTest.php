<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_renders(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);
        $response = $this->withSession([
            'user_id' => $admin->id,
            'user_role' => 'admin',
        ])->get('/admin');

        $response->assertStatus(200);
        // Print output to log
        echo "\nADMIN RESPONSE STATUS: " . $response->status() . "\n";
        echo "\nADMIN RESPONSE BODY LENGTH: " . strlen($response->content()) . "\n";
    }

    public function test_admin_dashboard_denied_for_non_admin(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Non Admin Test',
            'email' => 'nonadmin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'founder',
        ]);
        $response = $this->withSession([
            'user_id' => $user->id,
            'user_role' => 'founder',
        ])->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_redirected_to_admin_panel_from_dashboard(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Test 2',
            'email' => 'admintest2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);
        $response = $this->withSession([
            'user_id' => $admin->id,
            'user_role' => 'admin',
        ])->get('/dashboard');

        $response->assertRedirect('/admin');
    }

    public function test_founder_dashboard_renders(): void
    {
        $founder = \App\Models\User::create([
            'name' => 'Founder User',
            'email' => 'founder@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'founder',
        ]);
        $response = $this->withSession([
            'user_id' => $founder->id,
            'user_role' => 'founder',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Founder Dashboard');
        $response->assertSee('My Startups');
    }

    public function test_investor_dashboard_renders(): void
    {
        $investor = \App\Models\User::create([
            'name' => 'Investor User',
            'email' => 'investor@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'investor',
        ]);
        $response = $this->withSession([
            'user_id' => $investor->id,
            'user_role' => 'investor',
        ])->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Investor Dashboard');
        $response->assertSee('Favorited Startups');
        $response->assertSee('Recent Inquiries Sent');
    }

    public function test_founder_analytics_renders(): void
    {
        $founder = \App\Models\User::create([
            'name' => 'Founder User 2',
            'email' => 'founder2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'founder',
        ]);
        $response = $this->withSession([
            'user_id' => $founder->id,
            'user_role' => 'founder',
        ])->get('/dashboard/analytics');

        $response->assertStatus(200);
        $response->assertSee('Founder Analytics');
    }

    public function test_investor_analytics_renders(): void
    {
        $investor = \App\Models\User::create([
            'name' => 'Investor User 2',
            'email' => 'investor2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'investor',
        ]);
        $response = $this->withSession([
            'user_id' => $investor->id,
            'user_role' => 'investor',
        ])->get('/dashboard/analytics');

        $response->assertStatus(200);
        $response->assertSee('Investor Analytics');
    }

    public function test_founder_inquiries_renders(): void
    {
        $founder = \App\Models\User::create([
            'name' => 'Founder User 3',
            'email' => 'founder3@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'founder',
        ]);
        $response = $this->withSession([
            'user_id' => $founder->id,
            'user_role' => 'founder',
        ])->get('/dashboard/inquiries');

        $response->assertStatus(200);
        $response->assertSee('Received Inquiries');
    }

    public function test_investor_inquiries_renders(): void
    {
        $investor = \App\Models\User::create([
            'name' => 'Investor User 3',
            'email' => 'investor3@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'investor',
        ]);
        $response = $this->withSession([
            'user_id' => $investor->id,
            'user_role' => 'investor',
        ])->get('/dashboard/inquiries');

        $response->assertStatus(200);
        $response->assertSee('Sent Inquiries');
    }
}



