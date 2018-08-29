<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\CryptKey;
use Laravel\Passport\Bridge\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * Generate a JWT from the access token
     *
     * @param CryptKey $privateKey
     *
     * @return Token
     */
    public function convertToJWT(CryptKey $privateKey)
    {
        return (new Builder())
            ->setAudience($this->getClient()->getIdentifier())
            ->setId($this->getIdentifier(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($this->getExpiryDateTime()->getTimestamp())
            ->setSubject(Helper::decodeUuid($this->getUserIdentifier()))
            ->set('scopes', $this->getScopes())
            ->sign(new Sha256(), new Key($privateKey->getKeyPath(), $privateKey->getPassPhrase()))
            ->getToken();
    }
}