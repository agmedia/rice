<?php

namespace App\Console\Commands;

use App\Models\Back\Marketing\Email;
use Illuminate\Console\Command;

class SendReviewRequestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:review_request_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails to users 7 days after order.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 1;//Email::sendReviewRequestEmails();
    }
}
