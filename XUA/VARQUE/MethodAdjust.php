<?php

namespace XUA\VARQUE;

use Services\XUA\ConstantService;
use Services\XUA\FileInstanceSame;
use Supers\Basics\Files\Generic;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\MethodEve;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

abstract class MethodAdjust extends MethodEve
{
    # Finalize Eve Methods
    final protected static function requestSignaturesCalculator(): array
    {
        $request = parent::requestSignaturesCalculator();
        $fields = static::fields();
        foreach ($fields as $field) {
            $request[$field->root->name()] = new MethodItemSignature($field->root->type(), $field->required, $field->default, $field->const);
        }
        return $request;
    }

    final protected static function responseSignaturesCalculator(): array
    {
        return parent::responseSignaturesCalculator();
    }

    protected function body(): void
    {
        $feed = $this->feed();
        $fields = static::fields();
        foreach ($fields as $field) {
            try {
                if ($field->root->name() != 'id') {
                    if (!is_a($field->root->type(), Generic::class)) {
                        $feed->{$field->root->name()} = $field->root->valueFromRequest($this->{'Q_' . $field->root->name()}, $feed);
                    } else {
                        // What about Recursive fields in children?
                        if (!is_a($this->{'Q_' . $field->root->name()}, FileInstanceSame::class)) {
                            if ($feed->{$field->root->name()} and file_exists($feed->{$field->root->name()}->path)) {
                                unlink($feed->{$field->root->name()}->path);
                            }
                            $this->{'Q_' . $field->root->name()}?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . static::entity()::table() . DIRECTORY_SEPARATOR . $feed->id);
                            $feed->{$field->root->name()} = $this->{'Q_' . $field->root->name()};
                        }
                    }
                }
            } catch (EntityFieldException $e) {
                $this->error->fromException($e);
                $this->throwError();
            }
        }
        try {
            $feed->store();
        } catch (EntityFieldException $e) {
            throw $this->error->fromException($e);
        }
    }

    # New Overridable Methods
    abstract protected static function entity(): string;

    /**
     * @return VarqueMethodFieldSignature[]
     */
    abstract protected static function fields(): array;

    abstract protected function feed(): Entity;
}