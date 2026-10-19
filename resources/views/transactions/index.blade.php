<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>All Transactions History</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Account Number</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Notes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $tx)
                                <tr>
                                    <td>{{ $tx->id }}</td>
                                    <td>{{ $tx->account->account_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tx->type === 'deposit' ? 'success' : 'danger' }}">
                                            {{ ucfirst($tx->type) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($tx->amount, 2) }}</td>
                                    <td>{{ $tx->reference_number ?? 'N/A' }}</td>
                                    <td>{{ $tx->notes ?? 'N/A' }}</td>
                                    <td>{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>