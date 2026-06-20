<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AutomationEmail extends Mailable
{
    public string $htmlBody;

    public function __construct(string $subject, string $htmlBody)
    {
        $this->subject($subject);
        $this->htmlBody = $htmlBody;
    }

    public function build(): static
    {
        return $this->view('email_templates.automation');
    }
}
