@extends('layouts.app')

@section('title', 'Manage Investments (Admin) — LaunchPad Market')

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
    .filter-row {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
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
            <h1>⚙️ Platform Investments Manager</h1>
            <p style="font-size:13.5px;color:var(--ink3)">Monitor and approve/reject startup investment pledges.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-dark btn-sm">← Admin Dashboard</a>
    </div>

    <div class="admin-card">
        <!-- Status filter bar -->
        <div class="filter-row">
            <a href="?status=all" class="fp @if($status === 'all') on @endif">All Investments</a>
            <a href="?status=pending" class="fp @if($status === 'pending') on @endif">Pending</a>
            <a href="?status=approved" class="fp @if($status === 'approved') on @endif">Approved</a>
            <a href="?status=rejected" class="fp @if($status === 'rejected') on @endif">Rejected</a>
        </div>

        @if($investments->isEmpty())
            <div class="empty-state">
                <h3>📈 No investments found matching this filter</h3>
            </div>
        @else
            <table class="dtbl">
                <thead>
                    <tr>
                        <th>Startup</th>
                        <th>Investor</th>
                        <th>Amount</th>
                        <th>Transaction ID</th>
                        <th>Payment Status</th>
                        <th>Status</th>
                        <th>Date Pledged</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($investments as $inv)
                        <tr>
                            <td>
                                <strong>{{ $inv->startup->name }}</strong>
                                <span style="display:block;font-size:11px;color:var(--ink4)">{{ $inv->startup->industry }}</span>
                            </td>
                            <td>
                                <strong>{{ $inv->user->name }}</strong>
                                <span style="display:block;font-size:11px;color:var(--ink3)">{{ $inv->user->email }}</span>
                            </td>
                            <td><strong style="color:var(--green)">{{ $inv->formatted_amount }}</strong></td>
                            <td style="font-family:monospace;font-size:12px">{{ $inv->transaction_id }}</td>
                            <td>
                                <span style="font-size:12px;font-weight:600">
                                    🟢 Success <span style="color:var(--ink3)">({{ ucfirst($inv->payment_method) }})</span>
                                </span>
                            </td>
                            <td>
                                <span class="dpill @if($inv->status === 'approved') dp-l @elseif($inv->status === 'pending') dp-r @else dp-c @endif">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td>{{ $inv->created_at->format('M d, Y h:i A') }}</td>
                            <td style="text-align:right">
                                <div style="display:flex;gap:6px;justify-content:flex-end">
                                    @if($inv->status === 'pending')
                                        <form action="{{ route('admin.investments.approve', $inv) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-green btn-xs">Approve ✅</button>
                                        </form>
                                        <button class="btn btn-outline-dark btn-xs open-reject-modal" 
                                                data-id="{{ $inv->id }}"
                                                data-startup="{{ $inv->startup->name }}"
                                                data-investor="{{ $inv->user->name }}"
                                                data-amount="{{ $inv->formatted_amount }}">
                                            Reject ❌
                                        </button>
                                    @else
                                        <a href="{{ route('investments.receipt', $inv) }}" target="_blank" class="btn btn-outline-dark btn-xs">View Invoice 🧾</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination-wrap">
                {{ $investments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Reject Investment -->
<div id="reject-modal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal()">×</button>
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px">Reject Investment Pledge</h3>
        
        <div style="background:var(--off);border:1px solid var(--line);border-radius:var(--r1);padding:14px;margin-bottom:20px;font-size:13px">
            <div><strong>Startup:</strong> <span id="modal-startup"></span></div>
            <div><strong>Investor:</strong> <span id="modal-investor"></span></div>
            <div><strong>Amount:</strong> <span id="modal-amount" style="color:var(--green);font-weight:700"></span></div>
        </div>

        <form id="reject-form" method="POST">
            @csrf
            <div class="fg">
                <label class="flb" for="reject_reason">Rejection Reason</label>
                <textarea class="ftxt" id="reject_reason" name="reject_reason" rows="4" placeholder="State reason for rejecting this pledge (e.g. KYC document mismatch)..." required></textarea>
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
                document.getElementById("modal-startup").textContent = this.getAttribute("data-startup");
                document.getElementById("modal-investor").textContent = this.getAttribute("data-investor");
                document.getElementById("modal-amount").textContent = this.getAttribute("data-amount");
                
                // Set form action dynamically
                document.getElementById("reject-form").action = `/admin/investments/${id}/reject`;
                
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
