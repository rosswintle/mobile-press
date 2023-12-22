<?php

namespace App\data;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

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

    /**
     * @var integer
     */
    public $page = 0;

    /**
     * @var string
     */
    public $type = '';

    public static function fromArray(array $data): WordPressResponse
    {
        $instance = new self();
        $instance->posts = collect($data['data'])->map(fn($post) => Post::fromArray($post));
        $instance->meta = WordPressResponseMeta::fromArray($data['meta']);
        return $instance;
    }

    public function saveToDisk()
    {
        $indexFilePath = $this->getDataFilePath();

        echo "Saving index JSON for type $this->type page $this->page to $indexFilePath\n";

        // Save the post, creating its path directories recursively if needed. Use Laravel helpers.
        File::ensureDirectoryExists(dirname($indexFilePath));
        File::put($indexFilePath, $this->posts->toJson(JSON_PRETTY_PRINT));

        $this->saveMetaToDisk();
    }

    /**
     * Gets the file path for this data file
     *
     * @return string
     */
    public function getDataFilePath(): string
    {
        // TODO: Construct this once somewhere!
        $baseFilePath = config('scraping.public_path') . '/data';

        // Ensure that the basePath ends with a slash
        $baseFilePath = rtrim($baseFilePath, '/') . '/';

        // Construct the path to the file
        $postPath = $this->type . '/' . $this->page;
        return $baseFilePath . $postPath . '.json';
    }

    private function saveMetaToDisk()
    {
        if ($this->meta) {
            $this->meta->saveToDisk($this->type);
        }
    }
}
