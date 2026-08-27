<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Repositories\Debt\DebtRepository;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class PrinterController extends Controller
{
    private $debt;

    public function __construct(DebtRepository $debt)
    {
        $this->debt = $debt;
    }

    public function factuerClient($id, $fullname)
    {
        $debt = $this->debt->find($id);
        
        if (!$debt) {
            abort(404, __('Invoice not found.'));
        }

        return view('content.Printer.facteur-client', compact('debt'));
    }

    /**
     * Download invoice as an Arabic RTL PDF
     */
    public function downloadPdf($id, $fullname = null)
    {
        $debt = $this->debt->find($id);

        if (!$debt) {
            abort(404, __('Invoice not found.'));
        }

        $invNo = str_pad($debt->id, 5, '0', STR_PAD_LEFT);
        $filename = "facture-{$invNo}.pdf";

        $pdf = PDF::loadView('content.Printer.facteur-pdf', compact('debt'));

        return $pdf->download($filename);
    }

    /**
     * Stream invoice as an Arabic RTL PDF in browser for viewing / printing
     */
    public function streamPdf($id, $fullname = null)
    {
        $debt = $this->debt->find($id);

        if (!$debt) {
            abort(404, __('Invoice not found.'));
        }

        $invNo = str_pad($debt->id, 5, '0', STR_PAD_LEFT);
        $filename = "facture-{$invNo}.pdf";

        $pdf = PDF::loadView('content.Printer.facteur-pdf', compact('debt'));

        return $pdf->stream($filename);
    }

    public function sendEmail(Request $request, $id)
    {
        $debt = $this->debt->find($id);

        if (!$debt) {
            return response()->json(['success' => false, 'message' => __('Invoice not found.')], 404);
        }

        // Support both 'emails' (array or string) and 'email' (string or array)
        $rawEmails = $request->input('emails', $request->input('email', []));

        if (is_string($rawEmails)) {
            $rawEmails = preg_split('/[;,\s]+/', $rawEmails, -1, PREG_SPLIT_NO_EMPTY);
        }

        $emails = [];
        if (is_array($rawEmails)) {
            foreach ($rawEmails as $email) {
                $email = trim($email);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                }
            }
        }

        $emails = array_values(array_unique($emails));

        if (empty($emails)) {
            return response()->json([
                'success' => false,
                'message' => __('يرجى تحديد أو إدخال عنوان بريد إلكتروني صحيح واحد على الأقل.')
            ], 422);
        }

        try {
            Mail::to($emails)->send(new InvoiceMail($debt, $request->input('note')));
            $recipientsList = implode(', ', $emails);
            return response()->json([
                'success' => true,
                'message' => __('تم إرسال الفاتورة بنجاح إلى: ') . $recipientsList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('فشل إرسال البريد الإلكتروني: ') . $e->getMessage()
            ], 500);
        }
    }
}
