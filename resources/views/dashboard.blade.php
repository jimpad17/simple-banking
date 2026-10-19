<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Banking Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-0">🏦 Simple Banking System</h1>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?php echo route('transactions.create'); ?>" class="btn btn-primary">Make a Transaction</a>
                <a href="<?php echo route('transactions.index'); ?>" class="btn btn-outline-secondary">View All Transactions</a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo session('success'); ?></div>
        <?php endif; ?>

        <!-- Low Balance Notification Banner -->
        <div id="low-balance-alert" class="alert alert-warning d-none" role="alert">
            <strong>⚠️ Warning:</strong> One or more accounts have fallen below their minimum balance threshold!
        </div>

        <!-- Clean White Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border">
                    <div class="card-body">
                        <span class="text-muted small fw-bold text-uppercase">Total Accounts</span>
                        <h3 id="stat-total-accounts" class="fw-bold mt-1 mb-0"><?php echo count($accounts); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border">
                    <div class="card-body">
                        <span class="text-muted small fw-bold text-uppercase">Total Money in Bank</span>
                        <h3 id="stat-total-money" class="fw-bold mt-1 mb-0 text-success">$<?php echo number_format($accounts->sum('balance'), 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border">
                    <div class="card-body">
                        <span class="text-muted small fw-bold text-uppercase">Transactions Today</span>
                        <h3 id="stat-total-tx-today" class="fw-bold mt-1 mb-0"><?php echo count($transactions); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Accounts Section -->
            <div class="col-md-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Accounts</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="accounts-list">
                            <?php if(!empty($accounts)): ?>
                                <?php foreach($accounts as$account): ?>
                                    <?php $isLow = $account->balance <$account->minimum_balance; ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center <?php echo $isLow ? 'list-group-item-danger' : ''; ?>">
                                        <div>
                                            <strong>Acc #<?php echo htmlspecialchars($account->account_number); ?></strong><br>
                                            <small class="text-muted">Min Balance: $<?php echo number_format($account->minimum_balance, 2); ?></small>
                                        </div>
                                        <span class="badge bg-success fs-6">$<?php echo number_format($account->balance, 2); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Section -->
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Recent Transactions</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Account</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-table-body">
                                    <?php if(!empty($transactions)): ?>
                                        <?php foreach($transactions as$tx): ?>
                                            <tr>
                                                <td><?php echo $tx->id; ?></td>
                                                <td><?php echo htmlspecialchars($tx->account->account_number); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $tx->type === 'deposit' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($tx->type); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($tx->amount, 2); ?></td>
                                                <td><?php echo $tx->created_at->format('M d, Y H:i'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time polling script -->
    <script>
        function updateDashboard() {
            // Poll accounts and recent transactions
            fetch('/dashboard-data')
                .then(response => response.json())
                .then(data => {
                    let accountsList = document.getElementById('accounts-list');
                    accountsList.innerHTML = '';
                    let hasLowBalance = false;

                    data.accounts.forEach(acc => {
                        let isLow = parseFloat(acc.balance) < parseFloat(acc.minimum_balance);
                        if (isLow) hasLowBalance = true;

                        let itemClass = isLow ? 'list-group-item d-flex justify-content-between align-items-center list-group-item-danger' : 'list-group-item d-flex justify-content-between align-items-center';
                        
                        accountsList.innerHTML += `
                            <li class="${itemClass}">
                                <div>
                                    <strong>Acc #${acc.account_number}</strong><br>
                                    <small class="text-muted">Min Balance: $${parseFloat(acc.minimum_balance).toFixed(2)}</small>
                                </div>
                                <span class="badge bg-success fs-6">$${parseFloat(acc.balance).toFixed(2)}</span>
                            </li>
                        `;
                    });

                    let alertBox = document.getElementById('low-balance-alert');
                    if (hasLowBalance) {
                        alertBox.classList.remove('d-none');
                    } else {
                        alertBox.classList.add('d-none');
                    }

                    let txTable = document.getElementById('transactions-table-body');
                    txTable.innerHTML = '';
                    data.transactions.forEach(tx => {
                        let badgeClass = tx.type === 'deposit' ? 'success' : 'danger';
                        let dateStr = new Date(tx.created_at).toLocaleString();
                        txTable.innerHTML += `
                            <tr>
                                <td>${tx.id}</td>
                                <td>${tx.account.account_number}</td>
                                <td><span class="badge bg-${badgeClass}">${tx.type.charAt(0).toUpperCase() + tx.type.slice(1)}</span></td>
                                <td>$${parseFloat(tx.amount).toFixed(2)}</td>
                                <td>${dateStr}</td>
                            </tr>
                        `;
                    });
                })
                .catch(err => console.log(err));

            // Poll API Stats
            fetch('/api/dashboard/stats')
                .then(response => response.json())
                .then(stats => {
                    document.getElementById('stat-total-accounts').innerText = stats.total_accounts;
                    document.getElementById('stat-total-money').innerText = '\$' + parseFloat(stats.total_money).toFixed(2);
                    document.getElementById('stat-total-tx-today').innerText = stats.total_transactions_today;
                })
                .catch(err => console.log(err));
        }

        // Run polling every 5 seconds
        setInterval(updateDashboard, 5000);
    </script>
</body>
</html>