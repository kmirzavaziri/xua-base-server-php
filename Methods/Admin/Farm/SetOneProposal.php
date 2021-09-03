<?php

namespace Methods\Admin\Farm;

use Entities\Farm;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use Services\XUA\FileInstance;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?FileInstance Q_proposal
 * @method static MethodItemSignature Q_proposal() The Signature of: Request Item `proposal`
 */
class SetOneProposal extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Farm::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(Farm::F_proposal(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var Farm $farm */
        $farm = $this->feed();
        if ($farm->proposal and file_exists($farm->proposal->path)) {
            unlink($farm->proposal->path);
        }
        $this->Q_proposal?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . Farm::table() . DIRECTORY_SEPARATOR . $farm->id);
        $farm->proposal = $this->Q_proposal;
        $farm->store();
    }
}