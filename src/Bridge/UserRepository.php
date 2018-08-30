<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter\Bridge;

use RuntimeException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\Bridge\UserRepository as BaseUserRepository;

class UserRepository extends BaseUserRepository
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($models = config('auth.providers.' . $provider . '.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        foreach ((array)$models as $model) {
            if (method_exists($model, 'findForPassport')) {
                $user = (new $model)->findForPassport($username);
            } else {
                $user = (new $model)->where('email', $username)->first();
            }

            if ( ! $user) {
                continue;
            } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
                if ( ! $user->validateForPassportPasswordGrant($password)) {
                    continue;
                }
            } elseif ( ! $this->hasher->check($password, $user->getAuthPassword())) {
                continue;
            }

            return new User($user->getAuthIdentifier());
        }

        return null;
    }
}