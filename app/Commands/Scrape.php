<?php

namespace App\Commands;

use App\data\PostList;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use RickWest\WordPress\Facades\WordPress;

class Scrape extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'scrape';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Scrapes the site for content';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->getPagesList();
        $this->getPostsList();
    }

    protected function getPagesList()
    {
        $pages = new PostList('pages');
        $pages->fetchList();
        ray($pages);
    }

    protected function getPostsList()
    {
        $posts = new PostList('posts');
        $posts->fetchList();
        ray($posts);
    }
}

