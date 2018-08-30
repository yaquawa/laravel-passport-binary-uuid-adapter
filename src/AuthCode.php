<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\AuthCode as BaseModel;

class AuthCode extends BaseModel
{
    public $timestamps = false;

    use BinaryUuidFilter;
}