<?php


namespace XUA\Tools;


use XUA\Entity;
use XUA\Super;

class EntityFieldSignature
{
    public EntityRelObject $rel;

    public function __construct(
        public string $entity,
        public string $name,
        public Super $type,
        public $default = null,
    ) {
        $this->rel = new EntityRelObject($this->name);
        $this->name = $this->entity::table() . '.' . $this->name;
    }

    public static function processField(EntityFieldSignature $signature, &$arg) {
//        if (!$signature->type->accepts($arg, $messages)) {
//            throw new SuperArgumentException("$arg is not of type " . $signature->type . ": " . implode(' ', $messages));
//        }
    }


}