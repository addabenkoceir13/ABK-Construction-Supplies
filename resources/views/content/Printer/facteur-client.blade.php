<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('طباعة الفاتورة') }} - #{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}</title>

    <!-- Google Fonts: Cairo & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    
    <!-- FontAwesome & Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #0f172a;
            --accent-color: #2563eb;
            --accent-hover: #1d4ed8;
            --success-color: #16a34a;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --bg-light: #f8fafc;
            --border-color: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Cairo', 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: var(--text-main);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar (Screen Only) */
        .action-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .action-navbar .brand-title {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        /* Invoice Container Sheet */
        .invoice-sheet-wrapper {
            padding: 30px 15px;
            display: flex;
            justify-content: center;
        }

        .invoice-sheet {
            background: #ffffff;
            width: 100%;
            max-width: 920px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            padding: 40px;
        }

        .invoice-sheet::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #0f172a 0%, #2563eb 50%, #d97706 100%);
        }

        /* Invoice Header Section */
        .company-logo {
            max-height: 80px;
            object-fit: contain;
            border-radius: 8px;
        }

        .company-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 0;
            line-height: 1.2;
        }

        .company-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 4px;
        }

        .invoice-badge-title {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }

        .invoice-number-highlight {
            color: var(--accent-color);
            font-family: 'Inter', sans-serif;
            font-weight: 700;
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-status-paid {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-status-partial {
            background-color: #fef3c7;
            color: #b45309;
        }

        .badge-status-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        /* Party Info Cards */
        .info-card {
            background-color: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            height: 100%;
        }

        .info-card-header {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--accent-color);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 8px;
        }

        .info-card-body p {
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .info-card-body p:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-value {
            color: var(--text-main);
            font-weight: 700;
        }

        /* Metric Highlights */
        .metric-box {
            border-radius: 10px;
            padding: 14px;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .metric-box.total-box {
            background: #f0f9ff;
            border-color: #bae6fd;
        }

        .metric-box.paid-box {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .metric-box.rest-box {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .metric-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .metric-value {
            font-size: 1.15rem;
            font-weight: 800;
            font-family: 'Inter', 'Cairo', sans-serif;
        }

        /* Invoice Table */
        .invoice-table-wrapper {
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            margin-top: 24px;
        }

        .invoice-table {
            margin-bottom: 0;
        }

        .invoice-table thead {
            background-color: var(--primary-color);
            color: #ffffff;
        }

        .invoice-table thead th {
            padding: 14px 16px;
            font-size: 0.85rem;
            font-weight: 700;
            border: none;
            text-transform: uppercase;
        }

        .invoice-table tbody td {
            padding: 14px 16px;
            font-size: 0.95rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }

        .invoice-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .invoice-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Summary Calculations */
        .summary-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px 24px;
            border: 1px solid var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 0.95rem;
        }

        .summary-row.total-grand {
            border-top: 2px solid var(--primary-color);
            margin-top: 8px;
            padding-top: 12px;
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-color);
        }

        /* Notes Box */
        .notes-callout {
            background: #eff6ff;
            border-right: 4px solid var(--accent-color);
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #1e40af;
        }

        /* Signatures */
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
        }

        .signature-box {
            text-align: center;
            padding: 15px;
        }

        .signature-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 50px;
        }

        .signature-line {
            border-bottom: 2px dashed #cbd5e1;
            width: 70%;
            margin: 0 auto;
        }

        /* Invoice Footer */
        .invoice-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid var(--border-color);
            padding-top: 16px;
        }

        /* PRINT STYLES */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .d-print-none,
            .action-navbar,
            .modal,
            .modal-backdrop {
                display: none !important;
            }

            .invoice-sheet-wrapper {
                padding: 0 !important;
            }

            .invoice-sheet {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 10px !important;
                border-radius: 0 !important;
            }

            .invoice-sheet::before {
                display: none !important;
            }

            .invoice-table-wrapper {
                border: 1px solid #cbd5e1 !important;
            }

            .invoice-table thead {
                background-color: #0f172a !important;
                color: #ffffff !important;
            }

            .metric-box,
            .info-card,
            .summary-card {
                border: 1px solid #cbd5e1 !important;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="action-navbar d-print-none">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="brand-title">
                <i class="bx bx-receipt text-primary fs-3"></i>
                <span>{{ __('مؤسسة عدة بن قصير لمستلزمات البناء') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-primary action-btn">
                    <i class="fa-solid fa-print"></i> {{ __('طباعة سريعة') }}
                </button>
                <a href="{{ route('debt.download-facteur-client', ['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" class="btn btn-danger action-btn" title="{{ __('تحميل الفاتورة بصيغة PDF بجودة عالية') }}">
                    <i class="fa-solid fa-file-pdf"></i> {{ __('تحميل PDF') }}
                </a>
                <a href="{{ route('debt.stream-facteur-client', ['debt' => $debt->id, 'fullname' => str_replace('%20', '-', urlencode($debt->fullname))]) }}" target="_blank" class="btn btn-outline-secondary action-btn" title="{{ __('معاينة وطباعة ملف PDF بجودة عالية') }}">
                    <i class="fa-solid fa-eye"></i> {{ __('معاينة PDF') }}
                </a>
                <button type="button" class="btn btn-outline-primary action-btn" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                    <i class="fa-solid fa-paper-plane"></i> {{ __('إرسال عبر البريد') }}
                </button>
                <a href="javascript:history.back()" class="btn btn-light action-btn border">
                    <i class="fa-solid fa-arrow-right"></i> {{ __('عودة') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Main Container -->
    <div class="invoice-sheet-wrapper">
        <div class="invoice-sheet" id="invoice_wrapper">
            
            <!-- Header Section -->
            <div class="row align-items-center pb-4 mb-4 border-bottom">
                <div class="col-sm-7 mb-3 mb-sm-0">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/img/logos/logo-v2.jpg') }}" alt="ABK Logo" class="company-logo">
                        <div>
                            <h1 class="company-name">مؤسسة عدة بن قصير</h1>
                            <div class="company-subtitle">لمستلزمات البناء ومواد التشييد بالجملة والتجزئة</div>
                            <div class="text-muted small mt-1">
                                <i class="fa-solid fa-phone me-1 text-primary"></i> 06 61785937 / 07 70932767
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <div class="invoice-badge-title">
                        {{ __('فاتورة') }}
                    </div>
                    <div class="fs-5 fw-bold mb-2">
                        {{ __('رقم:') }} <span class="invoice-number-highlight">#{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}/{{ $debt->created_at ? $debt->created_at->format('Y') : date('Y') }}</span>
                    </div>

                    @php
                        $paid = is_numeric($debt->debt_paid) ? (float)$debt->debt_paid : 0;
                        $total = is_numeric($debt->total_debt_amount) ? (float)$debt->total_debt_amount : 0;
                        $rest = is_numeric($debt->rest_debt_amount) ? (float)$debt->rest_debt_amount : ($total - $paid);
                    @endphp

                    @if($debt->status === 'paid' || $rest <= 0)
                        <span class="badge-status badge-status-paid">
                            <i class="fa-solid fa-circle-check"></i> {{ __('مدفوعة بالكامل') }}
                        </span>
                    @elseif($paid > 0 && $rest > 0)
                        <span class="badge-status badge-status-partial">
                            <i class="fa-solid fa-clock"></i> {{ __('مدفوعة جزئياً') }}
                        </span>
                    @else
                        <span class="badge-status badge-status-unpaid">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ __('غير مدفوعة') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Dates & Key Info Bar -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="metric-box">
                        <div class="metric-label"><i class="fa-regular fa-calendar me-1"></i> {{ __('تاريخ الإصدار') }}</div>
                        <div class="metric-value text-dark">{{ $debt->date_debut_debt ? \Carbon\Carbon::parse($debt->date_debut_debt)->format('Y-m-d') : date('Y-m-d') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-box total-box">
                        <div class="metric-label"><i class="fa-solid fa-wallet me-1"></i> {{ __('المبلغ الإجمالي') }}</div>
                        <div class="metric-value text-primary">{{ number_format($total, 2) }} <small class="fs-6">د.ج</small></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-box paid-box">
                        <div class="metric-label"><i class="fa-solid fa-circle-dollar-to-slot me-1"></i> {{ __('المبلغ المدفوع') }}</div>
                        <div class="metric-value text-success">{{ number_format($paid, 2) }} <small class="fs-6">د.ج</small></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="metric-box rest-box">
                        <div class="metric-label"><i class="fa-solid fa-hand-holding-dollar me-1"></i> {{ __('الرصيد المتبقي') }}</div>
                        <div class="metric-value text-danger">{{ number_format($rest, 2) }} <small class="fs-6">د.ج</small></div>
                    </div>
                </div>
            </div>

            <!-- Client & Supplier Details Cards -->
            <div class="row g-3 mb-4">
                <!-- Client Info -->
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fa-solid fa-user-tag fs-5"></i>
                            <span>{{ __('فاتورة موجهة إلى (العميل)') }}</span>
                        </div>
                        <div class="info-card-body">
                            <p>
                                <span class="info-label">{{ __('الاسم الكامل:') }}</span>
                                <span class="info-value text-primary fs-6">{{ $debt->fullname }}</span>
                            </p>
                            <p>
                                <span class="info-label">{{ __('رقم الهاتف:') }}</span>
                                <span class="info-value" dir="ltr">{{ $debt->phone ?? '---' }}</span>
                            </p>
                            @if($debt->date_end_debt)
                                <p>
                                    <span class="info-label">{{ __('تاريخ الاستحقاق:') }}</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($debt->date_end_debt)->format('Y-m-d') }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Supplier Info -->
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fa-solid fa-building-user fs-5"></i>
                            <span>{{ __('صادر عن (المورد)') }}</span>
                        </div>
                        <div class="info-card-body">
                            <p>
                                <span class="info-label">{{ __('المؤسسة:') }}</span>
                                <span class="info-value fs-6">مؤسسة عدة بن قصير لمستلزمات البناء</span>
                            </p>
                            <p>
                                <span class="info-label">{{ __('أرقام التواصل:') }}</span>
                                <span class="info-value" dir="ltr">0661785937 / 0770932767</span>
                            </p>
                            <p>
                                <span class="info-label">{{ __('النشاط:') }}</span>
                                <span class="info-value">{{ __('بيع ومواد البناء والتشييد') }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Summary Table -->
            <div class="invoice-table-wrapper mb-4">
                <table class="table invoice-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>{{ __('تفاصيل المنتج / التعيين') }}</th>
                            <th class="text-center" style="width: 160px;">{{ __('الكمية والوحدة') }}</th>
                            <th class="text-end" style="width: 150px;">{{ __('سعر الوحدة (تقديري)') }}</th>
                            <th class="text-end" style="width: 160px;">{{ __('المجموع (د.ج)') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($debt->getDebtProduct as $item)
                            @php
                                $unitName = optional($item->getSubcategory)->name ?? optional($item->getSubcategory)->display_name ?? '';
                                $qty = is_numeric($item->quantity) ? (float)$item->quantity : 1;
                                $amount = is_numeric($item->amount) ? (float)$item->amount : 0;
                                $unitPrice = ($qty > 0) ? ($amount / $qty) : $amount;
                            @endphp
                            <tr>
                                <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->name_category }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6 fw-semibold">
                                        {{ $qty }} {{ $unitName }}
                                    </span>
                                </td>
                                <td class="text-end font-monospace">
                                    {{ number_format($unitPrice, 2) }}
                                </td>
                                <td class="text-end fw-bold font-monospace text-primary">
                                    {{ number_format($amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('لا توجد عناصر مسجلة في هذه الفاتورة.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Breakdown & Notes Row -->
            <div class="row g-4 align-items-start mb-4">
                <!-- Notes Column -->
                <div class="col-md-6">
                    @if(!empty($debt->note))
                        <div class="notes-callout mb-3">
                            <div class="fw-bold mb-1"><i class="fa-solid fa-note-sticky me-1"></i> {{ __('ملاحظات الفاتورة:') }}</div>
                            <div>{{ $debt->note }}</div>
                        </div>
                    @endif
                    <div class="text-muted small">
                        <p class="mb-1"><i class="fa-solid fa-circle-info text-primary me-1"></i> {{ __('جميع البضائع المباعة خاضعة لشروط وأحكام المؤسسة.') }}</p>
                        <p class="mb-0"><i class="fa-solid fa-shield-halved text-success me-1"></i> {{ __('شكراً لثقتكم بنا وبسدادكم في المواعيد المحتسبة.') }}</p>
                    </div>
                </div>

                <!-- Financial Calculation Summary Column -->
                <div class="col-md-6">
                    <div class="summary-card">
                        <div class="summary-row">
                            <span class="text-muted">{{ __('المجموع الإجمالي:') }}</span>
                            <span class="fw-bold fs-6 font-monospace">{{ number_format($total, 2) }} د.ج</span>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted">{{ __('المبلغ المدفوع (المعجل):') }}</span>
                            <span class="fw-bold text-success fs-6 font-monospace">- {{ number_format($paid, 2) }} د.ج</span>
                        </div>
                        <div class="summary-row total-grand">
                            <span>{{ __('الرصيد المتبقي المستحق:') }}</span>
                            <span class="font-monospace text-danger">{{ number_format($rest, 2) }} د.ج</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="signature-section">
                <div class="row">
                    <div class="col-6">
                        <div class="signature-box">
                            <div class="signature-title">{{ __('توقيع وخاتم المورد') }}</div>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="signature-box">
                            <div class="signature-title">{{ __('توقيع الزبون / المستلم') }}</div>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p class="mb-1 fw-bold">مؤسسة عدة بن قصير لمستلزمات البناء - ABK Construction Supplies</p>
                <p class="mb-0">العنوان: الجزائر | الهاتف: 06 61785937 / 07 70932767</p>
            </div>

        </div>
    </div>

    <!-- Send Email Modal -->
    <div class="modal fade d-print-none" id="sendEmailModal" tabindex="-1" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="sendEmailModalLabel">
                        <i class="fa-solid fa-paper-plane me-2"></i> {{ __('إرسال الفاتورة عبر البريد الإلكتروني') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sendEmailForm">
                    @csrf
                    <div class="modal-body p-4">

                        <!-- Preset Quick Select Emails -->
                        <div class="mb-4">
                            <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                <span><i class="fa-solid fa-users me-1 text-primary"></i> {{ __('اختيار سريع للمستلمين المعتمدين:') }}</span>
                                <span class="badge bg-light text-primary border">{{ __('يمكنك اختيار واحد أو أكثر') }}</span>
                            </label>
                            
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="card border p-2 h-100 preset-email-card" style="cursor: pointer; transition: all 0.2s ease;">
                                        <div class="form-check d-flex align-items-center gap-2 m-0">
                                            <input class="form-check-input preset-checkbox fs-5" type="checkbox" value="iat.soft40@gmail.com" id="presetEmail1">
                                            <label class="form-check-label w-100" for="presetEmail1" style="cursor: pointer;">
                                                <div class="fw-bold text-dark" dir="ltr" style="text-align: right;">iat.soft40@gmail.com</div>
                                                <small class="text-muted"><i class="fa-solid fa-user-check me-1"></i> IAT Soft</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card border p-2 h-100 preset-email-card" style="cursor: pointer; transition: all 0.2s ease;">
                                        <div class="form-check d-flex align-items-center gap-2 m-0">
                                            <input class="form-check-input preset-checkbox fs-5" type="checkbox" value="addamohamed67@gmail.com" id="presetEmail2">
                                            <label class="form-check-label w-100" for="presetEmail2" style="cursor: pointer;">
                                                <div class="fw-bold text-dark" dir="ltr" style="text-align: right;">addamohamed67@gmail.com</div>
                                                <small class="text-muted"><i class="fa-solid fa-user-tie me-1"></i> Adda Mohamed</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Email Input -->
                        <div class="mb-4">
                            <label for="customEmailInput" class="form-label fw-bold">
                                <i class="fa-solid fa-envelope-open-text me-1 text-primary"></i> {{ __('إدخال بريد إلكتروني إضافي / مخصص:') }}
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-at text-muted"></i></span>
                                <input type="email" class="form-control" id="customEmailInput" placeholder="example@domain.com" dir="ltr">
                                <button type="button" class="btn btn-outline-primary fw-bold" id="btnAddCustomEmail">
                                    <i class="fa-solid fa-plus me-1"></i> {{ __('إضافة') }}
                                </button>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                {{ __('اكتب البريد الإلكتروني واضغط "إضافة" أو Enter لإدراجه في قائمة الإرسال.') }}
                            </div>
                        </div>

                        <!-- Selected Recipients Visual Tag Container -->
                        <div class="mb-4">
                            <label class="form-label fw-bold d-flex align-items-center justify-content-between">
                                <span><i class="fa-solid fa-list-check me-1 text-success"></i> {{ __('قائمة المستلمين المحددين للإرسال:') }}</span>
                                <span class="badge bg-primary rounded-pill px-3" id="recipientCountBadge">0 مستلم</span>
                            </label>
                            
                            <div class="border rounded p-3 bg-light min-h-50 d-flex flex-wrap gap-2 align-items-center" id="selectedEmailsContainer">
                                <span class="text-muted small fst-italic" id="noEmailsSelectedText">
                                    <i class="fa-solid fa-circle-info me-1"></i> {{ __('لم يتم تحديد أي بريد إلكتروني بعد. اختر من المستلمين أعلاه أو أدخل بريداً جديداً.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Optional Message Note -->
                        <div class="mb-3">
                            <label for="emailNote" class="form-label fw-bold">
                                <i class="fa-solid fa-comment-dots me-1 text-primary"></i> {{ __('ملاحظة إضافية مع الرسالة (اختياري)') }}
                            </label>
                            <textarea class="form-control" id="emailNote" name="note" rows="2" placeholder="{{ __('أضف أي ملاحظة تود إرفاقها مع الفاتورة...') }}"></textarea>
                        </div>

                        <!-- Attachment Notice -->
                        <div class="alert alert-info py-2 px-3 m-0 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis small">
                            <i class="fa-solid fa-file-pdf fs-5 text-danger"></i>
                            <div>{{ __('سيتم تلقائياً إرفاق ملف الفاتورة بصيغة PDF باللغة العربية مع كل رسالة مرسلة.') }}</div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('إلغاء') }}</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitEmail">
                            <span class="spinner-border spinner-border-sm d-none me-2" id="emailSpinner" role="status" aria-hidden="true"></span>
                            <i class="fa-solid fa-paper-plane me-1"></i> {{ __('إرسال الآن') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Selected emails set
            var selectedEmails = new Set();

            function isValidEmail(email) {
                var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function renderSelectedEmails() {
                var container = $('#selectedEmailsContainer');
                var countBadge = $('#recipientCountBadge');
                container.empty();

                if (selectedEmails.size === 0) {
                    container.html('<span class="text-muted small fst-italic" id="noEmailsSelectedText"><i class="fa-solid fa-circle-info me-1"></i> {{ __("لم يتم تحديد أي بريد إلكتروني بعد. اختر من المستلمين أعلاه أو أدخل بريداً جديداً.") }}</span>');
                    countBadge.text('0 {{ __("مستلم") }}').removeClass('bg-success').addClass('bg-secondary');
                } else {
                    countBadge.text(selectedEmails.size + ' {{ __("مستلم") }}').removeClass('bg-secondary').addClass('bg-success');
                    
                    selectedEmails.forEach(function(email) {
                        var pill = $(`
                            <span class="badge bg-white text-dark border shadow-sm p-2 d-inline-flex align-items-center gap-2 rounded-3 fs-6">
                                <i class="fa-solid fa-envelope text-primary"></i>
                                <span dir="ltr" class="fw-semibold">${email}</span>
                                <button type="button" class="btn-close btn-sm ms-1 remove-email-btn" data-email="${email}" aria-label="Remove" style="font-size: 0.65rem;"></button>
                            </span>
                        `);
                        container.append(pill);
                    });
                }

                // Sync preset checkboxes
                $('.preset-checkbox').each(function() {
                    var val = $(this).val();
                    $(this).prop('checked', selectedEmails.has(val));
                    var card = $(this).closest('.preset-email-card');
                    if (selectedEmails.has(val)) {
                        card.addClass('border-primary bg-primary-subtle');
                    } else {
                        card.removeClass('border-primary bg-primary-subtle');
                    }
                });
            }

            // Preset checkbox change handler
            $('.preset-checkbox').on('change', function() {
                var email = $(this).val();
                if ($(this).is(':checked')) {
                    selectedEmails.add(email);
                } else {
                    selectedEmails.delete(email);
                }
                renderSelectedEmails();
            });

            // Preset card click toggles checkbox
            $('.preset-email-card').on('click', function(e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) {
                    return;
                }
                var chk = $(this).find('.preset-checkbox');
                chk.prop('checked', !chk.prop('checked')).trigger('change');
            });

            // Add custom email function
            function addCustomEmail() {
                var input = $('#customEmailInput');
                var rawVal = input.val().trim();

                if (!rawVal) {
                    return;
                }

                // Support comma / space separated multiple entries
                var parts = rawVal.split(/[,;\s]+/);
                var addedAny = false;

                parts.forEach(function(p) {
                    var cleanEmail = p.trim().toLowerCase();
                    if (cleanEmail) {
                        if (isValidEmail(cleanEmail)) {
                            selectedEmails.add(cleanEmail);
                            addedAny = true;
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __("تنبيه") }}',
                                text: '{{ __("عنوان البريد الإلكتروني غير صحيح:") }} ' + cleanEmail,
                                confirmButtonText: '{{ __("حسناً") }}'
                            });
                        }
                    }
                });

                if (addedAny) {
                    input.val('');
                    renderSelectedEmails();
                }
            }

            $('#btnAddCustomEmail').on('click', function() {
                addCustomEmail();
            });

            $('#customEmailInput').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addCustomEmail();
                }
            });

            // Remove email pill handler
            $(document).on('click', '.remove-email-btn', function() {
                var emailToRemove = $(this).data('email');
                selectedEmails.delete(emailToRemove);
                renderSelectedEmails();
            });

            // Form submission
            $('#sendEmailForm').on('submit', function(e) {
                e.preventDefault();

                // If user typed an email in the input without clicking "+", add it now
                var typedEmail = $('#customEmailInput').val().trim();
                if (typedEmail && isValidEmail(typedEmail)) {
                    selectedEmails.add(typedEmail.toLowerCase());
                    $('#customEmailInput').val('');
                    renderSelectedEmails();
                }

                var note = $('#emailNote').val();
                var submitBtn = $('#btnSubmitEmail');
                var spinner = $('#emailSpinner');

                if (selectedEmails.size === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("تنبيه") }}',
                        text: '{{ __("يرجى تحديد أو إدخال عنوان بريد إلكتروني واحد على الأقل.") }}',
                        confirmButtonText: '{{ __("حسناً") }}'
                    });
                    return;
                }

                var emailsArray = Array.from(selectedEmails);

                // Disable submit button & show spinner
                submitBtn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: "{{ route('debt.send-email', $debt->id) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        emails: emailsArray,
                        note: note
                    },
                    success: function(response) {
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                        $('#sendEmailModal').modal('hide');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("تم الإرسال بنجاح!") }}',
                                html: `<div class="mt-2">${response.message}</div>`,
                                confirmButtonText: '{{ __("ممتاز") }}',
                                timer: 6000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("خطأ") }}',
                                text: response.message || '{{ __("حدث خطأ أثناء إرسال البريد الإلكتروني.") }}',
                                confirmButtonText: '{{ __("حسناً") }}'
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                        
                        var errorMsg = '{{ __("حدث خطأ غير متوقع.") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("فشل الإرسال") }}',
                            text: errorMsg,
                            confirmButtonText: '{{ __("حسناً") }}'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
