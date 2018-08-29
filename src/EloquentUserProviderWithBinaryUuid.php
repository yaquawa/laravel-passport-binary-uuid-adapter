<?php

namespace Yaquawa\Laravel\PassportBinaryUuidAdapter;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class EloquentUserProviderWithBinaryUuid implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var HasherContract
     */
    protected $hasher;

    /**
     * User model classes.
     *
     * @var [string]
     */
    protected $modelClasses;

    /**
     * Create a new database user provider.
     *
     * @param  HasherContract $hasher
     * @param  string|[string] $model
     *
     * @return void
     */
    public function __construct(HasherContract $hasher, $modelClasses)
    {
        $this->modelClasses = $this->getNormalizedClasses((array)$modelClasses);
        $this->hasher       = $hasher;
    }

    /**
     * Get normalized class names.
     *
     * @param  [string] $classes
     *
     * @return [string]
     */
    protected function getNormalizedClasses(array $classes): array
    {
        return array_map(function ($class) {
            return $class = '\\' . ltrim($class, '\\');
        }, $classes);
    }

    /**
     * Create the query builders from model classes.
     *
     * @return [UserContract]
     */
    protected function createModels(): array
    {
        return array_map(function ($modelClass) {
            return new $modelClass;
        }, $this->modelClasses);
    }


    /**
     * Determine if the given id is a valid string UUID.
     *
     * @param $id
     *
     * @return bool
     */
    protected function isStringUuid($id): bool
    {
        return Uuid::isValid($id);
    }

    /**
     * Get a binary UUID from a given string UUID.
     *
     * @param $stringUuid
     *
     * @return string
     * @throws \Ramsey\Uuid\Exception\InvalidUuidStringException
     */
    protected function getBinaryUuidFromString($stringUuid): string
    {
        return Uuid::fromString($stringUuid)->getBytes();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     *
     * @return UserContract|null
     */
    public function retrieveById($identifier): ?UserContract
    {
        // If the $identifier is a string UUID,
        // convert it to a binary UUID.
        $identifier = Helper::encodeUuid($identifier);

        // try to retrive user by walk through the given model classes
        // if hit one then return immediately
        foreach ($this->createModels() as $model) {
            $result = $model->where($model->getAuthIdentifierName(), $identifier)->first();

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     *
     * @return UserContract|null
     */
    public function retrieveByCredentials(array $credentials): ?UserContract
    {
        if (empty($credentials) ||
            (\count($credentials) === 1 &&
             array_key_exists('password', $credentials))) {
            return null;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.

        foreach ($this->createModels() as $model) {

            $query = $model->newQuery();

            foreach ($credentials as $key => $value) {
                if (Str::contains($key, 'password')) {
                    continue;
                }

                // If the $key is `id` and if the `id` is a string,
                // convert it to binary string.
                if ($key === $model->getAuthIdentifierName() && $this->isStringUuid($value)) {
                    $value = $this->getBinaryUuidFromString($value);
                }

                if (\is_array($value) || $value instanceof Arrayable) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }

            $result = $query->first();

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }


    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     *
     * @return UserContract|null
     */
    public function retrieveByToken($identifier, $token): ?UserContract
    {
        $model = $this->retrieveById($identifier);

        if ( ! $model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  UserContract $user
     * @param  string $token
     *
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token): void
    {
        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }


    /**
     * Validate a user against the given credentials.
     *
     * @param  UserContract $user
     * @param  array $credentials
     *
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

    /**
     * Gets the hasher implementation.
     *
     * @return HasherContract
     */
    public function getHasher(): HasherContract
    {
        return $this->hasher;
    }

    /**
     * Sets the hasher implementation.
     *
     * @param  HasherContract $hasher
     *
     * @return $this
     */
    public function setHasher(HasherContract $hasher): self
    {
        $this->hasher = $hasher;

        return $this;
    }
}