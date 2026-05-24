@extends('layouts.app')
@section('title', 'Admin — Manage Startups')
@section('content')
<div class="container" style="padding:40px 6%">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
        <h1 class="serif" style="font-size:32px">Manage Startups</h1>
        <div style="display:flex;gap:8px">
            @foreach(['all','pending','active','rejected'] as $s)
                <a href="{{ route('admin.startups', ['status'=>$s]) }}"
                   class="btn btn-sm {{ $status==$s ? 'btn-dark' : 'btn-outline-dark' }}">
                    {{ ucfirst($s) }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="dash-section">
        <table class="dtbl">
            <thead>
                <tr>
                    <th>Startup</th><th>Founder</th><th>Stage</th>
                    <th>ARR</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($startups as $startup)
                <tr>
                    <td>
                        <strong>{{ $startup->name }}</strong>
                        <div style="font-size:11px;color:#9A9A92">{{ $startup->tagline }}</div>
                    </td>
                    <td>{{ $startup->founder->name ?? 'N/A' }}</td>
                    <td>{{ $startup->stage }}</td>
                    <td>{{ $startup->formatted_arr }}</td>
                    <td>
                        @if($startup->status === 'active')
                            <span class="dpill dp-l">Active</span>
                        @elseif($startup->status === 'pending')
                            <span class="dpill dp-r">Pending</span>
                        @else
                            <span class="dpill dp-c">{{ ucfirst($startup->status) }}</span>
                        @endif
                    </td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap">
                        {{-- Approve --}}
                        @if($startup->status !== 'active')
                        <form action="{{ route('admin.startups.approve', $startup) }}" method="POST">
                            @csrf
                            <button class="btn btn-green btn-xs">✅ Approve</button>
                        </form>
                        @endif

                        {{-- Reject --}}
                        @if($startup->status !== 'rejected')
                        <form action="{{ route('admin.startups.reject', $startup) }}" method="POST"
                              onsubmit="return confirm('Enter reject reason below')">
                            @csrf
                            <input type="hidden" name="reject_reason" value="Does not meet our listing criteria."/>
                            <button class="btn btn-xs" style="background:#C0392B;color:#fff">❌ Reject</button>
                        </form>
                        @endif

                        {{-- Delete --}}
                        <form action="{{ route('admin.startups.delete', $startup) }}" method="POST"
                              onsubmit="return confirm('Delete {{ $startup->name }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-xs" style="background:#111;color:#fff">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:40px;color:#9A9A92">No startups found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:20px">{{ $startups->links() }}</div>
    </div>

    <div style="margin-top:20px">
        <a href="{{ route('admin.index') }}" class="btn btn-outline-dark btn-sm">← Back to Dashboard</a>
    </div>
</div>
@endsection
