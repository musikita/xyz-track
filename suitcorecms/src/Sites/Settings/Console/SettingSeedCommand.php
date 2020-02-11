<?php

namespace Suitcorecms\Sites\Settings\Console;

use Illuminate\Console\Command;
use Suitcorecms\Sites\Settings\Setting;

class SettingSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suitcorecms:seed-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Pre-Defined Settings';

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
        Setting::seedDatabase();
        $this->info('Config has been seeded.');
    }
}
