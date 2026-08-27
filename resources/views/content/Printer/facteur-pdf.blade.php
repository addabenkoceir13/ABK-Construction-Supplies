<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('فاتورة') }} - #{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
            margin-top: 15mm;
            margin-bottom: 18mm;
            margin-left: 10mm;
            margin-right: 10mm;
        }

        body {
            font-family: 'tajawal', 'almarai', 'sans-serif';
            direction: rtl;
            text-align: right;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* General Utilities */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; }
        .text-primary { color: #1d4ed8; }
        .text-success { color: #15803d; }
        .text-danger { color: #b91c1c; }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-logo {
            max-height: 70px;
            max-width: 140px;
        }

        .company-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }

        .company-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin: 0 0 3px 0;
        }

        .company-contact {
            font-size: 9pt;
            color: #334155;
            direction: ltr;
            text-align: right;
        }

        .invoice-title {
            font-size: 22pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .invoice-number {
            font-size: 12pt;
            font-weight: bold;
            color: #2563eb;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 6px;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .badge-partial {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* Metrics Bar */
        .metrics-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .metric-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }

        .metric-box.total {
            background-color: #eff6ff;
            border-color: #bfdbfe;
        }

        .metric-box.paid {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }

        .metric-box.rest {
            background-color: #fef2f2;
            border-color: #fecaca;
        }

        .metric-label {
            font-size: 8pt;
            color: #64748b;
            margin-bottom: 3px;
        }

        .metric-val {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Party Cards */
        .party-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }

        .party-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
            width: 50%;
        }

        .party-card-header {
            font-size: 10pt;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .party-row {
            margin-bottom: 4px;
            font-size: 9.5pt;
        }

        /* Products Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5pt;
            font-weight: bold;
            padding: 8px 10px;
            border: 1px solid #0f172a;
        }

        .items-table td {
            padding: 8px 10px;
            font-size: 9.5pt;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Bottom Section: Notes & Summary */
        .bottom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 15px;
        }

        .notes-card {
            background-color: #eff6ff;
            border-right: 4px solid #2563eb;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 9pt;
            color: #1e40af;
            vertical-align: top;
            width: 50%;
        }

        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
            width: 50%;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 0;
            font-size: 9.5pt;
        }

        .summary-table .grand-total {
            border-top: 2px solid #0f172a;
            margin-top: 5px;
            padding-top: 6px;
            font-weight: bold;
            font-size: 11pt;
            color: #0f172a;
        }

        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #475569;
            margin-bottom: 45px;
        }

        .signature-line {
            width: 70%;
            margin: 0 auto;
            border-bottom: 1px dashed #94a3b8;
        }

        /* Footer */
        .footer-text {
            font-size: 8.5pt;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    @php
        $paid = is_numeric($debt->debt_paid) ? (float)$debt->debt_paid : 0;
        $total = is_numeric($debt->total_debt_amount) ? (float)$debt->total_debt_amount : 0;
        $rest = is_numeric($debt->rest_debt_amount) ? (float)$debt->rest_debt_amount : ($total - $paid);
        $invoiceNo = str_pad($debt->id, 5, '0', STR_PAD_LEFT) . '/' . ($debt->created_at ? $debt->created_at->format('Y') : date('Y'));
        $logoPath = public_path('assets/img/logos/logo-v2.jpg');
    @endphp

    <!-- HEADER SECTION -->
    <table class="header-table">
        <tr>
            <!-- Company Info & Logo -->
            <td style="width: 60%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        @if(file_exists($logoPath))
                            <td style="width: 80px; vertical-align: top;">
                                <img src="{{ $logoPath }}" class="header-logo" alt="Logo">
                            </td>
                        @endif
                        <td style="vertical-align: top; padding-right: 8px;">
                            <div class="company-title">مؤسسة عدة بن قصير</div>
                            <div class="company-subtitle">لمستلزمات البناء ومواد التشييد بالجملة والتجزئة</div>
                            <div class="company-contact">الهاتف: 06 61785937 / 07 70932767</div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Invoice Details & Badge -->
            <td style="width: 40%; vertical-align: top; text-align: left;">
                <div class="invoice-title">فاتورة</div>
                <div class="invoice-number">رقم: #{{ $invoiceNo }}</div>
                <div>
                    @if($debt->status === 'paid' || $rest <= 0)
                        <span class="status-badge badge-paid">مدفوعة بالكامل</span>
                    @elseif($paid > 0 && $rest > 0)
                        <span class="status-badge badge-partial">مدفوعة جزئياً</span>
                    @else
                        <span class="status-badge badge-unpaid">غير مدفوعة</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- KEY METRICS ROW -->
    <table class="metrics-table">
        <tr>
            <td style="width: 25%;">
                <div class="metric-box">
                    <div class="metric-label">تاريخ الإصدار</div>
                    <div class="metric-val text-dark">{{ $debt->date_debut_debt ? \Carbon\Carbon::parse($debt->date_debut_debt)->format('Y-m-d') : date('Y-m-d') }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box total">
                    <div class="metric-label">المبلغ الإجمالي</div>
                    <div class="metric-val text-primary">{{ number_format($total, 2) }} <small style="font-size: 8pt;">د.ج</small></div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box paid">
                    <div class="metric-label">المبلغ المدفوع</div>
                    <div class="metric-val text-success">{{ number_format($paid, 2) }} <small style="font-size: 8pt;">د.ج</small></div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="metric-box rest">
                    <div class="metric-label">الرصيد المتبقي</div>
                    <div class="metric-val text-danger">{{ number_format($rest, 2) }} <small style="font-size: 8pt;">د.ج</small></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- CLIENT & SUPPLIER CARDS -->
    <table class="party-table">
        <tr>
            <!-- Client Info -->
            <td class="party-card">
                <div class="party-card-header">فاتورة موجهة إلى (العميل)</div>
                <div class="party-row">
                    <span class="text-muted">الاسم الكامل: </span>
                    <strong class="text-primary">{{ $debt->fullname }}</strong>
                </div>
                <div class="party-row">
                    <span class="text-muted">رقم الهاتف: </span>
                    <span dir="ltr">{{ $debt->phone ?? '---' }}</span>
                </div>
                @if($debt->date_end_debt)
                    <div class="party-row">
                        <span class="text-muted">تاريخ الاستحقاق: </span>
                        <span>{{ \Carbon\Carbon::parse($debt->date_end_debt)->format('Y-m-d') }}</span>
                    </div>
                @endif
            </td>

            <!-- Supplier Info -->
            <td class="party-card">
                <div class="party-card-header">صادر عن (المورد)</div>
                <div class="party-row">
                    <span class="text-muted">المؤسسة: </span>
                    <strong>مؤسسة عدة بن قصير لمستلزمات البناء</strong>
                </div>
                <div class="party-row">
                    <span class="text-muted">أرقام التواصل: </span>
                    <span dir="ltr">06 61785937 / 07 70932767</span>
                </div>
                <div class="party-row">
                    <span class="text-muted">النشاط: </span>
                    <span>بيع ومواد البناء والتشييد بالجملة والتجزئة</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">#</th>
                <th class="text-right">تفاصيل المنتج / التعيين</th>
                <th style="width: 110px;" class="text-center">الكمية والوحدة</th>
                <th style="width: 100px;" class="text-left">سعر الوحدة</th>
                <th style="width: 110px;" class="text-left">المجموع (د.ج)</th>
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
                    <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->name_category }}</div>
                    </td>
                    <td class="text-center">
                        {{ $qty }} {{ $unitName }}
                    </td>
                    <td class="text-left">
                        {{ number_format($unitPrice, 2) }}
                    </td>
                    <td class="text-left fw-bold text-primary">
                        {{ number_format($amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding: 15px;">
                        لا توجد عناصر مسجلة في هذه الفاتورة.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- BOTTOM SECTION: NOTES & FINANCIAL SUMMARY -->
    <table class="bottom-table">
        <tr>
            <!-- Notes -->
            <td class="notes-card">
                @if(!empty($debt->note))
                    <div style="font-weight: bold; margin-bottom: 4px;">ملاحظات الفاتورة:</div>
                    <div style="margin-bottom: 8px;">{{ $debt->note }}</div>
                @endif
                <div style="font-size: 8.5pt; color: #475569; line-height: 1.5;">
                    • جميع البضائع المباعة خاضعة لشروط وأحكام المؤسسة.<br>
                    • شكراً لثقتكم بنا وبسدادكم في المواعيد المحددة.
                </div>
            </td>

            <!-- Financial Summary -->
            <td class="summary-card">
                <table class="summary-table">
                    <tr>
                        <td class="text-muted">المجموع الإجمالي:</td>
                        <td class="text-left fw-bold">{{ number_format($total, 2) }} د.ج</td>
                    </tr>
                    <tr>
                        <td class="text-muted">المبلغ المدفوع (المعجل):</td>
                        <td class="text-left fw-bold text-success">- {{ number_format($paid, 2) }} د.ج</td>
                    </tr>
                    <tr class="grand-total">
                        <td>الرصيد المتبقي المستحق:</td>
                        <td class="text-left text-danger">{{ number_format($rest, 2) }} د.ج</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SIGNATURES SECTION -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-title">توقيع وخاتم المورد</div>
                <div class="signature-line"></div>
            </td>
            <td class="signature-cell">
                <div class="signature-title">توقيع الزبون / المستلم</div>
                <div class="signature-line"></div>
            </td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer-text">
        مؤسسة عدة بن قصير لمستلزمات البناء - ABK Construction Supplies | الهاتف: 06 61785937 / 07 70932767
    </div>

</body>
</html>
