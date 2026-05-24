<?php

namespace App\Http\Controllers;

use App\Models\Startup;
use App\Models\Investment;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvestmentController extends Controller
{
    // Render the investment process/workflow guide page
    public function process()
    {
        return view('investments.process');
    }

    // Show the checkout / investment page for a startup
    public function checkout(Startup $startup)
    {
        // Require active startup
        if ($startup->status !== 'active') {
            return redirect()->route('startups.show', $startup)
                ->with('error', 'This startup is not currently accepting investments.');
        }

        $user = User::findOrFail(session('user_id'));

        return view('investments.checkout', compact('startup', 'user'));
    }

    // Process the mock payment submission
    public function pay(Request $request, Startup $startup)
    {
        $user = User::findOrFail(session('user_id'));

        $request->validate([
            'amount'         => 'required|integer|min:1000', // Minimum $1,000 investment
            'payment_method' => 'required|in:stripe,razorpay',
        ], [
            'amount.min' => 'The minimum investment amount is $1,000.',
        ]);

        $amount = $request->amount;
        $paymentMethod = $request->payment_method;

        // Extra payment fields based on method (mock validation)
        if ($paymentMethod === 'stripe') {
            $request->validate([
                'card_name'   => 'required|string|max:100',
                'card_number' => 'required|string|min:12|max:19',
                'card_expiry' => 'required|string|regex:/^\d{2}\/\d{2}$/',
                'card_cvc'    => 'required|string|digits_between:3,4',
            ]);
        } else {
            $request->validate([
                'upi_id' => 'required|string|max:100',
            ]);
        }

        // Generate transaction IDs
        $transactionId = 'TXN-' . strtoupper($paymentMethod) . '-' . strtoupper(Str::random(12));
        $receiptNo = 'REC-LP-' . date('Ymd') . '-' . rand(1000, 9999);

        // Create the investment record
        $investment = Investment::create([
            'user_id'            => $user->id,
            'startup_id'          => $startup->id,
            'amount'             => $amount,
            'status'             => 'pending', // Starts as pending admin approval
            'payment_status'     => 'success', // Simulated success
            'payment_method'     => $paymentMethod,
            'payment_id'         => 'PAY-' . strtoupper(Str::random(10)),
            'transaction_id'     => $transactionId,
            'payment_receipt_no' => $receiptNo,
        ]);

        // Send a notification to the investor
        Notification::create([
            'user_id' => $user->id,
            'title'   => 'Investment Pledge Placed 📈',
            'message' => "Your investment of $" . number_format($amount) . " in {$startup->name} was successfully paid via " . ucfirst($paymentMethod) . ". It is now pending admin approval.",
        ]);

        // Also notify the founder of the startup
        if ($startup->founder) {
            Notification::create([
                'user_id' => $startup->founder->id,
                'title'   => 'New Investment Pledged! 💰',
                'message' => "{$user->name} has pledged $" . number_format($amount) . " to your startup {$startup->name}. View details on your dashboard.",
            ]);
        }

        return redirect()->route('investments.success', $investment);
    }

    // Show the payment success page
    public function success(Investment $investment)
    {
        if ($investment->user_id !== session('user_id')) {
            abort(403);
        }

        $investment->load('startup');

        return view('investments.success', compact('investment'));
    }

    // View / Print PDF-styled receipt
    public function receipt(Investment $investment)
    {
        // Admin or the user who invested can view the receipt
        $userId = session('user_id');
        $userRole = session('user_role');

        if ($investment->user_id !== $userId && $userRole !== 'admin') {
            abort(403);
        }

        $investment->load(['startup.founder', 'user']);

        return view('investments.receipt', compact('investment'));
    }
}
