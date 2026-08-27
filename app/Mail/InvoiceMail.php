<?php

namespace App\Mail;

use App\Models\Debt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $debt;
    public $customNote;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Debt $debt, $customNote = null)
    {
        $this->debt = $debt;
        $this->customNote = $customNote;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invNo = str_pad($this->debt->id, 5, '0', STR_PAD_LEFT) . '/' . ($this->debt->created_at ? $this->debt->created_at->format('Y') : date('Y'));
        $filename = 'facture-' . str_pad($this->debt->id, 5, '0', STR_PAD_LEFT) . '.pdf';

        $mail = $this->subject(__('فاتورة رقم') . ' #' . $invNo . ' - ' . __('مؤسسة عدة بن قصير لمستلزمات البناء'))
                     ->view('emails.invoice');

        try {
            $pdf = PDF::loadView('content.Printer.facteur-pdf', ['debt' => $this->debt]);
            $mail->attachData($pdf->output(), $filename, [
                'mime' => 'application/pdf',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Could not attach invoice PDF: ' . $e->getMessage());
        }

        return $mail;
    }
}
