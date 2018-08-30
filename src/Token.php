<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\Token as BaseModel;

class Token extends BaseModel
{
    use BinaryUuidFilter;
}