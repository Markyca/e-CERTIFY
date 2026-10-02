<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate Issuance History Report</title>
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
            <a href="{{ route('certificates.history') }}" class="btn btn-secondary btn-sm">← Back to History</a>
            <button onclick="window.print()" class="btn btn-primary btn-sm ms-2">🖨️ Print Again</button>
        </div>

        <!-- Official Header -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-uppercase mb-1">Republic of the Philippines</h5>
            <h6 class="text-muted mb-1">Province of Isabela — Municipality of Mallig</h6>
            <h4 class="fw-bold text-dark mt-2">BARANGAY SIEMPRE VIVA SUR</h4>
            <hr class="w-50 mx-auto">
            <h5 class="fw-bold text-dark mt-3">CERTIFICATE ISSUANCE HISTORY REPORT</h5>
            <p class="text-muted small">{{ $filterText }} | Generated on: {{ date('M d, Y') }}</p>
        </div>

        <!-- Full Table -->
        <table class="table table-bordered align-middle">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Control No.</th>
                    <th>Resident Name</th>
                    <th>Document Type</th>
                    <th>Date Issued</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($certificates as $cert)
                    <tr>
                        <td>{{ $cert->control_number }}</td>
                        <td>{{ $cert->resident->first_name ?? '' }} {{ $cert->resident->last_name ?? '' }}</td>
                        <td>{{ $cert->certificate_type }}</td>
                        <td>{{ $cert->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>
</html>