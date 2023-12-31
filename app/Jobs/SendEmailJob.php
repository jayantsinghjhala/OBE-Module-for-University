<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendEmail;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // Change the timeout to 2 minutes

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5; // Change the number of attempts to 5

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $retryAfter = 300;
    /**
     * Create a new job instance.
     */
    protected $details;
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // dd("test");
        $email = new SendEmail($this->details); // Pass $this->details to the Mailable
        Mail::to($this->details["email"])->send($email);
    }
}

// <?php
// namespace App\Jobs;

// use Illuminate\Bus\Queueable;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use App\Mail\SendEmail;
// use Mail;

// class SendEmailJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
//     protected $details;
//     /**
//      * Create a new job instance.
//      *
//      * @return void
//      */
//     public function __construct($details)
//     {
//         $this->details = $details;
//     }

//     /**
//      * Execute the job.
//      *
//      * @return void
//      */
//     public function handle()
//     {
//         // Extract any additional parameters you need from $this->details
//         $email = new SendEmail($this->details); // Pass $this->details to the Mailable
//         Mail::to($this->details['email'])->send($email);
//     }
// }


