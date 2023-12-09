<?php

namespace App\data;

class WordPressResponseMeta
{
    /*
     * Number of pages that meet the query
     *
     * @var int
     */
    public int $pages;

    /**
     * Total number of posts in the response
     *
     * @var int
     */
    public int $total;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): WordPressResponseMeta
    {
        $instance = new self();
        $instance->pages = $data['pages'];
        $instance->total = $data['total'];
        return $instance;
    }
}
