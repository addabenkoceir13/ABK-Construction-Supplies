<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} #{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            direction: rtl;
            text-align: right;
            color: #333333;
        }
        .email-container {
            max-width: 680px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        .email-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0 0 8px 0;
            font-size: 24px;
            font-weight: 700;
        }
        .email-header p {
            margin: 0;
            font-size: 14px;
            color: #94a3b8;
        }
        .email-body {
            padding: 30px;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .table th {
            background-color: #f1f5f9;
            color: #475569;
            padding: 12px;
            font-size: 13px;
            text-align: right;
            border-bottom: 2px solid #cbd5e1;
        }
        .table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-table {
            width: 50%;
            margin-right: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 12px;
            font-size: 14px;
        }
        .summary-table .total-row {
            font-weight: bold;
            font-size: 16px;
            color: #0f172a;
            border-top: 2px solid #0f172a;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .note-box {
            background-color: #eff6ff;
            border-right: 4px solid #3b82f6;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>مؤسسة عدة بن قصير لمستلزمات البناء</h1>
            <p>تفاصيل الفاتورة #{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}/{{ $debt->created_at ? $debt->created_at->format('Y') : date('Y') }}</p>
        </div>

        <div class="email-body">
            @if(!empty($customNote))
                <div class="note-box">
                    <strong>{{ __('ملاحظة:') }}</strong> {{ $customNote }}
                </div>
            @endif

            <p style="font-size: 16px; font-weight: 600;">{{ __('مرحباً') }} {{ $debt->fullname }}،</p>
            <p style="color: #475569;">{{ __('مرفق أدناه تفاصيل الفاتورة الخاصة بكم من مؤسسة عدة بن قصير لمستلزمات البناء:') }}</p>

            <div class="info-card">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;">
                            <strong>{{ __('رقم الفاتورة:') }}</strong> #{{ str_pad($debt->id, 5, '0', STR_PAD_LEFT) }}<br>
                            <strong>{{ __('تاريخ الإصدار:') }}</strong> {{ $debt->date_debut_debt }}
                        </td>
                        <td style="width: 50%;">
                            <strong>{{ __('العميل:') }}</strong> {{ $debt->fullname }}<br>
                            <strong>{{ __('رقم الهاتف:') }}</strong> {{ $debt->phone }}
                        </td>
                    </tr>
                </table>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('المنتج / التفاصيل') }}</th>
                        <th>{{ __('الكمية') }}</th>
                        <th>{{ __('المجموع') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($debt->getDebtProduct as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name_category }}</td>
                            <td>{{ $item->quantity }} {{ optional($item->getSubcategory)->display_name }}</td>
                            <td>{{ number_format($item->amount, 2) }} {{ __('د.ج') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary-table">
                <tr>
                    <td>{{ __('المجموع الكلي:') }}</td>
                    <td style="text-align: left;"><strong>{{ number_format($debt->total_debt_amount, 2) }} {{ __('د.ج') }}</strong></td>
                </tr>
                <tr>
                    <td>{{ __('المبلغ المدفوع:') }}</td>
                    <td style="text-align: left; color: #16a34a;">{{ number_format($debt->debt_paid ?? 0, 2) }} {{ __('د.ج') }}</td>
                </tr>
                <tr class="total-row">
                    <td>{{ __('الرصيد المتبقي:') }}</td>
                    <td style="text-align: left; color: #dc2626;">{{ number_format($debt->rest_debt_amount ?? ($debt->total_debt_amount - $debt->debt_paid), 2) }} {{ __('د.ج') }}</td>
                </tr>
            </table>
        </div>

        <div class="email-footer">
            <p style="margin: 0 0 5px 0;">مؤسسة عدة بن قصير لمستلزمات البناء</p>
            <p style="margin: 0;">الهاتف: 0661785937 / 0770932767</p>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #94a3b8;">شكراً لتعاملكم معنا!</p>
        </div>
    </div>
</body>
</html>
