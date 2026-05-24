<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StartupController extends Controller
{
    public function index()
    {
        $startups = Startup::with(['founder', 'inquiries'])
            ->active()
            ->orderByDesc('created_at')
            ->paginate(6);

        return view('startups.index', compact('startups'));
    }

    public function create()
    {
        return view('startups.create');
    }

    public function store(Request $request)
    {
        // Check login first
        if (!session('user_id')) {
            return redirect()->route('auth.login')
                             ->with('error', 'Please log in to list a startup.');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'tagline'      => 'required|string|max:200',
            'description'  => 'required|string|max:2000',
            'industry'     => 'required|in:SaaS,FinTech,HealthTech,EdTech,E-commerce,AI,DevTools',
            'stage'        => 'required|in:Bootstrapped,Pre-Seed,Seed,Pre-Series A,Series A',
            'arr'          => 'required|numeric|min:0',
            'asking_price' => 'required|numeric|min:0',
            'mrr_growth'   => 'required|string|max:20',
            'website'      => 'nullable|url',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required'        => 'Startup name is required.',
            'industry.in'          => 'Please select a valid industry.',
            'stage.in'             => 'Please select a valid stage.',
            'arr.numeric'          => 'Annual Revenue must be a number.',
            'asking_price.numeric' => 'Asking price must be a number.',
            'website.url'          => 'Please enter a valid website URL.',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['user_id'] = session('user_id');

        // By default, newly created startups require admin approval
        $validated['status'] = 'pending';

        Startup::create($validated);

        return redirect()
            ->route('home')
            ->with('success', '🎉 Your startup has been submitted and is pending admin approval.');
    }

    public function show(Startup $startup)
    {
        $startup->load(['founder', 'inquiries']);
        // Increment view count
        $startup->increment('views');
        return view('startups.show', compact('startup'));
    }

    public function edit(Startup $startup)
    {
        // Only the founder can edit
        if ($startup->user_id !== session('user_id')) {
            return redirect()->route('home')->with('error', 'You can only edit your own startups.');
        }
        return view('startups.edit', compact('startup'));
    }

    public function update(Request $request, Startup $startup)
    {
        if ($startup->user_id !== session('user_id')) {
            return redirect()->route('home')->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'tagline'      => 'required|string|max:200',
            'description'  => 'required|string|max:2000',
            'industry'     => 'required|in:SaaS,FinTech,HealthTech,EdTech,E-commerce,AI,DevTools',
            'stage'        => 'required|in:Bootstrapped,Pre-Seed,Seed,Pre-Series A,Series A',
            'arr'          => 'required|numeric|min:0',
            'asking_price' => 'required|numeric|min:0',
            'mrr_growth'   => 'required|string|max:20',
            'website'      => 'nullable|url',
        ]);

        $startup->update($validated);

        return redirect()
            ->route('startups.show', $startup)
            ->with('success', '✅ Listing updated successfully!');
    }

    public function search(Request $request)
    {
        $query    = $request->get('q', '');
        $stage    = $request->get('stage', 'all');
        $industry = $request->get('industry', '');

        $startups = Startup::with(['founder'])
            ->active()
            ->when($query, fn($q) => $q->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('tagline', 'like', "%{$query}%");
            }))
            ->when($stage && $stage !== 'all', fn($q) => $q->where('stage', $stage))
            ->when($industry, fn($q) => $q->where('industry', $industry))
            ->paginate(6)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'data'  => $startups->items(),
                'total' => $startups->total(),
            ]);
        }

        return view('startups.index', compact('startups', 'query', 'stage', 'industry'));
    }
}
