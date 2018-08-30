<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter\Bridge;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\AccessTokenRepository as BaseAccessTokenRepository;

class AccessTokenRepository extends BaseAccessTokenRepository
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessToken($userIdentifier, $scopes);
    }
}