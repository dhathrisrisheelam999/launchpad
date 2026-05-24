@extends('layouts.app')
@section('title', 'Admin — Categories')
@section('content')
<div class="container" style="padding:40px 6%">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
        <h1 class="serif" style="font-size:32px">Manage Categories</h1>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-dark btn-sm">← Dashboard</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px">
        {{-- Add Category Form --}}
        <div class="dash-section">
            <h2 class="serif dash-sec-title">Add New Category</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="fg">
                    <label class="flb">Category Name</label>
                    <input class="finp" name="name" type="text" placeholder="e.g. CleanTech" value="{{ old('name') }}"/>
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="fg">
                    <label class="flb">Emoji Icon</label>
                    <input class="finp" name="icon" type="text" placeholder="e.g. 🌿" value="{{ old('icon') }}"/>
                </div>
                <div class="fg">
                    <label class="flb">Description (optional)</label>
                    <textarea class="ftxt" name="description" rows="3" placeholder="What kind of startups does this cover?">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="fbtn">Add Category</button>
            </form>
        </div>

        {{-- Existing Categories --}}
        <div class="dash-section">
            <h2 class="serif dash-sec-title">Existing Categories</h2>
            @forelse($categories as $cat)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--line)">
                <div>
                    <span style="font-size:20px;margin-right:10px">{{ $cat->icon }}</span>
                    <strong>{{ $cat->name }}</strong>
                    <span style="font-size:12px;color:#9A9A92;margin-left:8px">{{ $cat->startups_count }} startups</span>
                </div>
                <form action="{{ route('admin.categories.delete', $cat) }}" method="POST"
                      onsubmit="return confirm('Delete {{ $cat->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs" style="background:#C0392B;color:#fff">🗑️</button>
                </form>
            </div>
            @empty
                <p style="color:#9A9A92;text-align:center;padding:20px">No categories yet. Add one!</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
