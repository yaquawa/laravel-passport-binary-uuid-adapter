<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter\Bridge;

use Laravel\Passport\Passport;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Laravel\Passport\Bridge\AuthCodeRepository as BaseAuthCodeRepository;

class AuthCodeRepository extends BaseAuthCodeRepository
{
    public function getNewAuthCode()
    {
        return new AuthCode;
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $attributes = [
            'id' => $authCodeEntity->getIdentifier(),
            'user_id' => $authCodeEntity->getUserIdentifier(),
            'client_id' => $authCodeEntity->getClient()->getIdentifier(),
            'scopes' => $this->formatScopesForStorage($authCodeEntity->getScopes()),
            'revoked' => false,
            'expires_at' => $authCodeEntity->getExpiryDateTime(),
        ];

        Passport::authCode()->setRawAttributes($attributes)->save();
    }
}