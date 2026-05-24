@extends('layouts.app')

@section('title', 'My Profile Settings — LaunchPad Market')

@section('content')
<style>
    .profile-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
    }
    .profile-sidebar {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 24px;
        height: fit-content;
        box-shadow: var(--sh1);
    }
    .profile-user-summary {
        text-align: center;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--line);
    }
    .profile-avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 16px;
    }
    .profile-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--paper2);
        background: var(--paper);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Cormorant Garamond', serif;
        font-size: 40px;
        font-weight: 700;
        color: var(--green);
    }
    .avatar-upload-trigger {
        position: absolute;
        bottom: 0;
        right: 0;
        background: var(--green);
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        border: 2.5px solid #fff;
        box-shadow: var(--sh1);
        transition: background .2s;
    }
    .avatar-upload-trigger:hover {
        background: var(--green2);
    }
    .profile-user-name {
        font-family: 'Cormorant Garamond', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
    }
    .profile-user-role {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--green);
        background: var(--green5);
        padding: 3px 12px;
        border-radius: 100px;
        display: inline-block;
        margin-top: 4px;
    }
    .profile-nav {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .profile-nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: var(--r1);
        color: var(--ink3);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all .2s;
    }
    .profile-nav-link:hover {
        color: var(--ink);
        background: var(--off);
    }
    .profile-nav-link.active {
        color: var(--green);
        background: var(--green5);
    }
    .profile-content {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 36px;
        box-shadow: var(--sh1);
    }
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }
    .tab-title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 32px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 24px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 12px;
    }
    .kyc-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid;
    }
    .kyc-not_submitted {
        background: var(--paper);
        color: var(--ink3);
        border-color: var(--line2);
    }
    .kyc-pending {
        background: var(--gold4);
        color: var(--gold);
        border-color: var(--gold3);
    }
    .kyc-approved {
        background: var(--green5);
        color: var(--green);
        border-color: var(--green3);
    }
    .kyc-rejected {
        background: #FDF0EE;
        color: #7A3B2E;
        border-color: #F5D5CE;
    }
    .inquiry-thread {
        background: var(--off);
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 20px;
        margin-bottom: 20px;
    }
    .inquiry-message {
        font-size: 14px;
        color: var(--ink2);
        padding-left: 12px;
        border-left: 3px solid var(--line2);
        margin: 12px 0;
    }
    .reply-bubble {
        background: var(--green5);
        border: 1px solid var(--green3);
        border-radius: var(--r1);
        padding: 16px;
        margin-top: 16px;
    }
    .reply-header {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--green);
        font-weight: 700;
        margin-bottom: 6px;
    }
    .notification-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px;
        border-bottom: 1px solid var(--line);
        transition: background .2s;
    }
    .notification-item:hover {
        background: var(--off);
    }
    .notification-item.unread {
        background: var(--green5);
    }
    .notification-left {
        display: flex;
        gap: 16px;
    }
    .notification-icon {
        font-size: 20px;
        padding-top: 2px;
    }
    .notification-text {
        flex: 1;
    }
    .notification-title {
        font-weight: 700;
        font-size: 14px;
        color: var(--ink);
    }
    .notification-msg {
        font-size: 13px;
        color: var(--ink3);
        margin-top: 4px;
        line-height: 1.5;
    }
    .notification-time {
        font-size: 11px;
        color: var(--ink4);
        margin-top: 6px;
    }
</style>

<div class="profile-container">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="profile-user-summary">
            <div class="profile-avatar-wrapper">
                @if($user->avatar_path)
                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="profile-avatar">
                @else
                    <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif
                <form id="avatar-form" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" style="display:none">
                    @csrf
                    <input type="file" id="avatar-file" name="avatar" accept="image/*" onchange="document.getElementById('avatar-form').submit()">
                </form>
                <div class="avatar-upload-trigger" onclick="document.getElementById('avatar-file').click()">📷</div>
            </div>
            <div class="profile-user-name">{{ $user->name }}</div>
            <span class="profile-user-role">{{ ucfirst($user->role) }}</span>
        </div>

        <div class="profile-nav">
            <a href="?tab=details" class="profile-nav-link {{ $activeTab === 'details' ? 'active' : '' }}">👤 Profile Details</a>
            <a href="?tab=security" class="profile-nav-link {{ $activeTab === 'security' ? 'active' : '' }}">🔒 Security Settings</a>
            <a href="?tab=investments" class="profile-nav-link {{ $activeTab === 'investments' ? 'active' : '' }}">📈 Investment History</a>
            <a href="?tab=inquiries" class="profile-nav-link {{ $activeTab === 'inquiries' ? 'active' : '' }}">📩 Sent Inquiries</a>
            <a href="?tab=notifications" class="profile-nav-link {{ $activeTab === 'notifications' ? 'active' : '' }}">🔔 Notifications</a>
            <a href="?tab=kyc" class="profile-nav-link {{ $activeTab === 'kyc' ? 'active' : '' }}">📄 KYC Verification</a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="profile-content">
        <!-- TAB 1: DETAILS -->
        <div class="tab-pane {{ $activeTab === 'details' ? 'active' : '' }}">
            <h2 class="tab-title">Profile Details</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="frow">
                    <div class="fg">
                        <label class="flb" for="name">Full Name</label>
                        <input type="text" class="finp @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="fg">
                        <label class="flb" for="email">Email Address</label>
                        <input type="email" class="finp @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="frow">
                    <div class="fg">
                        <label class="flb" for="phone">Phone Number</label>
                        <input type="text" class="finp" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 123-4567">
                    </div>
                    <div class="fg">
                        <label class="flb" for="company_name">Company Name</label>
                        <input type="text" class="finp" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" placeholder="Acme Investments Corp">
                    </div>
                </div>
                <div class="fg">
                    <label class="flb" for="bio">Investor Bio / Professional Summary</label>
                    <textarea class="ftxt" id="bio" name="bio" rows="4" placeholder="Describe your background and investment preferences...">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <button type="submit" class="btn btn-green">Save Details</button>
            </form>
        </div>

        <!-- TAB 2: SECURITY -->
        <div class="tab-pane {{ $activeTab === 'security' ? 'active' : '' }}">
            <h2 class="tab-title">Security Settings</h2>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                <div class="fg">
                    <label class="flb" for="current_password">Current Password</label>
                    <input type="password" class="finp @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                    @error('current_password')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="fg">
                    <label class="flb" for="password">New Password</label>
                    <input type="password" class="finp @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="fg">
                    <label class="flb" for="password_confirmation">Confirm New Password</label>
                    <input type="password" class="finp" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-dark">Change Password</button>
            </form>
        </div>

        <!-- TAB 3: INVESTMENTS -->
        <div class="tab-pane {{ $activeTab === 'investments' ? 'active' : '' }}">
            <h2 class="tab-title">Investment History</h2>
            @if($investments->isEmpty())
                <div class="empty-state">
                    <h3>📈 No investments made yet</h3>
                    <p style="margin-top:8px">Explore active startups on our platform and start backing them.</p>
                    <a href="{{ route('home') }}" class="btn btn-green btn-sm" style="margin-top:20px">Explore Startups</a>
                </div>
            @else
                <table class="dtbl">
                    <thead>
                        <tr>
                            <th>Startup</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($investments as $inv)
                            <tr>
                                <td>
                                    <strong style="color:var(--ink)">{{ $inv->startup->name }}</strong>
                                    <span style="display:block;font-size:11px;color:var(--ink4)">{{ $inv->startup->industry }}</span>
                                </td>
                                <td><strong style="color:var(--green)">{{ $inv->formatted_amount }}</strong></td>
                                <td>{{ ucfirst($inv->payment_method) }}</td>
                                <td>
                                    <span class="dpill @if($inv->status === 'approved') dp-l @elseif($inv->status === 'pending') dp-r @else dp-c @endif">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td>{{ $inv->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('investments.receipt', $inv) }}" target="_blank" class="btn btn-outline-dark btn-xs">Invoice/Receipt 🧾</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- TAB 4: SENT INQUIRIES -->
        <div class="tab-pane {{ $activeTab === 'inquiries' ? 'active' : '' }}">
            <h2 class="tab-title">Sent Inquiries</h2>
            @if($inquiries->isEmpty())
                <div class="empty-state">
                    <h3>📩 No inquiries sent yet</h3>
                    <p style="margin-top:8px">Ask questions to founders regarding their startups or express interest to invest.</p>
                </div>
            @else
                @foreach($inquiries as $inq)
                    <div class="inquiry-thread">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <h4 style="font-family:'Cormorant Garamond', serif;font-size:20px;font-weight:700">Inquiry on {{ $inq->startup->name }}</h4>
                            <span class="dpill @if($inq->status === 'replied') dp-l @elseif($inq->status === 'unread') dp-r @else dp-c @endif">
                                {{ ucfirst($inq->status) }}
                            </span>
                        </div>
                        <div style="font-size:12px;color:var(--ink4);margin-top:4px">
                            Sent by: {{ $inq->investor_name }} ({{ $inq->organisation }}) &bull; {{ $inq->created_at->format('M d, Y, h:i A') }}
                        </div>
                        <div class="inquiry-message">
                            {{ $inq->message }}
                        </div>
                        @if($inq->status === 'replied')
                            <div class="reply-bubble">
                                <div class="reply-header">
                                    <span>💬 Reply from {{ $inq->repliedBy ? ($inq->repliedBy->role === 'admin' ? 'Admin' : $inq->repliedBy->name) : 'Founder' }}</span>
                                    <span>{{ \Carbon\Carbon::parse($inq->replied_at)->format('M d, Y, h:i A') }}</span>
                                </div>
                                <div style="font-size:14px;line-height:1.6;color:var(--ink2);white-space:pre-wrap">
                                    {{ $inq->reply_message }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- TAB 5: NOTIFICATIONS -->
        <div class="tab-pane {{ $activeTab === 'notifications' ? 'active' : '' }}">
            <div style="display:flex;justify-content:between;align-items:center;margin-bottom:24px;border-bottom: 1px solid var(--line);padding-bottom:12px">
                <h2 class="tab-title" style="margin:0;border:none;padding:0;flex:1">In-App Notifications</h2>
                @if($notifications->whereNull('read_at')->count() > 0)
                    <form action="{{ route('profile.notifications.read_all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark btn-sm">Mark All as Read</button>
                    </form>
                @endif
            </div>

            @if($notifications->isEmpty())
                <div class="empty-state">
                    <h3>🔔 No notifications yet</h3>
                </div>
            @else
                <div style="display:flex;flex-direction:column">
                    @foreach($notifications as $notif)
                        <div class="notification-item @if(is_null($notif->read_at)) unread @endif">
                            <div class="notification-left">
                                <div class="notification-icon">
                                    @if(str_contains($notif->title, 'Approved') || str_contains($notif->title, 'Verified'))
                                        ✅
                                    @elseif(str_contains($notif->title, 'Rejected') || str_contains($notif->title, 'Failed'))
                                        ❌
                                    @elseif(str_contains($notif->title, 'Pledge') || str_contains($notif->title, 'Investment'))
                                        📈
                                    @elseif(str_contains($notif->title, 'Reply') || str_contains($notif->title, 'Inquiry'))
                                        💬
                                    @else
                                        🔔
                                    @endif
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">{{ $notif->title }}</div>
                                    <div class="notification-msg">{{ $notif->message }}</div>
                                    <div class="notification-time">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            @if(is_null($notif->read_at))
                                <form action="{{ route('profile.notifications.read', $notif) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-dark btn-xs" style="font-size:11px">Mark Read</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 6: KYC VERIFICATION -->
        <div class="tab-pane {{ $activeTab === 'kyc' ? 'active' : '' }}">
            <h2 class="tab-title">KYC / Investor Accreditation</h2>
            <div style="margin-bottom:30px">
                <span class="kyc-badge kyc-{{ $user->kyc_status }}">
                    @if($user->kyc_status === 'not_submitted')
                        ⚪ Not Submitted
                    @elseif($user->kyc_status === 'pending')
                        ⏳ Pending Verification
                    @elseif($user->kyc_status === 'approved')
                        ✅ Accredited Investor
                    @elseif($user->kyc_status === 'rejected')
                        ❌ Verification Rejected
                    @endif
                </span>
            </div>

            @if($user->kyc_status === 'not_submitted' || $user->kyc_status === 'rejected')
                @if($user->kyc_status === 'rejected')
                    <div style="background:#FDF0EE;border:1px solid #F5D5CE;padding:16px;border-radius:var(--r1);color:#7A3B2E;margin-bottom:24px;font-size:14px">
                        <strong>Your documents were rejected.</strong> Please review the guidelines below and re-submit a valid identification proof (Passport, Driving License, or Accreditation Certificate) in PDF or Image format.
                    </div>
                @endif

                <div style="background:var(--off);border:1px solid var(--line);padding:24px;border-radius:var(--r2);margin-bottom:30px">
                    <h3 style="font-size:18px;margin-bottom:8px;font-weight:700">Verification Process Instructions</h3>
                    <p style="font-size:13.5px;color:var(--ink3);line-height:1.6">
                        Under regulatory compliance frameworks (SEC Regulation D / standard KYC/AML rules), investors must upload documents confirming identity and accreditation before submitting pledges to active startups.
                    </p>
                    <ul style="font-size:13px;color:var(--ink3);margin:14px 0 0 20px;line-height:1.6">
                        <li>Valid files: PDF, JPEG, JPG, PNG (Max 5MB)</li>
                        <li>Must contain: Clear full name, photograph, and issuing authority seal/credentials.</li>
                        <li>Approval time: Admin will review and verify documents within 24 hours.</li>
                    </ul>
                </div>

                <form action="{{ route('profile.kyc') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="fg">
                        <label class="flb" for="kyc_document">Upload KYC Accreditation Document (PDF/Image)</label>
                        <input type="file" class="finp @error('kyc_document') is-invalid @enderror" id="kyc_document" name="kyc_document" accept=".pdf,image/*" required>
                        @error('kyc_document')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn btn-green">Submit Documents</button>
                </form>
            @elseif($user->kyc_status === 'pending')
                <div style="text-align:center;padding:40px;background:var(--off);border:1px solid var(--line);border-radius:var(--r2)">
                    <span style="font-size:48px;display:block;margin-bottom:16px">⏳</span>
                    <h3 style="font-weight:700;font-size:18px;margin-bottom:8px">Verification Under Review</h3>
                    <p style="font-size:13.5px;color:var(--ink3);max-width:480px;margin:0 auto;line-height:1.6">
                        Your verification document has been uploaded. An administrator will review your files shortly. You will receive an in-app notification when the check completes.
                    </p>
                    @if($user->kyc_document)
                        <div style="margin-top:20px">
                            <a href="{{ asset('storage/' . $user->kyc_document) }}" target="_blank" class="btn btn-outline-dark btn-xs">View Uploaded Document 📄</a>
                        </div>
                    @endif
                </div>
            @elseif($user->kyc_status === 'approved')
                <div style="text-align:center;padding:40px;background:var(--green5);border:1px solid var(--green3);border-radius:var(--r2)">
                    <span style="font-size:48px;display:block;margin-bottom:16px">🌟</span>
                    <h3 style="font-weight:700;font-size:18px;margin-bottom:8px;color:var(--green)">You are Accredited!</h3>
                    <p style="font-size:13.5px;color:var(--ink3);max-width:480px;margin:0 auto;line-height:1.6">
                        Your identity documents are verified. You are authorized to make pledges, view details, and complete investments on LaunchPad Market.
                    </p>
                    @if($user->kyc_document)
                        <div style="margin-top:20px">
                            <a href="{{ asset('storage/' . $user->kyc_document) }}" target="_blank" class="btn btn-outline-green btn-xs">View Accredited File 📄</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
