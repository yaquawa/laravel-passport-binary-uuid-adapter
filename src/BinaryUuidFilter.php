<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

trait BinaryUuidFilter
{
    public function toArray(): array
    {
        $array = parent::toArray();
        $array['user_id'] = Helper::decodeUuid($array['user_id']);

        return $array;
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            if ($userId = $model->getAttribute('user_id')) {
                $model->setAttribute('user_id', Helper::encodeUuid($userId));
            }
        });
    }
}