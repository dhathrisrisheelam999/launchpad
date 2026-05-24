@extends('layouts.app')
@section('title', 'My Favorites')
@section('content')
<div class="container" style="padding:60px 6%">
    <div class="sec-label">Saved Startups</div>
    <h1 class="serif" style="font-size:36px;margin-bottom:32px">My Favorites ❤️</h1>

    @if($favorites->isEmpty())
        <div class="empty-state">
            <p style="font-size:40px;margin-bottom:16px">💔</p>
            <p>You haven't saved any startups yet.</p>
            <a href="{{ route('home') }}" class="btn btn-green" style="margin-top:16px">Browse Startups</a>
        </div>
    @else
        <div class="grid3">
            @foreach($favorites as $fav)
                @php $startup = $fav->startup @endphp
                <div class="card">
                    <div class="card-top">
                        <div class="c-logo" style="background:linear-gradient(135deg,#2D5A2E,#3A7A3C)">
                            {{ strtoupper(substr($startup->name, 0, 1)) }}
                        </div>
                        <span class="cbadge b-seed">{{ $startup->stage }}</span>
                    </div>
                    <div class="c-name serif">{{ $startup->name }}</div>
                    <div class="c-desc">{{ $startup->tagline }}</div>
                    <div class="c-sep"></div>
                    <div class="c-foot">
                        <div class="c-ask">
                            Asking<strong>{{ $startup->formatted_asking }}</strong>
                        </div>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('startups.show', $startup) }}" class="btn btn-dark btn-xs">View</a>
                            <form action="{{ route('favorites.toggle', $startup) }}" method="POST">
                                @csrf
                                <button class="btn btn-xs" style="background:#C0392B;color:#fff">❤️ Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
