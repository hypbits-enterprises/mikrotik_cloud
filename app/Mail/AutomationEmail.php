<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AutomationEmail extends Mailable
{
    public string $htmlBody;
    private ?string $pdfBytes;
    private ?string $pdfName;

    public function __construct(string $subject, string $htmlBody, ?string $pdfBytes = null, ?string $pdfName = null)
    {
        $this->subject($subject);
        $this->htmlBody = $htmlBody;
        $this->pdfBytes = $pdfBytes;
        $this->pdfName  = $pdfName;
    }

    public function build(): static
    {
        $mail = $this->view('email_templates.automation');
        if ($this->pdfBytes !== null) {
            $mail->attachData($this->pdfBytes, $this->pdfName ?? 'receipt.pdf', ['mime' => 'application/pdf']);
        }
        return $mail;
    }
}
