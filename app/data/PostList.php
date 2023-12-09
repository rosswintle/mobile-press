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
        $this->fetchAllIds();
    }

    /**
     * @return void
     */
    protected function fetchAllIds(): void
    {
        $type = $this->postType;
        $pageNums = collect(range(1, $this->pages));
        $this->idList = $pageNums->map([$this, 'fetchPostIds'])->flatten();
    }

    /**
     * @param int $page
     * @return Collection<Post>
     */
    public function fetchPosts(int $page): Collection
    {
        $type = $this->postType;
        $response = WordPressResponse::fromArray(wordpress()->$type()->withOptions(['verify' => false])->page($page)->get());
        return $response->posts;
    }

    /**
     * @return Collection<int>
     */
    public function fetchPostIds(int $page): Collection
    {
        return $this->fetchPosts($page)->pluck('id');
    }
}
