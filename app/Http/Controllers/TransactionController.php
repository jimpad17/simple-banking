<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionController extends Controller
{
    // Display a listing of transactions
    public function index()
    {
        $transactions = Transaction::with('account')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    // Show the form for creating a new transaction
    public function create()
    {
        $accounts = Account::all();
        return view('transactions.create', compact('accounts'));
    }

    // Store a newly created transaction in storage
    public function store(Request $request)
    {
        // Standard validation
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $account = Account::findOrFail($request->account_id);

        // Custom validation: Check sufficient funds for withdrawal
        if ($request->type === 'withdrawal') {
            $projectedBalance = $account->balance - $request->amount;
            if ($projectedBalance < $account->minimum_balance) {
                return back()->withErrors(['amount' => 'Insufficient funds: Withdrawal would cause balance to fall below minimum balance.'])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($request) {
                Transaction::create([
                    'account_id' => $request->account_id,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'reference_number' => $request->reference_number,
                    'notes' => $request->notes,
                ]);
            });

            return redirect()->back()->with('success', 'Transaction completed successfully!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}