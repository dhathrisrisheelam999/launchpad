@extends('layouts.app')
@section('title', 'LaunchPad Market')
@section('content')

<div class="hero-wrap">
    <div class="hero container">
        <div>
            <div class="h-pill"><span class="h-pill-dot"></span>Live Marketplace</div>
            <h1 class="serif">The Marketplace<br>for <em>Ambitious</em><br>Startups.</h1>
            <p class="hero-desc">Connect founders with investors, acquirers, and co-builders. Powered by Laravel MVC.</p>
            <div class="hero-btns">
                <a href="#listings" class="btn btn-green">Explore Startups ↓</a>
                <a href="{{ route('startups.create') }}" class="btn btn-outline-dark">List Your Startup</a>
            </div>
            <div class="hero-stats">
                <div><div class="hs-val">$48M+</div><div class="hs-lbl">Capital Deployed</div></div>
                <div class="stat-div"></div>
                <div><div class="hs-val">2,400+</div><div class="hs-lbl">Startups Listed</div></div>
                <div class="stat-div"></div>
                <div><div class="hs-val">890</div><div class="hs-lbl">Verified Investors</div></div>
            </div>
        </div>
    </div>
</div>

<div class="search-bar-wrap" id="listings">
    <form action="{{ route('startups.search') }}" method="GET" class="sbar container">
        <div class="sbar-inner">
            <input class="sbar-input" name="q" type="text"
                   value="{{ old('q', $query ?? '') }}"
                   placeholder="Search startups…"/>
            <div class="sbar-divider"></div>
            <select class="sbar-select" name="industry">
                <option value="">All Categories</option>
                @foreach(['SaaS','FinTech','HealthTech','EdTech','E-commerce','AI','DevTools'] as $ind)
                    <option value="{{ $ind }}" {{ ($industry ?? '') == $ind ? 'selected' : '' }}>{{ $ind }}</option>
                @endforeach
            </select>
            <button class="sbar-btn" type="submit">Search</button>
        </div>
    </form>
</div>

<section class="sec">
    <div class="container">
        <div class="list-top">
            <div>
                <div class="sec-label">Marketplace</div>
                <h2 class="sec-h serif">Featured Startups</h2>
            </div>
            <div class="result-count">Showing {{ $startups->total() }} startups</div>
        </div>

        <div class="filter-bar">
            @foreach(['all'=>'All Stages','Seed'=>'Seed','Pre-Series A'=>'Pre-Series A','Series A'=>'Series A','Bootstrapped'=>'Bootstrapped'] as $val => $label)
                <a href="{{ route('startups.search', array_merge(request()->all(), ['stage'=>$val])) }}"
                   class="fp {{ ($stage ?? 'all') == $val ? 'on' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="grid3">
            @forelse($startups as $startup)
            <div class="card">
                <div class="card-top">
                    <div class="c-logo" style="background:linear-gradient(135deg,#2D5A2E,#3A7A3C)">
                        {{ strtoupper(substr($startup->name, 0, 1)) }}
                    </div>
                    <span class="cbadge b-seed">{{ $startup->stage }}</span>
                </div>
                <div class="c-name serif">{{ $startup->name }}</div>
                <div class="c-desc">{{ $startup->tagline }}</div>
                <div class="c-tags">
                    <span class="tag">{{ $startup->industry }}</span>
                    @if($startup->category)
                        <span class="tag">{{ $startup->category->icon }} {{ $startup->category->name }}</span>
                    @endif
                </div>
                <div class="c-sep"></div>
                <div class="c-meta">
                    <div class="c-m">Annual Revenue<strong>{{ $startup->formatted_arr }}</strong></div>
                    <div class="c-m">MRR Growth<strong>{{ $startup->mrr_growth }}</strong></div>
                </div>
                <div class="c-foot">
                    <div class="c-ask">Asking<strong>{{ $startup->formatted_asking }}</strong></div>
                    <a href="{{ route('startups.show', $startup) }}" class="btn btn-dark btn-xs">View Details</a>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column:span 3">
                <p>No startups found. <a href="{{ route('home') }}">Clear filters</a></p>
            </div>
            @endforelse
        </div>

        <div class="pagination-wrap">{{ $startups->links() }}</div>
    </div>
</section>
{{-- HOW IT WORKS --}}
<section id="how" style="padding:80px 6%;background:#fff;border-top:1px solid var(--line)">
    <div class="container">
        <div class="sec-label">Process</div>
        <h2 class="sec-h serif">How It Works</h2>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:32px;margin-top:48px">
            @foreach([
                ['1','📝','Submit & Verify','List your startup with financials. Laravel validates every field server-side (Unit V).'],
                ['2','🌐','Go Live','Your listing is reviewed by admin and published. Blade templates render it dynamically (Unit III).'],
                ['3','🤝','Connect','Investors send inquiries. Inquiries stored in MySQL via Eloquent ORM (Unit VI).'],
                ['4','🎉','Close the Deal','Finalize with investors. REST API connects to external services (Unit VI).'],
            ] as $step)
            <div style="text-align:center;padding:24px">
                <div style="width:64px;height:64px;border-radius:50%;background:#EBF4EB;border:2px solid #BDD9BE;display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:24px;font-weight:700;color:#2D5A2E;margin:0 auto 16px">{{ $step[0] }}</div>
                <div style="font-size:28px;margin-bottom:12px">{{ $step[1] }}</div>
                <h3 style="font-size:15px;font-weight:700;margin-bottom:8px">{{ $step[2] }}</h3>
                <p style="font-size:13px;color:#6B6B67;line-height:1.6">{{ $step[3] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>



{{-- PRICING --}}
<section id="pricing" style="padding:80px 6%;background:#fff;border-top:1px solid var(--line)">
    <div class="container">
        <div style="text-align:center;margin-bottom:48px">
            <div class="sec-label" style="display:inline-block">Pricing</div>
            <h2 class="sec-h serif">Simple, Founder-Friendly Plans</h2>
            <p style="color:#6B6B67">No hidden fees. 5% success fee only when you close.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:0 auto">
            @foreach([
                ['Explorer','$0','For early-stage founders testing the waters.',['1 active listing','Basic analytics','5 investor connections']],
                ['Growth','$49','For serious founders ready to raise or sell.',['3 active listings','Full analytics','Unlimited connections','Verified badge']],
                ['Scale','$149','For funds, accelerators and portfolio managers.',['Unlimited listings','Advanced analytics','Featured label','REST API access']],
            ] as $i => $plan)
            <div style="border:{{ $i===1 ? '2px solid #2D5A2E' : '1px solid var(--line)' }};border-radius:20px;padding:30px;position:relative;background:{{ $i===1 ? '#F5FAF5' : '#fff' }}">
                @if($i===1)<div style="position:absolute;top:-13px;left:50%;transform:translateX(-50%);background:#2D5A2E;color:#fff;font-size:11px;font-weight:800;padding:4px 18px;border-radius:100px">Most Popular</div>@endif
                <div style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#9A9A92;margin-bottom:12px">{{ $plan[0] }}</div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:48px;font-weight:700;letter-spacing:-.03em;margin-bottom:4px">{{ $plan[1] }}<span style="font-size:14px;font-weight:400;font-family:'Inter',sans-serif;color:#9A9A92">/mo</span></div>
                <div style="font-size:13px;color:#6B6B67;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--line)">{{ $plan[2] }}</div>
                <ul style="list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:24px">
                    @foreach($plan[3] as $feat)
                    <li style="font-size:13px;color:#6B6B67;display:flex;align-items:center;gap:8px">
                        <span style="color:#2D5A2E;font-weight:800">✓</span> {{ $feat }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('auth.register') }}" class="btn {{ $i===1 ? 'btn-green' : 'btn-outline-dark' }}" style="width:100%;justify-content:center">Get Started</a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection