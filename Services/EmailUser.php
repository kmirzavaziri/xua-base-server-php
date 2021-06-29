<?php


namespace Services;


use XUA\Service;

final class EmailUser extends Service
{
    public function __construct(
        public string $address,
        public string $name = '',
    ) {}
}