<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">New Transaction</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('transactions.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="account_id" class="form-label">Select Account</label>
                                <select name="account_id" id="account_id" class="form-select" required>
                                    <option value="">-- Choose Account --</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">Acc #{{ $account->account_number }} (Balance: ${{ number_format($account->balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Transaction Type</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdrawal">Withdrawal</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount ($)</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required min="1">
                            </div>

                            <div class="mb-3">
                                <label for="reference_number" class="form-label">Reference Number (Optional)</label>
                                <input type="text" name="reference_number" id="reference_number" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
                                <button type="submit" class="btn btn-success">Submit Transaction</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>