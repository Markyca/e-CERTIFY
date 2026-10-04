<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Audit Logs Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: white; font-family: 'Times New Roman', Times, serif; }
        @media print {
            .no-print { display: none !important; }
            @page { size: A4; margin: 15mm; }
            tr { page-break-inside: avoid; }
            thead { display: table-header-group; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container mt-4">
        <!-- Back Button (Hidden when printing) -->
        <div class="mb-3 no-print">
            <a href="{{ route('logs.index') }}" class="btn btn-secondary btn-sm">← Back to Logs</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm ms-2">🖨️ Print Again</button>
        </div>

        <!-- Official Header -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-uppercase mb-1">Republic of the Philippines</h5>
            <h6 class="text-muted mb-1">Province of {{ $brgy->identity()['province'] }} — Municipality of {{ $brgy->identity()['municipality'] }}</h6>
            <h4 class="fw-bold text-dark mt-2">BARANGAY {{ mb_strtoupper($brgy->identity()['barangay']) }}</h4>
            <hr class="w-50 mx-auto">
            <h5 class="fw-bold text-dark mt-3">SYSTEM ACTIVITY AUDIT LOGS</h5>
            <p class="text-muted small">{{ $filterText }}</p>
        </div>

        <!-- Full Table -->
        <table class="table table-bordered align-middle">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                        <td>{{ $log->user->name ?? 'System' }}</td>
                        <td>{{ $log->action }}</td>
                        <td><span class="font-monospace small">{{ $log->ip_address ?? 'N/A' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No audit logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>