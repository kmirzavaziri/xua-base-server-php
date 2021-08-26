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
 * @property ?\Services\XUA\FileInstance Q_profilePicture
 * @method static MethodItemSignature Q_profilePicture() The Signature of: Request Item `profilePicture`
 */
class SetOneProfilePicture extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_profilePicture(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->profilePicture and file_exists($user->profilePicture->path)) {
            unlink($user->profilePicture->path);
        }
        $this->Q_profilePicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . User::table() . DIRECTORY_SEPARATOR . $user->id);
        $user->profilePicture = $this->Q_profilePicture;
        $user->store();
    }
}