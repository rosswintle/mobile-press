<?php

namespace App\data;

use Illuminate\Support\Collection;

class WordPressResponse
{
    /**
     * @var ?Collection<Post>
     */
    public ?Collection $posts = null;

    /**
     * @var ?WordPressResponseMeta
     */
    public ?WordPressResponseMeta $meta = null;

    public static function fromArray(array $data): WordPressResponse
    {
        $instance = new self();
        $instance->posts = collect($data['data'])->map(fn($post) => Post::fromArray($post));
        $instance->meta = WordPressResponseMeta::fromArray($data['meta']);
        return $instance;
    }
}
