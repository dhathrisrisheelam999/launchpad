@extends('layouts.app')
@section('title', $startup->name)
@section('content')

<div style="background:linear-gradient(135deg,#F5FAF5 0%,#fff 100%);border-bottom:1px solid var(--line);padding:48px 6% 36px">
<div class="container">
    <div style="display:flex;gap:28px;align-items:flex-start">
        {{-- Logo --}}
        <div style="width:88px;height:88px;border-radius:20px;background:linear-gradient(135deg,#2D5A2E,#3A7A3C);display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:40px;font-weight:700;color:#fff;flex-shrink:0">
            {{ strtoupper(substr($startup->name, 0, 1)) }}
        </div>
        <div style="flex:1">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <span class="cbadge b-seed">{{ $startup->stage }}</span>
                <span class="cbadge b-pre">{{ $startup->industry }}</span>
                @if($startup->status === 'active')
                    <span style="background:#EBF4EB;color:#2D5A2E;font-size:11px;font-weight:700;padding:3px 10px;border-radius:100px;border:1px solid #BDD9BE">✅ Live</span>
                @endif
            </div>
            <h1 style="font-family:'Cormorant Garamond',serif;font-size:42px;font-weight:700;letter-spacing:-.03em;color:#111;margin-bottom:6px">{{ $startup->name }}</h1>
            <p style="font-size:16px;color:#6B6B67;margin-bottom:12px">{{ $startup->tagline }}</p>
            <p style="font-size:13px;color:#9A9A92">
                By <strong style="color:#111">{{ $startup->founder->name ?? 'Unknown' }}</strong>
                · Listed {{ $startup->created_at->diffForHumans() }}
                @if($startup->website)
                · <a href="{{ $startup->website }}" target="_blank" style="color:#2D5A2E">🌐 Website</a>
                @endif
            </p>
        </div>
        {{-- Action buttons --}}
        <div style="display:flex;gap:10px;flex-shrink:0">
            @if(session('user_id'))
            <form action="{{ route('favorites.toggle', $startup) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-dark">❤️ Save</button>
            </form>
            @endif
            <a href="{{ route('home') }}" class="btn btn-outline-dark">← Back</a>
        </div>
    </div>
</div>
</div>

<div class="container" style="padding:40px 6%">
    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:40px">
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;border-left:4px solid #2D5A2E">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:8px">Annual Revenue</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $startup->formatted_arr }}</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;border-left:4px solid #8A6914">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:8px">MRR Growth</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $startup->mrr_growth }}</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;border-left:4px solid #1E3A5F">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:8px">Asking Price</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $startup->formatted_asking }}</div>
        </div>
        <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;border-left:4px solid #7A3B2E">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9A9A92;margin-bottom:8px">Total Inquiries</div>
            <div style="font-family:'Cormorant Garamond',serif;font-size:32px;font-weight:700;color:#111;letter-spacing:-.02em">{{ $startup->inquiries()->count() }}</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:40px">
        {{-- LEFT --}}
        <div>
            {{-- About --}}
            <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px;margin-bottom:24px">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:14px">About {{ $startup->name }}</h3>
                <p style="font-size:15px;color:#5A5A55;line-height:1.8">{{ $startup->description }}</p>
            </div>

            {{-- Reviews --}}
            <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:28px">
                @php $reviews = $startup->reviews()->with('user')->get(); @endphp
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;margin-bottom:6px">
                    Reviews
                    <span style="font-size:16px;color:#9A9A92;font-weight:400">({{ $reviews->count() }})</span>
                    @if($reviews->count() > 0)
                        <span style="font-size:16px;color:#B8891A;margin-left:8px">
                            {{ str_repeat('⭐', round($reviews->avg('rating'))) }}
                            {{ number_format($reviews->avg('rating'), 1) }}/5
                        </span>
                    @endif
                </h3>

                @if(session('user_id'))
                <form action="{{ route('reviews.store', $startup) }}" method="POST"
                      style="background:#F5FAF5;border:1px solid #BDD9BE;border-radius:12px;padding:18px;margin:18px 0">
                    @csrf
                    <label style="font-size:13px;font-weight:600;display:block;margin-bottom:8px">Your Rating</label>
                    <select class="fsel" name="rating" style="width:auto;margin-bottom:12px">
                        <option value="5">⭐⭐⭐⭐⭐ Excellent (5/5)</option>
                        <option value="4">⭐⭐⭐⭐ Good (4/5)</option>
                        <option value="3">⭐⭐⭐ Average (3/5)</option>
                        <option value="2">⭐⭐ Poor (2/5)</option>
                        <option value="1">⭐ Terrible (1/5)</option>
                    </select>
                    <textarea class="ftxt" name="comment" rows="2" placeholder="Share your thoughts about this startup…"></textarea>
                    <button type="submit" class="btn btn-green btn-sm" style="margin-top:10px">Submit Review</button>
                </form>
                @endif

                @forelse($reviews as $review)
                <div style="border-bottom:1px solid var(--line);padding:16px 0">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <div>
                            <strong style="font-size:14px">{{ $review->user->name ?? 'Anonymous' }}</strong>
                            <span style="font-size:11px;color:#9A9A92;margin-left:8px">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <span>{{ str_repeat('⭐', $review->rating) }}</span>
                    </div>
                    @if($review->comment)
                    <p style="font-size:13px;color:#6B6B67;line-height:1.6">{{ $review->comment }}</p>
                    @endif
                </div>
                @empty
                <p style="color:#9A9A92;font-size:13px;margin-top:14px">No reviews yet. Be the first to review!</p>
                @endforelse
            </div>
        </div>

        {{-- RIGHT --}}
        <div>

            {{-- Investment Box --}}
            <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;margin-bottom:24px;border-left:4px solid var(--green)">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;margin-bottom:6px">📈 Invest in {{ $startup->name }}</h3>
                <p style="font-size:13px;color:#6B6B67;margin-bottom:16px">Pledge support to this startup directly on our secure mock gateway.</p>
                @if(session('user_id'))
                    <a href="{{ route('investments.checkout', $startup) }}" class="btn btn-green" style="width:100%;justify-content:center">Invest Now ↗</a>
                @else
                    <div style="text-align:center;padding:12px;background:var(--green5);border-radius:12px">
                        <p style="font-size:12.5px;color:#6B6B67;margin-bottom:10px">Log in to place investment pledges</p>
                        <a href="{{ route('auth.login') }}" class="btn btn-green btn-sm" style="width:100%;justify-content:center">Log In to Invest</a>
                    </div>
                @endif
            </div>

            {{-- Inquiry Box --}}
            <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px">
                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:700;margin-bottom:6px">📩 Express Interest</h3>
                <p style="font-size:13px;color:#6B6B67;margin-bottom:16px">Formal investor inquiry — goes to founder.</p>

                @if(session('user_id'))
                    @if($errors->any())
                    <div class="error-box">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                    @endif

                    <form action="{{ route('inquiry.store', $startup) }}" method="POST">
                        @csrf
                        <div class="fg">
                            <label class="flb">Your Name</label>
                            <input class="finp" name="investor_name" value="{{ old('investor_name') }}" placeholder="Arjun Mehta" required/>
                        </div>
                        <div class="fg">
                            <label class="flb">Organisation</label>
                            <input class="finp" name="organisation" value="{{ old('organisation') }}" placeholder="Peak Ventures" required/>
                        </div>
                        <div class="fg">
                            <label class="flb">Interest Type</label>
                            <select class="fsel" name="interest_type">
                                <option value="investment">💰 Investment</option>
                                <option value="acquisition">🤝 Acquisition</option>
                                <option value="acqui-hire">👥 Acqui-hire</option>
                                <option value="partnership">🔗 Partnership</option>
                            </select>
                        </div>
                        <div class="fg">
                            <label class="flb">Message</label>
                            <textarea class="ftxt" name="message" rows="4" placeholder="Introduce yourself and describe your interest…">{{ old('message') }}</textarea>
                        </div>
                        <button type="submit" class="fbtn">Send Inquiry</button>
                    </form>
                @else
                <div style="text-align:center;padding:24px;background:#F5FAF5;border-radius:12px">
                    <p style="font-size:14px;color:#6B6B67;margin-bottom:14px">Log in to contact this founder</p>
                    <a href="{{ route('auth.login') }}" class="btn btn-green">Log In →</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
