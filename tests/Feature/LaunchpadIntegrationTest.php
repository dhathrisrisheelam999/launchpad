<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Startup;
use App\Models\Inquiry;
use App\Models\Investment;
use App\Models\Notification;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LaunchpadIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $investor;
    private User $founder;
    private User $admin;
    private Startup $startup;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create a category
        $this->category = Category::create([
            'name' => 'FinTech',
            'slug' => 'fintech',
            'icon' => '💳',
            'description' => 'Financial Technology'
        ]);

        // Create standard roles
        $this->investor = User::create([
            'name' => 'Jane Investor',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'role' => 'investor',
            'kyc_status' => 'not_submitted',
        ]);

        $this->founder = User::create([
            'name' => 'John Founder',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'founder',
        ]);

        $this->admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create active startup owned by founder
        $this->startup = Startup::create([
            'user_id' => $this->founder->id,
            'category_id' => $this->category->id,
            'name' => 'Stripe 2.0',
            'tagline' => 'Payments made simpler',
            'description' => 'We make receiving and sending payments easier than ever before.',
            'industry' => 'FinTech',
            'stage' => 'Seed',
            'arr' => 100000,
            'asking_price' => 5000000,
            'mrr_growth' => '+15%',
            'status' => 'active',
        ]);
    }

    // ── PROFILE TESTS ──────────────────────────────────────────

    public function test_user_can_view_profile_page(): void
    {
        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->get(route('profile.index'));

        $response->assertStatus(200);
        $response->assertSee('Profile Settings');
        $response->assertSee($this->investor->email);
    }

    public function test_user_can_update_profile_details(): void
    {
        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->post(route('profile.update'), [
            'name' => 'Jane R. Investor',
            'email' => 'jane_new@example.com',
            'phone' => '+15551234',
            'company_name' => 'Redwood Capital',
            'bio' => 'Experienced angel investor.',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'details']));
        
        $this->assertDatabaseHas('users', [
            'id' => $this->investor->id,
            'name' => 'Jane R. Investor',
            'email' => 'jane_new@example.com',
            'phone' => '+15551234',
            'company_name' => 'Redwood Capital',
            'bio' => 'Experienced angel investor.',
        ]);
        
        $this->assertEquals('Jane R. Investor', session('user_name'));
    }

    public function test_user_can_upload_avatar(): void
    {
        $file = UploadedFile::fake()->create('jane_avatar.png', 100, 'image/png');

        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->post(route('profile.avatar'), [
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'details']));

        $this->investor->refresh();
        $this->assertNotNull($this->investor->avatar_path);
        Storage::disk('public')->assertExists($this->investor->avatar_path);
    }

    public function test_user_can_change_password(): void
    {
        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->post(route('profile.password'), [
            'current_password' => 'password123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'security']));
        $response->assertSessionHas('success');

        $this->investor->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->investor->password));
    }

    public function test_user_can_submit_kyc_document(): void
    {
        $document = UploadedFile::fake()->create('accreditation_proof.pdf', 1024, 'application/pdf');

        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->post(route('profile.kyc'), [
            'kyc_document' => $document,
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'kyc']));

        $this->investor->refresh();
        $this->assertEquals('pending', $this->investor->kyc_status);
        $this->assertNotNull($this->investor->kyc_document);
        Storage::disk('public')->assertExists($this->investor->kyc_document);

        // Check verification notification exists
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'KYC Document Submitted 📄',
        ]);
    }

    // ── INVESTMENT TESTS ───────────────────────────────────────

    public function test_investor_can_place_investment_pledge(): void
    {
        $response = $this->withSession([
            'user_id' => $this->investor->id,
            'user_role' => $this->investor->role,
            'user_name' => $this->investor->name,
        ])->post(route('investments.pay', $this->startup), [
            'amount' => 5000,
            'payment_method' => 'stripe',
            'card_name' => 'Jane Investor',
            'card_number' => '4242424242424242',
            'card_expiry' => '12/28',
            'card_cvc' => '123',
        ]);

        // Should create investment, notify investor & founder, and redirect to success
        $investment = Investment::first();
        $this->assertNotNull($investment);
        $this->assertEquals(5000, $investment->amount);
        $this->assertEquals('pending', $investment->status);
        $this->assertEquals('success', $investment->payment_status);

        $response->assertRedirect(route('investments.success', $investment));

        // Notifications
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Investment Pledge Placed 📈',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->founder->id,
            'title' => 'New Investment Pledged! 💰',
        ]);
    }

    // ── ADMIN CONTROL TESTS ────────────────────────────────────

    public function test_admin_can_approve_kyc(): void
    {
        $this->investor->update([
            'kyc_status' => 'pending',
            'kyc_document' => 'kyc_documents/dummy.pdf'
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.users.kyc.approve', $this->investor));

        $response->assertRedirect();
        
        $this->investor->refresh();
        $this->assertEquals('approved', $this->investor->kyc_status);

        // Check approval notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'KYC Verified Successfully ✅',
        ]);
    }

    public function test_admin_can_reject_kyc(): void
    {
        $this->investor->update([
            'kyc_status' => 'pending',
            'kyc_document' => 'kyc_documents/dummy.pdf'
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.users.kyc.reject', $this->investor), [
            'kyc_reject_reason' => 'Uploaded document was unreadable'
        ]);

        $response->assertRedirect();

        $this->investor->refresh();
        $this->assertEquals('rejected', $this->investor->kyc_status);

        // Check rejection notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'KYC Document Rejected ❌',
            'message' => 'Your KYC accreditation was rejected. Reason: Uploaded document was unreadable. Please upload valid documents in your profile dashboard.'
        ]);
    }

    public function test_admin_can_approve_investment(): void
    {
        $investment = Investment::create([
            'user_id' => $this->investor->id,
            'startup_id' => $this->startup->id,
            'amount' => 5000,
            'status' => 'pending',
            'payment_status' => 'success',
            'payment_method' => 'stripe',
            'transaction_id' => 'TXN-TEST',
            'payment_receipt_no' => 'REC-TEST',
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.investments.approve', $investment));

        $response->assertRedirect();
        
        $investment->refresh();
        $this->assertEquals('approved', $investment->status);

        // Notifications sent to user & founder
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Investment Approved 🌟',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->founder->id,
            'title' => 'Investment Funding Approved! 💰',
        ]);
    }

    public function test_admin_can_reject_investment(): void
    {
        $investment = Investment::create([
            'user_id' => $this->investor->id,
            'startup_id' => $this->startup->id,
            'amount' => 5000,
            'status' => 'pending',
            'payment_status' => 'success',
            'payment_method' => 'stripe',
            'transaction_id' => 'TXN-TEST',
            'payment_receipt_no' => 'REC-TEST',
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.investments.reject', $investment), [
            'reject_reason' => 'Source of funds verification failed.'
        ]);

        $response->assertRedirect();

        $investment->refresh();
        $this->assertEquals('rejected', $investment->status);

        // Notification sent to investor
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Investment Rejected ❌',
            'message' => 'Your investment pledge of $5,000 in \'Stripe 2.0\' was rejected. Reason: Source of funds verification failed.'
        ]);
    }

    // ── INQUIRY & REPLY TESTS ─────────────────────────────────

    public function test_admin_can_reply_to_inquiry(): void
    {
        $inquiry = Inquiry::create([
            'user_id' => $this->investor->id,
            'startup_id' => $this->startup->id,
            'investor_name' => 'Jane Investor',
            'organisation' => 'Jane Capital',
            'interest_type' => 'investment',
            'message' => 'This is a test inquiry message with more than twenty characters.',
            'status' => 'unread',
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.inquiries.reply', $inquiry), [
            'reply_message' => 'We are interested in discussing details. Please schedule a call.',
        ]);

        $response->assertRedirect();

        $inquiry->refresh();
        $this->assertEquals('replied', $inquiry->status);
        $this->assertEquals('We are interested in discussing details. Please schedule a call.', $inquiry->reply_message);
        $this->assertEquals($this->admin->id, $inquiry->replied_by);

        // Notification to user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Admin Replied to Inquiry 💬',
            'message' => 'The admin replied to your inquiry for \'Stripe 2.0\': "We are interested in discussing details. Please schedule a c..."'
        ]);
    }

    public function test_founder_can_reply_to_inquiry(): void
    {
        $inquiry = Inquiry::create([
            'user_id' => $this->investor->id,
            'startup_id' => $this->startup->id,
            'investor_name' => 'Jane Investor',
            'organisation' => 'Jane Capital',
            'interest_type' => 'investment',
            'message' => 'This is a test inquiry message with more than twenty characters.',
            'status' => 'unread',
        ]);

        $response = $this->withSession([
            'user_id' => $this->founder->id,
            'user_role' => $this->founder->role,
            'user_name' => $this->founder->name,
        ])->post(route('dashboard.inquiries.reply', $inquiry), [
            'reply_message' => 'Thanks for your interest! Let us discuss next week.',
        ]);

        $response->assertRedirect();

        $inquiry->refresh();
        $this->assertEquals('replied', $inquiry->status);
        $this->assertEquals('Thanks for your interest! Let us discuss next week.', $inquiry->reply_message);
        $this->assertEquals($this->founder->id, $inquiry->replied_by);

        // Notification to user (investor)
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Founder Replied to Inquiry 💬',
            'message' => 'The founder of Stripe 2.0 replied: "Thanks for your interest! Let us discuss next week."'
        ]);
    }

    public function test_founder_cannot_reply_to_inquiry_for_other_startup(): void
    {
        // Create another founder and startup
        $otherFounder = User::create([
            'name' => 'Other Founder',
            'email' => 'other@example.com',
            'password' => Hash::make('password123'),
            'role' => 'founder',
        ]);
        $otherStartup = Startup::create([
            'user_id' => $otherFounder->id,
            'category_id' => $this->category->id,
            'name' => 'Other Startup',
            'tagline' => 'Other Tagline',
            'description' => 'Other description here.',
            'industry' => 'FinTech',
            'stage' => 'Seed',
            'arr' => 50000,
            'asking_price' => 1000000,
            'mrr_growth' => '+5%',
            'status' => 'active',
        ]);

        $inquiry = Inquiry::create([
            'user_id' => $this->investor->id,
            'startup_id' => $otherStartup->id,
            'investor_name' => 'Jane Investor',
            'organisation' => 'Jane Capital',
            'interest_type' => 'investment',
            'message' => 'This is a test inquiry message with more than twenty characters.',
            'status' => 'unread',
        ]);

        // Try to reply as $this->founder who doesn't own otherStartup
        $response = $this->withSession([
            'user_id' => $this->founder->id,
            'user_role' => $this->founder->role,
            'user_name' => $this->founder->name,
        ])->post(route('dashboard.inquiries.reply', $inquiry), [
            'reply_message' => 'Unauthorized reply attempt.',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_close_inquiry(): void
    {
        $inquiry = Inquiry::create([
            'user_id' => $this->investor->id,
            'startup_id' => $this->startup->id,
            'investor_name' => 'Jane Investor',
            'organisation' => 'Jane Capital',
            'interest_type' => 'investment',
            'message' => 'This is a test inquiry message with more than twenty characters.',
            'status' => 'unread',
        ]);

        $response = $this->withSession([
            'user_id' => $this->admin->id,
            'user_role' => $this->admin->role,
            'user_name' => $this->admin->name,
        ])->post(route('admin.inquiries.close', $inquiry));

        $response->assertRedirect();

        $inquiry->refresh();
        $this->assertEquals('closed', $inquiry->status);

        // Notification to user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->investor->id,
            'title' => 'Inquiry Marked Closed 🔒',
        ]);
    }

    public function test_forgot_password_workflow(): void
    {
        // 1. Post to forgot password route
        $response = $this->post(route('password.send'), [
            'email' => $this->investor->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('reset_url');

        $resetUrl = session('reset_url');
        
        // 2. Parse token from path: /auth/reset-password/{token}
        $path = parse_url($resetUrl, PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        $token = end($pathParts);

        // 3. Get the reset form
        $responseForm = $this->get(route('password.reset.form', ['token' => $token, 'email' => $this->investor->email]));
        $responseForm->assertStatus(200);
        $responseForm->assertSee($token);

        // 4. Submit password reset
        $responseSubmit = $this->post(route('password.reset'), [
            'email' => $this->investor->email,
            'token' => $token,
            'password' => 'mynewsecretpassword',
            'password_confirmation' => 'mynewsecretpassword',
        ]);

        $responseSubmit->assertRedirect(route('auth.login'));
        $responseSubmit->assertSessionHas('success');

        // 5. Verify password works by trying to log in
        $responseLogin = $this->post(route('auth.login.store'), [
            'email' => $this->investor->email,
            'password' => 'mynewsecretpassword',
        ]);

        $responseLogin->assertRedirect(route('dashboard.index'));
        $this->assertEquals($this->investor->id, session('user_id'));
    }

    public function test_logout_functionality(): void
    {
        // 1. Test POST logout to /auth/logout
        $responsePost = $this->withSession([
            'user_id' => $this->investor->id,
            'user_name' => $this->investor->name,
            'user_role' => $this->investor->role,
        ])->post(route('auth.logout'));

        $responsePost->assertRedirect(route('home'));
        $this->assertNull(session('user_id'));

        // 2. Test GET logout to /auth/logout
        $responseGet = $this->withSession([
            'user_id' => $this->investor->id,
            'user_name' => $this->investor->name,
            'user_role' => $this->investor->role,
        ])->get(route('auth.logout'));

        $responseGet->assertRedirect(route('home'));
        $this->assertNull(session('user_id'));

        // 3. Test GET logout to /logout (root)
        $responseRootGet = $this->withSession([
            'user_id' => $this->investor->id,
            'user_name' => $this->investor->name,
            'user_role' => $this->investor->role,
        ])->get('/logout');

        $responseRootGet->assertRedirect(route('home'));
        $this->assertNull(session('user_id'));
    }

    public function test_startup_submission_and_approval_flow(): void
    {
        // 1. Submit a new startup as founder
        $response = $this->withSession([
            'user_id' => $this->founder->id,
            'user_role' => $this->founder->role,
            'user_name' => $this->founder->name,
        ])->post(route('startups.store'), [
            'name' => 'Pending Startup Tech',
            'tagline' => 'Innovative tech solutions',
            'description' => 'We build awesome software for enterprise clients and scaling teams.',
            'industry' => 'SaaS',
            'stage' => 'Seed',
            'arr' => 15000,
            'asking_price' => 100000,
            'mrr_growth' => '+5%',
            'website' => 'https://pendingtech.io',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', '🎉 Your startup has been submitted and is pending admin approval.');

        // Verify it exists in database with status 'pending'
        $startup = Startup::where('name', 'Pending Startup Tech')->first();
        $this->assertNotNull($startup);
        $this->assertEquals('pending', $startup->status);

        // Verify it is NOT shown on the public marketplace (active startups list)
        $marketplaceResponse = $this->get(route('home'));
        $marketplaceResponse->assertStatus(200);
        $marketplaceResponse->assertDontSee('Pending Startup Tech');

        // 2. Approve the startup as admin
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $approveResponse = $this->withSession([
            'user_id' => $admin->id,
            'user_role' => $admin->role,
            'user_name' => $admin->name,
        ])->post(route('admin.startups.approve', $startup));

        $approveResponse->assertRedirect();

        // Verify status is now 'active'
        $startup->refresh();
        $this->assertEquals('active', $startup->status);

        // Verify it is now shown on the public marketplace
        $marketplaceResponseActive = $this->get(route('home'));
        $marketplaceResponseActive->assertStatus(200);
        $marketplaceResponseActive->assertSee('Pending Startup Tech');
    }
}

