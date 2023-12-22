<?php

namespace App\data;

use Illuminate\Support\Facades\File;

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

    /**
     * Save the meta to disk. Takes a type parameter to determine the path to save to.
     *
     * @param string $type
     * @return void
     */
    public function saveToDisk(string $type): void
    {
        $metaFilePath = $this->getMetaFilePath($type);

        echo "Saving meta JSON for type $type to $metaFilePath\n";

        // Save the meta, creating its path directories recursively if needed.
        File::ensureDirectoryExists(dirname($metaFilePath));
        File::put($metaFilePath, $this->toJson(JSON_PRETTY_PRINT));
    }

    /**
     * Gets the file path for this data file. Requires a type parameter to determine the path.
     *
     * @param string $type
     * @return string
     */
    public function getMetaFilePath(string $type): string
    {
        // TODO: Construct this once somewhere!
        $baseFilePath = config('scraping.public_path') . '/data';

        // Ensure that the basePath ends with a slash
        $baseFilePath = rtrim($baseFilePath, '/') . '/';

        // Construct the path to the file
        $metaPath = $type . '/' . 'meta.json';
        return $baseFilePath . $metaPath;
    }

    private function toJson(int $options)
    {
        return json_encode([
            'pages' => $this->pages,
            'total' => $this->total,
        ], $options);
    }
}
