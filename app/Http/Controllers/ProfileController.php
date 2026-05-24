<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show the user profile page with tabs
    public function index(Request $request)
    {
        $user = User::findOrFail(session('user_id'));
        $investments = $user->investments()->with('startup')->latest()->get();
        $inquiries = $user->inquiries()->with(['startup', 'repliedBy'])->latest()->get();
        $notifications = $user->notifications()->latest()->get();
        $activeTab = $request->get('tab', 'details');

        return view('profile.index', compact('user', 'investments', 'inquiries', 'notifications', 'activeTab'));
    }

    // Update profile info
    public function update(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:150',
            'bio'          => 'nullable|string|max:1000',
        ]);

        $user->update($validated);

        // Update session name if changed
        session(['user_name' => $user->name]);

        return redirect()->route('profile.index', ['tab' => 'details'])
            ->with('success', 'Profile details updated successfully!');
    }

    // Update avatar
    public function updateAvatar(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->file('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar_path' => $path]);
        }

        return redirect()->route('profile.index', ['tab' => 'details'])
            ->with('success', 'Avatar updated successfully!');
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.index', ['tab' => 'security'])
                ->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index', ['tab' => 'security'])
            ->with('success', 'Password changed successfully!');
    }

    // Submit KYC Verification document
    public function storeKyc(Request $request)
    {
        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'kyc_document' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5120', // 5MB max
        ]);

        if ($request->file('kyc_document')) {
            // Delete old document if exists
            if ($user->kyc_document) {
                Storage::disk('public')->delete($user->kyc_document);
            }

            $path = $request->file('kyc_document')->store('kyc_documents', 'public');
            
            $user->update([
                'kyc_status'   => 'pending',
                'kyc_document' => $path,
            ]);

            // Create notification for admin (if needed, but user notifications is what we show in navbar)
            // Let's create an in-app notification for the user to confirm receipt
            Notification::create([
                'user_id' => $user->id,
                'title'   => 'KYC Document Submitted 📄',
                'message' => 'Your KYC document has been uploaded successfully and is currently under review by our admin.',
            ]);
        }

        return redirect()->route('profile.index', ['tab' => 'kyc'])
            ->with('success', 'KYC document submitted for verification successfully!');
    }

    // Mark notification as read
    public function readNotification(Request $request, Notification $notification)
    {
        if ($notification->user_id !== session('user_id')) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    // Mark all notifications as read
    public function readAllNotifications()
    {
        Notification::where('user_id', session('user_id'))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('profile.index', ['tab' => 'notifications'])
            ->with('success', 'All notifications marked as read.');
    }
}
