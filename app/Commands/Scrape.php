<?php

namespace App\Commands;

use App\data\PostList;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

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
        $this->copyTemplates();
    }

    protected function getPagesList()
    {
        $pages = new PostList('pages');
        $pages->fetchList();
//        ray($pages);
    }

    protected function getPostsList()
    {
        $posts = new PostList('posts');
        $posts->fetchList();
//        ray($posts);
    }

    private function copyTemplates()
    {
        File::copy(__DIR__ . '/../../resources/templates/index.html', config('scraping.public_path') . '/index.html');
        File::ensureDirectoryExists(config('scraping.public_path') . '/js');
        File::copy(__DIR__ . '/../../resources/js/main.js', config('scraping.public_path') . '/js/main.js');
    }
}

