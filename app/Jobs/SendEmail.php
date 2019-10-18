<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $view;
    protected $parameter;
    protected $to;
    protected $subject;

    /**
     * Create a new job instance.
     * @param string $view
     * @param array $parameter
     * @param string $to
     * @param string $subject
     * @return void
     */
    public function __construct($view, $parameter, $to, $subject)
    {
        $this->view = $view;
        $this->parameter = $parameter;
        $this->to = $to;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send(
            $this->view,
            $this->parameter,
            function ($message) {
                $message->to($this->to)->subject($this->subject);
            }
        );
    }
}
