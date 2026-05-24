@extends('layouts.app')

@section('title', 'Manage Inquiries (Admin) — LaunchPad Market')

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
        max-width: 600px;
        width: 100%;
        padding: 32px;
        box-shadow: var(--sh3);
        position: relative;
        animation: modalFadeIn 0.25s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
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
            <h1>⚙️ Investor Inquiries Manager</h1>
            <p style="font-size:13.5px;color:var(--ink3)">Read and respond directly to sent investor inquiries.</p>
        </div>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-dark btn-sm">← Admin Dashboard</a>
    </div>

    <div class="admin-card">
        <!-- Status filter bar -->
        <div class="filter-row">
            <a href="?status=all" class="fp @if($status === 'all') on @endif">All Inquiries</a>
            <a href="?status=unread" class="fp @if($status === 'unread') on @endif">Unread</a>
            <a href="?status=replied" class="fp @if($status === 'replied') on @endif">Replied</a>
            <a href="?status=closed" class="fp @if($status === 'closed') on @endif">Closed</a>
        </div>

        @if($inquiries->isEmpty())
            <div class="empty-state">
                <h3>📩 No inquiries found matching this filter</h3>
            </div>
        @else
            <table class="dtbl">
                <thead>
                    <tr>
                        <th>Startup</th>
                        <th>Investor / Company</th>
                        <th>Interest Type</th>
                        <th>Pledged Message</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inquiries as $inq)
                        <tr>
                            <td>
                                <strong>{{ $inq->startup->name }}</strong>
                                <span style="display:block;font-size:11px;color:var(--ink4)">{{ $inq->startup->industry }}</span>
                            </td>
                            <td>
                                <strong>{{ $inq->investor_name }}</strong>
                                <span style="display:block;font-size:11px;color:var(--ink3)">{{ $inq->organisation }}</span>
                            </td>
                            <td>
                                <span style="text-transform: capitalize;font-weight: 600;">
                                    @if($inq->interest_type === 'investment') 💰 Investment
                                    @elseif($inq->interest_type === 'acquisition') 🤝 Acquisition
                                    @elseif($inq->interest_type === 'acqui-hire') 👥 Acqui-hire
                                    @else 🔗 Partnership @endif
                                </span>
                            </td>
                            <td style="max-width: 250px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                {{ $inq->message }}
                            </td>
                            <td>
                                <span class="dpill @if($inq->status === 'replied') dp-l @elseif($inq->status === 'unread') dp-r @else dp-c @endif">
                                    {{ ucfirst($inq->status) }}
                                </span>
                            </td>
                            <td>{{ $inq->created_at->format('M d, Y h:i A') }}</td>
                            <td style="text-align:right">
                                <div style="display:flex;gap:6px;justify-content:flex-end">
                                    @if($inq->status === 'unread')
                                        <button class="btn btn-green btn-xs open-reply-modal" 
                                                data-id="{{ $inq->id }}" 
                                                data-startup="{{ $inq->startup->name }}"
                                                data-investor="{{ $inq->investor_name }}"
                                                data-message="{{ $inq->message }}">
                                            Reply 💬
                                        </button>
                                        <form action="{{ route('admin.inquiries.close', $inq) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-dark btn-xs">Close</button>
                                        </form>
                                    @elseif($inq->status === 'replied')
                                        <button class="btn btn-outline-dark btn-xs view-reply-modal"
                                                data-startup="{{ $inq->startup->name }}"
                                                data-investor="{{ $inq->investor_name }}"
                                                data-message="{{ $inq->message }}"
                                                data-reply="{{ $inq->reply_message }}"
                                                data-replied-at="{{ \Carbon\Carbon::parse($inq->replied_at)->format('M d, Y h:i A') }}">
                                            View Reply 👓
                                        </button>
                                    @else
                                        <span style="font-size: 11px;color:var(--ink4)">Closed</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="pagination-wrap">
                {{ $inquiries->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Reply to Inquiry -->
<div id="reply-modal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('reply-modal')">×</button>
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px">Reply to Investor</h3>
        
        <div style="background:var(--off);border:1px solid var(--line);border-radius:var(--r1);padding:14px;margin-bottom:20px;font-size:13px">
            <div><strong>Startup:</strong> <span id="modal-startup"></span></div>
            <div><strong>Investor:</strong> <span id="modal-investor"></span></div>
            <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--line);color:var(--ink3)">
                <strong>Message:</strong> <span id="modal-message"></span>
            </div>
        </div>

        <form id="reply-form" method="POST">
            @csrf
            <div class="fg">
                <label class="flb" for="reply_message">Your Reply Response Message</label>
                <textarea class="ftxt" id="reply_message" name="reply_message" rows="5" placeholder="Write your response message here..." required></textarea>
            </div>
            <button type="submit" class="fbtn">Send Reply</button>
        </form>
    </div>
</div>

<!-- Modal: View Reply details -->
<div id="view-modal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('view-modal')">×</button>
        <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px">Inquiry Thread Details</h3>
        
        <div style="background:var(--off);border:1px solid var(--line);border-radius:var(--r1);padding:14px;margin-bottom:20px;font-size:13px">
            <div><strong>Startup:</strong> <span id="view-startup"></span></div>
            <div><strong>Investor:</strong> <span id="view-investor"></span></div>
            <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--line);color:var(--ink3)">
                <strong>Original Message:</strong> <span id="view-message"></span>
            </div>
        </div>

        <div style="background:var(--green5);border:1px solid var(--green3);border-radius:var(--r1);padding:18px">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--green);font-weight:700;margin-bottom:6px">
                <span>💬 Admin Reply Response</span>
                <span id="view-replied-at"></span>
            </div>
            <div id="view-reply" style="font-size:14px;color:var(--ink2);line-height:1.6"></div>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Open Reply Modal
        const replyButtons = document.querySelectorAll(".open-reply-modal");
        const replyModal = document.getElementById("reply-modal");
        
        replyButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                document.getElementById("modal-startup").textContent = this.getAttribute("data-startup");
                document.getElementById("modal-investor").textContent = this.getAttribute("data-investor");
                document.getElementById("modal-message").textContent = this.getAttribute("data-message");
                
                // Set form action dynamically
                document.getElementById("reply-form").action = `/admin/inquiries/${id}/reply`;
                
                replyModal.style.display = "flex";
            });
        });

        // Open View Modal
        const viewButtons = document.querySelectorAll(".view-reply-modal");
        const viewModal = document.getElementById("view-modal");

        viewButtons.forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("view-startup").textContent = this.getAttribute("data-startup");
                document.getElementById("view-investor").textContent = this.getAttribute("data-investor");
                document.getElementById("view-message").textContent = this.getAttribute("data-message");
                document.getElementById("view-reply").textContent = this.getAttribute("data-reply");
                document.getElementById("view-replied-at").textContent = this.getAttribute("data-replied-at");
                
                viewModal.style.display = "flex";
            });
        });

        // Close on background click
        window.addEventListener("click", function(e) {
            if (e.target === replyModal) replyModal.style.display = "none";
            if (e.target === viewModal) viewModal.style.display = "none";
        });
    });
</script>
@endsection
