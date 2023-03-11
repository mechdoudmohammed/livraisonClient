<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class addArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add an article';

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
        DB::table('articles')->insert([
            "nom_article" => 'test',
            "prix_article" => 199,
            "stock_article" => 0,
            "etat_article" => "En traitement",
            "id_client" => 2,
        ]);
        $this->info('Done!');
    }
}
