<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details=$details;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Determine which email view to use based on the details
        if (isset($this->details['otp'])) {
            // Use the OTP email view
            return $this->view('emails.otp')->with('data', $this->details);
        }// elseif (isset($this->details['customParam1'])) {
        //     // Use a custom email view for another use case
        //     return $this->view('emails.custom_email')->with('customParam1', $this->details['customParam1']);
        // }

        // Default to a fallback email view if no specific case matches
        return $this->view('emails.otp');
    }
}