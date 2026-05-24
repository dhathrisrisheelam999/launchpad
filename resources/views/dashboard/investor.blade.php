@extends('layouts.app')
@section('title', ucfirst(session('user_role')) . ' Dashboard')
@section('content')

{{-- Dashboard Header --}}
<div style="background:linear-gradient(135deg,#FAF6F0 0%,#fff 100%);border-bottom:1px solid var(--line);padding:40px 6%">
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
            <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#8A6914;margin-bottom:8px">
                {{ ucfirst(session('user_role')) }} Dashboard
            </div>
            <h1 style="font-family:'Cormorant Garamond',serif;font-size:40px;font-weight:700;color:#111;letter-spacing:-.03em">
                Welcome back, {{ session('user_name') }}! 👋
            </h1>
            <p style="color:#6B6B67;font-size:14px;margin-top:6px">Track your pipeline, favorited deals, and reviews here</p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('home') }}" class="btn btn-dark">🔍 Browse Marketplace</a>
            <a href="{{ route('dashboard.analytics') }}" class="btn btn-outline-dark">📊 Analytics</a>
        </div>
    </div>
</div>
</div>

<div class="container" style="padding:40px 6%">

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:36px">
        <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;border-left:4px solid #2D5A2E">
            <div style="font-size:24px;margin-bottom:8px">❤️</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:6px">Saved Startups</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $totalFavorites }}</div>
            <div style="font-size:12px;color:#2D5A2E;font-weight:600;margin-top:6px">Added to favorites</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;border-left:4px solid #8A6914">
            <div style="font-size:24px;margin-bottom:8px">📩</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:6px">Inquiries Sent</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $totalSentInquiries }}</div>
            <div style="font-size:12px;color:#8A6914;font-weight:600;margin-top:6px">Showing interest in startups</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;border-left:4px solid #1E3A5F">
            <div style="font-size:24px;margin-bottom:8px">⭐</div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:6px">Reviews Written</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $totalReviews }}</div>
            <div style="font-size:12px;color:#1E3A5F;font-weight:600;margin-top:6px">Platform feedback left</div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px">

        {{-- Favorited Startups --}}
        <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700">Favorited Startups</h2>
                <a href="{{ route('favorites.index') }}" style="font-size:12px;color:#8A6914;font-weight:600;text-decoration:none">View All →</a>
            </div>

            @if($favorites->isEmpty())
            <div style="text-align:center;padding:40px 20px;background:#FAF6F0;border-radius:12px">
                <div style="font-size:36px;margin-bottom:12px">❤️</div>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;margin-bottom:6px">No favorites yet</h3>
                <p style="color:#6B6B67;font-size:13px;margin-bottom:16px">Save startups you find interesting while browsing!</p>
                <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">Explore Startups</a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach($favorites as $fav)
                @if($fav->startup)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--line)">
                    <div>
                        <div style="font-weight:700;font-size:14px">
                            <a href="{{ route('startups.show', $fav->startup) }}" style="color:#111;text-decoration:none;hover:underline">
                                {{ $fav->startup->name }}
                            </a>
                        </div>
                        <div style="font-size:11px;color:#9A9A92;margin-top:2px">
                            {{ $fav->startup->stage }} · ARR: {{ $fav->startup->formatted_arr }}
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('startups.show', $fav->startup) }}" class="btn btn-outline-dark btn-xs">View Deal</a>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- Recent Inquiries Sent (Showing Interest) --}}
        <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
                <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700">Recent Inquiries Sent</h2>
                <a href="{{ route('dashboard.inquiries') }}" style="font-size:12px;color:#8A6914;font-weight:600;text-decoration:none">View All →</a>
            </div>

            @if($sentInquiries->isEmpty())
            <div style="text-align:center;padding:40px 20px;background:#FAF6F0;border-radius:12px">
                <div style="font-size:36px;margin-bottom:12px">📩</div>
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;margin-bottom:6px">No inquiries sent</h3>
                <p style="color:#6B6B67;font-size:13px;margin-bottom:16px">Introduce yourself to founders and show interest in deals!</p>
                <a href="{{ route('home') }}" class="btn btn-outline-dark btn-sm">Browse Marketplace</a>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach($sentInquiries as $inq)
                @if($inq->startup)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:14px;border-bottom:1px solid var(--line)">
                    <div>
                        <div style="font-weight:700;font-size:14px">
                            Inquired: {{ $inq->startup->name }}
                        </div>
                        <div style="font-size:11px;color:#9A9A92;margin-top:2px">
                            Type: <strong style="color:#8A6914">{{ ucfirst($inq->interest_type) }}</strong> · {{ \Carbon\Carbon::parse($inq->created_at)->diffForHumans() }}
                        </div>
                    </div>
                    <div>
                        @if($inq->status === 'unread')
                            <span class="dpill dp-r">Sent</span>
                        @elseif($inq->status === 'replied')
                            <span class="dpill dp-l">Replied</span>
                        @else
                            <span class="dpill dp-c">Closed</span>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Reviews Submitted --}}
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px">
            <h2 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700">My Reviews & Feedback</h2>
        </div>

        @if($reviews->isEmpty())
        <div style="text-align:center;padding:40px 20px;background:#FAF6F0;border-radius:12px">
            <div style="font-size:36px;margin-bottom:12px">⭐</div>
            <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;margin-bottom:6px">No reviews written</h3>
            <p style="color:#6B6B67;font-size:13px">Share feedback on startups to help other users on the platform.</p>
        </div>
        @else
        <table class="dtbl">
            <thead>
                <tr>
                    <th>Startup</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                @if($review->startup)
                <tr>
                    <td>
                        <strong>{{ $review->startup->name }}</strong>
                    </td>
                    <td>
                        <span style="color:#F1C40F">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </span>
                    </td>
                    <td style="font-size:13.5px;color:#444">{{ $review->comment }}</td>
                    <td style="font-size:11px;color:#9A9A92">{{ \Carbon\Carbon::parse($review->created_at)->diffForHumans() }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection
