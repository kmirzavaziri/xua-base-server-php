<?php

namespace Services;

use Entities\Dataset\IranAdministrativeDivision;
use XUA\Service;

abstract class IranAdministrativeDivisionService extends Service
{
    public static function getSpecificLevel(IranAdministrativeDivision $geographicDivision, string $type): ?IranAdministrativeDivision
    {
        while ($geographicDivision !== null and $geographicDivision->type != $type) {
            $geographicDivision = $geographicDivision->parent;
        }
        return $geographicDivision;
    }
}