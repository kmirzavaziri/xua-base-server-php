<?php

namespace Services\XUA;

use Services\Mime;
use Services\UserService;
use XUA\Service;

class FileInstance extends Service
{
    public string $mime;
    public int $size;

    public function __construct(
        public string $path,
        public ?string $extension = null,
        public array $unifiers = [],
        public bool $stored = true,
        public bool $unified = true,
    )
    {
        $this->mime = mime_content_type($this->path);
        $this->size = filesize($this->path);
        if (!$this->extension) {
            $this->extension = pathinfo($this->path, PATHINFO_EXTENSION);
        }
    }

    public function newName(string $dir, ?string $seed = null): string
    {
        if (!$seed) {
            $seed = UserService::session()->id ?? 'guest';
        }

        do {
            $filename = md5($seed . '|' . SecurityService::getRandomSalt(32) . '|' . (new DateTimeInstance())->getTimestamp()) . '.' . $this->extension;
        } while (file_exists($dir . DIRECTORY_SEPARATOR . $filename));

        return $dir . DIRECTORY_SEPARATOR . $filename;
    }

    public function store(string $dir, ?string $seed = null)
    {
        if (!$this->unified) {
            $this->unified = true;
            foreach ($this->unifiers as $unifier) {
                if ($extension = Mime::unify($this->mime, $unifier, $this->path)) {
                    $this->extension = $extension;
                    break;
                }
            }
        }
        if (!$this->stored) {
            $this->stored = true;
            $newName = $this->newName($dir, $seed);

            if (!file_exists($dir)) {
                mkdir($dir, 0666, true);
            }
            move_uploaded_file($this->path, $newName);
            $this->path = $newName;
        }
    }
}