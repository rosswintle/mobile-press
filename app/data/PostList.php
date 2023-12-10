<?php

namespace App\data;

use Illuminate\Support\Collection;

class PostList
{
    /**
     * @var string
     */
    protected string $postType = '';

    /**
     * @var Collection<int>
     */
    protected Collection $idList;

    /**
     * @var int $pages
     */
    public int $pages = 0;

    /**
     * @param int $count
     */
    public int $count = 0;

    public function __construct(string $postType)
    {
        $this->idList = collect();
        $this->postType = $postType;
    }

    /**
     * @return void
     */
    public function fetchList(): void
    {
        $type = $this->postType;
        $page1 = WordPressResponse::fromArray(wordpress()->$type()->withOptions(['verify' => false])->get());
        $this->pages = $page1->meta->pages;
        $this->count = $page1->meta->total;
        $this->fetchAllIds(true);
    }

    /**
     * @param bool|null $savePostsToDisk
     * @return void
     */
    protected function fetchAllIds(?bool $savePostsToDisk = false): void
    {
        $type = $this->postType;
        $pageNums = collect(range(1, $this->pages));
        // Using map here load _all_ the posts into memory. So loop instead.
        foreach ($pageNums as $page) {
            $this->idList = $this->idList->merge($this->fetchPostIds($page, $savePostsToDisk));
        }
    }

    /**
     * Fetches a page of posts from the API. Optionally save the data to disk.
     *
     * @param int $page
     * @param bool|null $saveToDisk
     * @return Collection<Post>
     */
    public function fetchPosts(int $page, ?bool $saveToDisk = false): Collection
    {
        echo "Fetching page $page of {$this->pages} for {$this->postType}\n";
        $type = $this->postType;
        $response = WordPressResponse::fromArray(wordpress()->$type()->withOptions(['verify' => false])
            ->page($page)
            ->get());

        if ($saveToDisk) {
            $response->posts->each(function (Post $post) {
                $post->saveToDisk();
            });
        }

        return $response->posts;
    }

    /**
     * @return Collection<int>
     */
    public function fetchPostIds(int $page, ?bool $saveToDisk = false): Collection
    {
        return $this->fetchPosts($page, $saveToDisk)->pluck('id');
    }
}
