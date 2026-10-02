<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Document - e-CERTIFY</title>
    <!-- Bootstrap for structure -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* General Document Styling */
        body {
            background-color: #f4f4f4;
            font-family: "Times New Roman", Times, serif; 
        }

        /* The white A4 paper canvas (used for certificates) */
        .certificate-paper {
            width: 210mm; 
            min-height: 297mm; 
            margin: 20px auto;
            padding: 25mm; 
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            position: relative;
            box-sizing: border-box;
        }

        /* Hide buttons and shadows when actually printing */
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            body {
                background-color: white;
            }
            
            /* Only apply strict A4 height constraints if the certificate-paper class is actually used */
            .certificate-paper {
                margin: 0;
                padding: 0;
                box-shadow: none;
                width: 100%;
                min-height: auto;
            }
            
            .no-print {
                display: none !important;
            }
            
            /* Forces the printer to start a new page for multi-page documents */
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Controls (Hidden during printing) -->
    <div class="no-print text-center mt-4 mb-3">
        <button onclick="window.print()" class="btn btn-success btn-lg px-5">🖨️ Print Certificate</button>
        <a href="{{ route('residents.index') }}" class="btn btn-secondary btn-lg ms-2">Back</a>
    </div>

    <!-- The Actual Document Canvas -->
    <div>
        @yield('content')
    </div>

</body>
</html>