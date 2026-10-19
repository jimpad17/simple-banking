<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Para sa web view ng dashboard
    public function index()
    {
        $accounts = Account::all();
        $transactions = Transaction::with('account')->latest()->take(10)->get();
        return view('dashboard', compact('accounts', 'transactions'));
    }

    // Para sa AJAX data polling ng accounts at transactions (real-time)
    public function data()
    {
        return response()->json([
            'accounts' => Account::all(),
           'transactions' => Transaction::with('account')->latest()->take(10)->get(),
        ]);
    }

    // Part 4.2: API Endpoint para sa Dashboard Stats
    public function stats()
    {
        return response()->json([
            'total_accounts' => Account::count(),
            'total_money' => Account::sum('balance'),
            'total_transactions_today' => Transaction::whereDate('created_at', Carbon::today())->count()
        ]);
    }
}