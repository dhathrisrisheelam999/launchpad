@extends('layouts.app')
@section('title', 'Edit ' . $startup->name)
@section('content')
<div class="form-page container">
    <div class="form-header">
        <div class="sec-label">Edit Listing</div>
        <h1 class="serif">Edit {{ $startup->name }}</h1>
        <p style="color:#6B6B67">Update your startup details below.</p>
    </div>

    @if($errors->any())
    <div class="error-box">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('startups.update', $startup) }}" method="POST" class="form-card">
        @csrf
        @method('PUT')

        <div class="frow">
            <div class="fg">
                <label class="flb">Startup Name *</label>
                <input class="finp" name="name" value="{{ old('name', $startup->name) }}" required/>
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Industry *</label>
                <select class="fsel" name="industry">
                    @foreach(['SaaS','FinTech','HealthTech','EdTech','E-commerce','AI','DevTools'] as $i)
                        <option value="{{ $i }}" {{ old('industry',$startup->industry)===$i?'selected':'' }}>{{ $i }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="fg">
            <label class="flb">Tagline *</label>
            <input class="finp" name="tagline" value="{{ old('tagline', $startup->tagline) }}" required/>
        </div>

        <div class="fg">
            <label class="flb">Full Description *</label>
            <textarea class="ftxt" name="description" rows="5" required>{{ old('description', $startup->description) }}</textarea>
        </div>

        <div class="frow">
            <div class="fg">
                <label class="flb">Stage *</label>
                <select class="fsel" name="stage">
                    @foreach(['Bootstrapped','Pre-Seed','Seed','Pre-Series A','Series A'] as $s)
                        <option value="{{ $s }}" {{ old('stage',$startup->stage)===$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label class="flb">MRR Growth *</label>
                <input class="finp" name="mrr_growth" value="{{ old('mrr_growth', $startup->mrr_growth) }}" placeholder="+18%"/>
            </div>
        </div>

        <div class="frow">
            <div class="fg">
                <label class="flb">Annual Revenue USD *</label>
                <input class="finp" name="arr" type="number" value="{{ old('arr', $startup->arr) }}"/>
                @error('arr')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Asking Price USD *</label>
                <input class="finp" name="asking_price" type="number" value="{{ old('asking_price', $startup->asking_price) }}"/>
            </div>
        </div>

        <div class="fg">
            <label class="flb">Website URL</label>
            <input class="finp" name="website" type="url" value="{{ old('website', $startup->website) }}" placeholder="https://yourstartup.com"/>
        </div>

        <div style="display:flex;gap:12px">
            <button type="submit" class="fbtn" style="flex:1">Save Changes ✅</button>
            <a href="{{ route('startups.show', $startup) }}" class="btn btn-outline-dark" style="padding:14px 24px;justify-content:center">Cancel</a>
        </div>
    </form>
</div>
@endsection
