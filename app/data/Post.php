<?php

namespace App\data;

use App\Enums\PostStatus;

class Post
{
    public int $id;

    public string $dateGmt;

    public string $modifiedDateGmt;

    public string $slug;

    public PostStatus $status;

    public string $type;

    public string $link;

    public string $title;

    public string $content;

    public string $excerpt;

    public int $parentId;

    public Post $parent;

    public int $authorId;

    public int $featuredMediaId;

    public static function fromArray(array $data): Post
    {
        $instance = new self();

        $instance->id = $data['id'];
        $instance->dateGmt = $data['date_gmt'];
        $instance->modifiedDateGmt = $data['modified_gmt'];
        $instance->slug = $data['slug'];
        $instance->status = PostStatus::from($data['status']);
        $instance->type = $data['type'];
        $instance->link = $data['link'];
        $instance->title = $data['title']['rendered'];
        $instance->content = $data['content']['rendered'];
        $instance->excerpt = $data['excerpt']['rendered'];
        $instance->parentId = $data['parent'] ?? 0;
//        $instance->parent = Post::fromArray($data['parent']);
        $instance->authorId = $data['author'];
        $instance->featuredMediaId = $data['featured_media'];

        return $instance;
    }
}
