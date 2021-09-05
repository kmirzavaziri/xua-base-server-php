<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?\Services\XUA\FileInstance Q_idBookletPicture
 * @method static MethodItemSignature Q_idBookletPicture() The Signature of: Request Item `idBookletPicture`
 */
class SetOneIdBookletPicture extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_idBookletPicture(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->idBookletPicture and file_exists($user->idBookletPicture->path)) {
            unlink($user->idBookletPicture->path);
        }
        $this->Q_idBookletPicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . User::table() . DIRECTORY_SEPARATOR . $user->id);
        $user->idBookletPicture = $this->Q_idBookletPicture;
        $user->store();
    }
}