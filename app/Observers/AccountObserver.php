<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\Account;
use App\Notifications\LowBalanceNotification;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountObserver
{
    public function creating(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $account = Account::lockForUpdate()->findOrFail($transaction->account_id);

            if ($transaction->type === 'deposit') {
                $account->balance += $transaction->amount;
            } elseif ($transaction->type === 'withdrawal' || $transaction->type === 'transfer') {
                $newBalance = $account->balance - $transaction->amount;

                // Critical Rule: Prevent withdrawal if it falls below minimum balance or goes negative
                if ($newBalance < $account->minimum_balance) {
                    throw new Exception('Transaction failed: Insufficient funds or account would fall below minimum balance threshold.');
                }

                $account->balance = $newBalance;
            }

            $account->save();

            // Check if balance has fallen below or equal to minimum balance to trigger notification
            if ($account->balance <= $account->minimum_balance && $account->user) {
                $account->user->notify(new LowBalanceNotification($account));
            }
        });
    }
}