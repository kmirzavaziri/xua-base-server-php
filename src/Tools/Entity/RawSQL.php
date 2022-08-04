<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Eves\Service;

class RawSQL extends Service
{
    public function __construct(public string $value) {}
}