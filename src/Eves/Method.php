<?php

namespace XUA\Eves;

abstract class Method extends MethodEve
{
    final static protected function requestSignaturesCalculator() : array
    {
        return parent::requestSignaturesCalculator();
    }

    final static protected function responseSignaturesCalculator() : array
    {
        return parent::responseSignaturesCalculator();
    }
}