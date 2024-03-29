<?php

namespace Suitcorecms\Sites\Newsletters\Console;

use Illuminate\Console\Command;
use Suitcorecms\Sites\Newsletters\Newsletter;

class SendingNewsletterToCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suitcorecms:newsletter-sendto {id} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Newsletter by id that scheduled to an email address';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            list($newsletters, $sents, $unsents) = Newsletter::send($this->argument('id'), $email = $this->argument('email'));
            foreach ($newsletters as $newsletter) {
                foreach ($sents[$newsletter->id] as $sent) {
                    $this->info("Newsletter #{$newsletter->id} ({$newsletter->title}) has been sent to {$sent}.");
                }
                foreach ($unsents[$newsletter->id] as $unsent) {
                    $this->info("Newsletter #{$newsletter->id} ({$newsletter->title}) failed to be sent to {$unsent}.");
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
