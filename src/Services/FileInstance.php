<?php

namespace XUA\Services;

use Exception;
use XUA\Eves\Service;

class FileInstance extends Service
{
    public string $mime;
    public int $size;

    public function __construct(
        public string $path,
        public ?string $extension = null,
        public bool $stored = true,
    )
    {
        $this->mime = mime_content_type($this->path);
        $this->size = filesize($this->path);
        if (!$this->extension) {
            $this->extension = pathinfo($this->path, PATHINFO_EXTENSION);
        }
    }

    public function newName(string $dir, string $seed = ''): string
    {
        do {
            $filename = md5($seed . '|' . SecurityService::getRandomSalt(32) . '|' . (new DateTimeInstance())->getTimestamp()) . '.' . $this->extension;
        } while (file_exists($dir . DIRECTORY_SEPARATOR . $filename));

        return $dir . DIRECTORY_SEPARATOR . $filename;
    }

    public function store(string $dir, ?string $seed = null)
    {
        if (!$this->stored) {
            $this->stored = true;
            $newName = $this->newName($dir, $seed);

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!file_exists($dir)) {
                throw new Exception('Somehow failed');
            }
            move_uploaded_file($this->path, $newName);
            $this->path = $newName;
        }
    }
}