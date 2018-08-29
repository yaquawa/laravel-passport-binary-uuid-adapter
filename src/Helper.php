<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Ramsey\Uuid\Uuid;

class Helper
{

    /**
     * Encode a `string UUID` to `binary UUID` if possible.
     *
     * @param $uuid
     *
     * @return string
     */
    public static function encodeUuid($uuid): string
    {
        if ( ! Uuid::isValid($uuid)) {
            return $uuid;
        }

        if ( ! $uuid instanceof Uuid) {
            $uuid = Uuid::fromString($uuid);
        }

        return $uuid->getBytes();
    }

    /**
     * Decode a `binary UUID` to `string UUID` if possible.
     *
     * @param string $binaryUuid
     *
     * @return string
     */
    public static function decodeUuid(string $binaryUuid): string
    {
        if (Uuid::isValid($binaryUuid)) {
            return $binaryUuid;
        }

        return Uuid::fromBytes($binaryUuid)->toString();
    }


}