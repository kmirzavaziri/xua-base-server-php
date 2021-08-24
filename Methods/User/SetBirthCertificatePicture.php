<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ConstantService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodAdjust;

/**
 * @property ?\Services\XUA\FileInstance Q_birthCertificatePicture
 * @method static MethodItemSignature Q_birthCertificatePicture() The Signature of: Request Item `birthCertificatePicture`
 */
class SetBirthCertificatePicture extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_birthCertificatePicture(), false, null, false),
        ];
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->birthCertificatePicture and file_exists($user->birthCertificatePicture->path)) {
            unlink($user->birthCertificatePicture->path);
        }
        $this->Q_birthCertificatePicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . $user->id);
        $user->birthCertificatePicture = $this->Q_birthCertificatePicture;
        $user->store();
    }
}