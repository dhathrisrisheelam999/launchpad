@extends('layouts.app')
@section('title', $isSender ? 'Sent Inquiries' : 'Received Inquiries')
@section('content')

<div class="container" style="padding:40px 6%">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
        <div>
            <h1 class="serif" style="font-size:36px;font-weight:700">
                {{ $isSender ? '📩 Sent Inquiries' : '📥 Received Inquiries' }}
            </h1>
            <p style="color:#6B6B67;font-size:14px;margin-top:4px">
                {{ $isSender ? 'Inquiries you have submitted to startup founders showing interest.' : 'Inquiries submitted by investors interested in your startups.' }}
            </p>
        </div>
        <a href="{{ route('dashboard.index') }}" class="btn btn-outline-dark btn-sm">← Back to Dashboard</a>
    </div>

    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px">
        @if($inquiries->isEmpty())
        <div style="text-align:center;padding:60px 20px;background:#F5FAF5;border-radius:12px">
            <div style="font-size:48px;margin-bottom:16px">📩</div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:8px">No inquiries found</h3>
            <p style="color:#6B6B67">
                {{ $isSender ? 'You have not sent any inquiries to startup founders yet.' : 'No investors have sent inquiries regarding your startups yet.' }}
            </p>
        </div>
        @else
        <style>
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
                border-radius: var(--r2, 12px);
                max-width: 600px;
                width: 90%;
                padding: 32px;
                box-shadow: var(--sh3, 0 8px 30px rgba(0,0,0,0.12));
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
                color: var(--ink3, #6B6B67);
            }
        </style>

        <table class="dtbl">
            <thead>
                <tr>
                    @if($isSender)
                        <th>Startup</th>
                        <th>Founder / Contact</th>
                    @else
                        <th>Investor</th>
                        <th>Startup</th>
                    @endif
                    <th>Interest Type</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inquiries as $inq)
                <tr>
                    @if($isSender)
                        <td>
                            @if($inq->startup)
                                <strong><a href="{{ route('startups.show', $inq->startup) }}" style="color:#111;text-decoration:none;hover:underline">{{ $inq->startup->name }}</a></strong>
                            @else
                                <span style="color:#9A9A92">Deleted Startup</span>
                            @endif
                        </td>
                        <td>
                            @if($inq->startup && $inq->startup->founder)
                                <strong>{{ $inq->startup->founder->name }}</strong><br>
                                <span style="font-size:11px;color:#9A9A92">{{ $inq->startup->founder->email }}</span>
                            @else
                                <span style="color:#9A9A92">N/A</span>
                            @endif
                        </td>
                    @else
                        <td>
                            <strong>{{ $inq->investor_name }}</strong><br>
                            <span style="font-size:11px;color:#9A9A92">{{ $inq->organisation }}</span>
                        </td>
                        <td>
                            @if($inq->startup)
                                <strong><a href="{{ route('startups.show', $inq->startup) }}" style="color:#111;text-decoration:none;hover:underline">{{ $inq->startup->name }}</a></strong>
                            @else
                                <span style="color:#9A9A92">Deleted Startup</span>
                            @endif
                        </td>
                    @endif
                    <td>
                        <span style="font-size:12px;background:#EBF4EB;color:#2D5A2E;padding:3px 8px;border-radius:100px;font-weight:600">
                            {{ ucfirst($inq->interest_type) }}
                        </span>
                    </td>
                    <td style="font-size:13.5px;color:#444;max-width:350px;white-space:normal;word-break:break-word">
                        {{ $inq->message }}
                    </td>
                    <td style="font-size:11px;color:#9A9A92">
                        {{ \Carbon\Carbon::parse($inq->created_at)->diffForHumans() }}
                    </td>
                    <td>
                        @if($inq->status === 'unread')
                            <span class="dpill dp-r">{{ $isSender ? 'Sent' : 'Unread' }}</span>
                        @elseif($inq->status === 'replied')
                            <span class="dpill dp-l">Replied</span>
                        @else
                            <span class="dpill dp-c">Closed</span>
                        @endif
                    </td>
                    <td style="text-align:right">
                        <div style="display:flex;gap:6px;justify-content:flex-end">
                            @if($isSender)
                                @if($inq->status === 'replied')
                                    <button class="btn btn-outline-dark btn-xs view-reply-modal"
                                            data-startup="{{ $inq->startup ? $inq->startup->name : 'Deleted Startup' }}"
                                            data-sender="{{ $inq->repliedBy && $inq->repliedBy->role === 'admin' ? 'Admin' : 'Founder' }}"
                                            data-message="{{ $inq->message }}"
                                            data-reply="{{ $inq->reply_message }}"
                                            data-replied-at="{{ \Carbon\Carbon::parse($inq->replied_at)->format('M d, Y h:i A') }}">
                                        View Reply 👓
                                    </button>
                                @else
                                    <span style="color:#9A9A92;font-size:11px">—</span>
                                @endif
                            @else
                                @if($inq->status === 'unread')
                                    <button class="btn btn-green btn-xs open-reply-modal" 
                                            data-id="{{ $inq->id }}" 
                                            data-startup="{{ $inq->startup ? $inq->startup->name : 'Deleted Startup' }}"
                                            data-investor="{{ $inq->investor_name }}"
                                            data-message="{{ $inq->message }}">
                                        Reply 💬
                                    </button>
                                @elseif($inq->status === 'replied')
                                    <button class="btn btn-outline-dark btn-xs view-reply-modal"
                                            data-startup="{{ $inq->startup ? $inq->startup->name : 'Deleted Startup' }}"
                                            data-sender="Your Reply"
                                            data-message="{{ $inq->message }}"
                                            data-reply="{{ $inq->reply_message }}"
                                            data-replied-at="{{ \Carbon\Carbon::parse($inq->replied_at)->format('M d, Y h:i A') }}">
                                        View Reply 👓
                                    </button>
                                @else
                                    <span style="color:#9A9A92;font-size:11px">—</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top:20px">
            {{ $inquiries->links() }}
        </div>

        <!-- Modal: Reply to Inquiry -->
        <div id="reply-modal" class="modal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeModal('reply-modal')">×</button>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px;text-align:left">Reply to Investor</h3>
                
                <div style="background:var(--off, #F9F9F6);border:1px solid var(--line, #E2E2DF);border-radius:var(--r1, 8px);padding:14px;margin-bottom:20px;font-size:13px;text-align:left">
                    <div><strong>Startup:</strong> <span id="modal-startup"></span></div>
                    <div><strong>Investor:</strong> <span id="modal-investor"></span></div>
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--line, #E2E2DF);color:var(--ink3, #6B6B67)">
                        <strong>Message:</strong> <span id="modal-message"></span>
                    </div>
                </div>

                <form id="reply-form" method="POST">
                    @csrf
                    <div class="fg" style="margin-bottom: 20px; text-align: left;">
                        <label class="flb" for="reply_message" style="display:block;margin-bottom:6px;font-weight:600;font-size:13.5px">Your Reply Response Message</label>
                        <textarea class="ftxt" id="reply_message" name="reply_message" rows="5" placeholder="Write your response message here..." required style="width:100%;padding:10px;border:1px solid var(--line, #E2E2DF);border-radius:var(--r1, 8px);box-sizing:border-box"></textarea>
                    </div>
                    <button type="submit" class="btn btn-green" style="width:100%">Send Reply</button>
                </form>
            </div>
        </div>

        <!-- Modal: View Reply details -->
        <div id="view-modal" class="modal">
            <div class="modal-content">
                <button class="modal-close" onclick="closeModal('view-modal')">×</button>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:12px;text-align:left">Inquiry Thread Details</h3>
                
                <div style="background:var(--off, #F9F9F6);border:1px solid var(--line, #E2E2DF);border-radius:var(--r1, 8px);padding:14px;margin-bottom:20px;font-size:13px;text-align:left">
                    <div><strong>Startup:</strong> <span id="view-startup"></span></div>
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid var(--line, #E2E2DF);color:var(--ink3, #6B6B67)">
                        <strong>Original Message:</strong> <span id="view-message"></span>
                    </div>
                </div>

                <div style="background:var(--green5, #F5FAF5);border:1px solid var(--green3, #C6E0C6);border-radius:var(--r1, 8px);padding:18px;text-align:left">
                    <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--green, #2D5A2E);font-weight:700;margin-bottom:6px">
                        <span>💬 <span id="view-sender-title"></span> Response</span>
                        <span id="view-replied-at"></span>
                    </div>
                    <div id="view-reply" style="font-size:14px;color:var(--ink2, #3C3C3A);line-height:1.6;white-space:pre-wrap"></div>
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
                        document.getElementById("reply-form").action = `/dashboard/inquiries/${id}/reply`;
                        
                        replyModal.style.display = "flex";
                    });
                });

                // Open View Modal
                const viewButtons = document.querySelectorAll(".view-reply-modal");
                const viewModal = document.getElementById("view-modal");

                viewButtons.forEach(btn => {
                    btn.addEventListener("click", function() {
                        document.getElementById("view-startup").textContent = this.getAttribute("data-startup");
                        document.getElementById("view-message").textContent = this.getAttribute("data-message");
                        document.getElementById("view-reply").textContent = this.getAttribute("data-reply");
                        document.getElementById("view-replied-at").textContent = this.getAttribute("data-replied-at");
                        document.getElementById("view-sender-title").textContent = this.getAttribute("data-sender");
                        
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
        @endif
    </div>

</div>
@endsection
