<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    use Queueable;

    public $account;

    public function __construct($account)
    {
        $this->account = $account;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'account_id' => $this->account->id,
            'account_number' => $this->account->account_number,
            'balance' => $this->account->balance,
            'message' => 'Warning: Your account #' . $this->account->account_number . ' has fallen below the minimum balance threshold ($' . number_format($this->account->minimum_balance, 2) . ')!'
        ];
    }
}