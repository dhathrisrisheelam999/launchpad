@extends('layouts.app')
@section('title', 'List Your Startup')
@section('content')
<div class="form-page container">
    <div class="form-header">
        <div class="sec-label">New Listing</div>
        <h1 class="serif">List Your Startup</h1>
        <p>Reach 890+ verified investors.</p>
    </div>

    @if($errors->any())
    <div class="error-box">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('startups.store') }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        <div class="frow">
            <div class="fg">
                <label class="flb">Startup Name *</label>
                <input class="finp" name="name" value="{{ old('name') }}" placeholder="Verdant Analytics"/>
                @error('name')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Industry *</label>
                <select class="fsel" name="industry">
                    @foreach(['SaaS','FinTech','HealthTech','EdTech','E-commerce','AI','DevTools'] as $i)
                        <option value="{{ $i }}" {{ old('industry')==$i?'selected':'' }}>{{ $i }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="fg">
            <label class="flb">Tagline *</label>
            <input class="finp" name="tagline" value="{{ old('tagline') }}" placeholder="One line description"/>
            @error('tagline')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="fg">
            <label class="flb">Full Description *</label>
            <textarea class="ftxt" name="description" rows="5">{{ old('description') }}</textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="frow">
            <div class="fg">
                <label class="flb">Stage *</label>
                <select class="fsel" name="stage">
                    @foreach(['Bootstrapped','Pre-Seed','Seed','Pre-Series A','Series A'] as $s)
                        <option value="{{ $s }}" {{ old('stage')==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="fg">
                <label class="flb">MRR Growth *</label>
                <input class="finp" name="mrr_growth" value="{{ old('mrr_growth') }}" placeholder="+18%"/>
            </div>
        </div>
        <div class="frow">
            <div class="fg">
                <label class="flb">Annual Revenue USD *</label>
                <input class="finp" name="arr" type="number" value="{{ old('arr') }}" placeholder="340000"/>
                @error('arr')<span class="field-error">{{ $message }}</span>@enderror
            </div>
            <div class="fg">
                <label class="flb">Asking Price USD *</label>
                <input class="finp" name="asking_price" type="number" value="{{ old('asking_price') }}" placeholder="1200000"/>
            </div>
        </div>
        <div class="fg">
            <label class="flb">Website URL</label>
            <input class="finp" name="website" value="{{ old('website') }}" placeholder="https://yourstartup.com"/>
        </div>
        <button type="submit" class="fbtn">Submit Listing for Review</button>
    </form>
</div>
@endsection