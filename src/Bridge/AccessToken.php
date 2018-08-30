<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter\Bridge;

use Yaquawa\Laravel\PassportBinaryUuidAdapter\Helper;
use Laravel\Passport\Bridge\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    public function setUserIdentifier($identifier)
    {
        parent::setUserIdentifier(Helper::encodeUuid($identifier));
    }

    public function getUserIdentifier()
    {
        return Helper::decodeUuid($this->userIdentifier);
    }
}