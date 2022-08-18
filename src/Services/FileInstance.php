<?php

namespace Xua\Core\Services;

use Exception;
use Xua\Core\Eves\Service;

class FileInstance extends Service
{
    public string $mime;
    public int $size;
    public string $url;

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

    public static function fromUrl(string $url): ?static {
        $path = ConstantService::get('config', 'services.storage.path') . DIRECTORY_SEPARATOR .
            substr($url, strlen(ConstantService::get('config', 'services.storage.url')));
        if (file_exists($path)) {
            $return = new static($path);
            $return->url = $url;
            return $return;
        } else {
            return null;
        }
    }

    public function newName(string $dir): string
    {
        do {
            $filename = md5(SecurityService::getRandomSalt(32) . '|' . (new DateTimeInstance())->getTimestamp()) . '.' . $this->extension;
        } while (file_exists($dir . DIRECTORY_SEPARATOR . $filename));

        return $filename;
    }

    public function store($innerDir)
    {
        if (!$this->stored) {
            $this->stored = true;

            $dir = ConstantService::get('config', 'services.storage.path') . DIRECTORY_SEPARATOR . $innerDir;
            $newName = $this->newName($dir);
            $newPath = $dir . DIRECTORY_SEPARATOR . $newName;
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            if (!file_exists($dir)) {
                throw new Exception('Somehow failed');
            }
            move_uploaded_file($this->path, $newPath);

            $this->path = $newPath;
            $this->url = ConstantService::get('config', 'services.storage.url') . '/' . $innerDir . $newName;
        }
    }
}