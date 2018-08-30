<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\Token as BaseToken;

class Token extends BaseToken
{
    public function toArray()
    {
        $array            = parent::toArray();
        $array['user_id'] = Helper::decodeUuid($array['user_id']);

        return $array;
    }
}