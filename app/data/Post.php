<?php

namespace App\data;

use App\Enums\PostStatus;
use Illuminate\Support\Facades\File;

class Post
{
    public array $dataAsArray;

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

        $instance->dataAsArray = $data;
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

    public function saveToDisk(): void
    {
        $postJsonPath = $this->getJsonPath();
        $postHtmlPath = $this->getHtmlPath();

        echo "Saving post $this->id to $postJsonPath\n";

        // Save the post, creating its path directories recursively if needed. Use Laravel helpers.
        File::ensureDirectoryExists(dirname($postJsonPath));
        File::put($postJsonPath, json_encode($this->dataAsArray, JSON_PRETTY_PRINT));

        File::ensureDirectoryExists(dirname($postHtmlPath));
        // We output HTML for pagefind to index. This requires a <body> tag.
        File::put($postHtmlPath, '<body><header><h1 data-pagefind-meta="title">' . $this->title . '</h1></header><main>' . $this->content . '</main></body>');
    }

    /**
     * Gets the file path for this post
     *
     * @param string $extension
     * @return string
     */
    public function getDataFilePath(string $extension): string
    {
        // TODO: Construct this once somewhere!
        $baseFilePath = config('scraping.public_path') . '/data';

        // Ensure that the basePath ends with a slash
        $baseFilePath = rtrim($baseFilePath, '/') . '/';

        // Home page is a special case
        if ($this->isHomePage()) {
            return $baseFilePath . 'home.' . $extension;
        }

        // Construct the path to the file
        $postPath = rtrim($this->link, '/');
        // Remove the old site's domain from the path
        $postPath = str_replace(config('scraping.old_site_home'), '', $postPath);
        return $baseFilePath . $postPath . '.' . $extension;
    }

    private function getJsonPath()
    {
        return $this->getDataFilePath('json');
    }

    private function getHtmlPath()
    {
        return $this->getDataFilePath('html');
    }

    /**
     * Returns true if this post is the home page
     *
     * @return bool
     */
    private function isHomePage(): bool
    {
        return $this->link === config('scraping.old_site_home');
    }

}
