<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class initDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the db';

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
        $bar = $this->output->createProgressBar(4);
        Artisan::call('key:generate');
        $bar->setFormat('%bar%');
        $bar->start();

        system('composer dump-autoload');
        $bar->advance();

        if (Schema::hasTable('oauth_clients')) {
            $this->call('passport:install', ['--force' => true]);
            $bar->advance();
        }

        $this->call('migrate:fresh');
        $bar->advance();

        $this->call('db:seed');
        $bar->advance();

        $this->call('passport:install', ['--force' => true]);
        $bar->advance();

        $bar->finish();
    }
}
