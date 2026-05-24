@extends('layouts.app')

@section('title', 'Admin — Manage Users & KYC')

@section('content')
<style>
    .admin-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
    }
    .admin-header h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 36px;
        font-weight: 700;
    }
    .admin-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: var(--r2);
        padding: 28px;
        box-shadow: var(--sh1);
    }
    .filter-bar {
        display: flex;
        gap: 20px;
        margin-bottom: 24px;
        background: var(--off);
        border: 1px solid var(--line);
        padding: 16px 20px;
        border-radius: var(--r1);
        align-items: center;
        flex-wrap: wrap;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--ink3);
        text-transform: uppercase;
    }
    .filter-select {
        padding: 6px 12px;
        font-size: 13px;
        border: 1.5px solid var(--line2);
        border-radius: var(--r1);
        background: #fff;
        outline: none;
    }
    .kyc-link {
        color: var(--green);
        text-decoration: none;
        font-weight: 700;
    }
    .kyc-link:hover {
        text-decoration: underline;
    }
    .modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(17,17,16,0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: #fff;
        border-radius: var(--r2);
        max-width: 500px;
        width: 100%;
        padding: 32px;
        box-shadow: var(--sh3);
        position: relative;
    }
    .modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 24px;
        border: none;
        background: none;
        cursor: pointer;
        color: var(--ink3);
    }
</style>

<div class="admin-container">
    <div class="admin-header">
        <div>
            <h1>⚙️ User & KYC Control Panel</h1>
            <p style="font-size:13.5px;color:var(--ink3)">Edit user roles and verify accreditation documents.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-dark btn-sm">← Admin Dashboard</a>
    </div>

    <div class="admin-card">
        <!-- Filters Form -->
        <form method="GET" action="{{ route('admin.users') }}" class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">Role</span>
                <select name="role" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>All Roles</option>
                    <option value="founder" {{ $roleFilter === 'founder' ? 'selected' : '' }}>Founder</option>
                    <option value="investor" {{ $roleFilter === 'investor' ? 'selected' : '' }}>Investor</option>
                    <option value="acquirer" {{ $roleFilter === 'acquirer' ? 'selected' : '' }}>Acquirer</option>
                    <option value="advisor" {{ $roleFilter === 'advisor' ? 'selected' : '' }}>Advisor</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">KYC Status</span>
                <select name="kyc_status" class="filter-select" onchange="this.form.submit()">
                    <option value="all" {{ $kycFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="not_submitted" {{ $kycFilter === 'not_submitted' ? 'selected' : '' }}>Not Submitted</option>
                    <option value="pending" {{ $kycFilter === 'pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="approved" {{ $kycFilter === 'approved' ? 'selected' : '' }}>Approved / Verified</option>
                    <option value="rejected" {{ $kycFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <a href="{{ route('admin.users') }}" style="font-size: 13px; color: var(--ink3); text-decoration: none; margin-left: auto;">Reset Filters</a>
        </form>

        @if($users->isEmpty())
            <div class="empty-state">
                <h3>👥 No users found matching this filter</h3>
            </div>
        @else
            <table class="dtbl">
                <thead>
                    <tr>
                        <th>User Info</th>
                        <th>Role Control</th>
                        <th>KYC Status</th>
                        <th>KYC File</th>
                        <th>Startups Listed</th>
                        <th>Joined Date</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <span style="display:block;font-size:11px;color:var(--ink4)">{{ $user->email }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.users.role.update', $user) }}" method="POST" style="display:inline">
                                    @csrf
                                    <select name="role" class="filter-select" style="font-size:12px;padding:3px 8px;border-radius:4px" onchange="this.form.submit()">
                                        <option value="founder" {{ $user->role === 'founder' ? 'selected' : '' }}>Founder</option>
                                        <option value="investor" {{ $user->role === 'investor' ? 'selected' : '' }}>Investor</option>
                                        <option value="acquirer" {{ $user->role === 'acquirer' ? 'selected' : '' }}>Acquirer</option>
                                        <option value="advisor" {{ $user->role === 'advisor' ? 'selected' : '' }}>Advisor</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <span class="dpill @if($user->kyc_status === 'approved') dp-l @elseif($user->kyc_status === 'pending') dp-r @else dp-c @endif">
                                    {{ ucfirst(str_replace('_', ' ', $user->kyc_status)) }}
                                </span>
                            </td>
                            <td>
                                @if($user->kyc_document)
                                    <a href="{{ asset('storage/' . $user->kyc_document) }}" target="_blank" class="kyc-link">View File 📄</a>
                                @else
                                    <span style="font-size:12px;color:var(--ink4)">None</span>
                                @endif
                            </td>
                            <td>{{ $user->startups_count }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td style="text-align:right">
                                <div style="display:flex;gap:6px;justify-content:flex-end">
                                    @if($user->kyc_status === 'pending')
                                        <form action="{{ route('admin.users.kyc.approve', $user) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-green btn-xs">Approve ✅</button>
                                        </form>
                                        <button class="btn btn-outline-dark btn-xs open-reject-modal"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->name }}">
                                            Reject ❌
                                        </button>
                                    @endif
                                    
                                    @if($user->role !== 'admin')
                                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-xs" style="background:#C0392B;color:#fff;border-radius:4px">🗑️ Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination-wrap">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Reject KYC -->
<div id="reject-modal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">×</button>
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px">Reject KYC Accreditation</h3>
        
        <div style="background:var(--off);border:1px solid var(--line);border-radius:var(--r1);padding:14px;margin-bottom:20px;font-size:13px">
            <div><strong>User Name:</strong> <span id="modal-user-name"></span></div>
        </div>

        <form id="reject-form" method="POST">
            @csrf
            <div class="fg">
                <label class="flb" for="kyc_reject_reason">Reason for KYC Rejection</label>
                <textarea class="ftxt" id="kyc_reject_reason" name="kyc_reject_reason" rows="4" placeholder="Explain what document details are missing or invalid..." required></textarea>
            </div>
            <button type="submit" class="fbtn" style="background:#C0392B">Confirm Rejection</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("reject-modal");

    function closeModal() {
        modal.style.display = "none";
    }

    document.addEventListener("DOMContentLoaded", function() {
        const rejectButtons = document.querySelectorAll(".open-reject-modal");
        
        rejectButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                document.getElementById("modal-user-name").textContent = this.getAttribute("data-name");
                
                // Set form action dynamically
                document.getElementById("reject-form").action = `/admin/users/${id}/kyc/reject`;
                
                modal.style.display = "flex";
            });
        });

        // Close on background click
        window.addEventListener("click", function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>
@endsection
