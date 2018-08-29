<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\Bridge\AccessTokenRepository as BaseAccessTokenRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class AccessTokenRepository extends BaseAccessTokenRepository
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessToken($userIdentifier, $scopes);
    }
}