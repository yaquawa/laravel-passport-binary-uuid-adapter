<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Laravel\Passport\Client as BaseModel;

class Client extends BaseModel
{
    use BinaryUuidFilter;
}