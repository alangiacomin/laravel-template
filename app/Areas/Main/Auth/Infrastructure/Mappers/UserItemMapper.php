<?php

namespace App\Areas\Main\Auth\Infrastructure\Mappers;

use AlanGiacomin\LaravelCqrs\App\Infrastructure\Mappers\Mapper;
use App\Areas\Main\Auth\Domain\Entities\UserItem;
use App\Models\User;

/**
 * @extends Mapper<User, UserItem>
 */
class UserItemMapper extends Mapper
{
    public static function toDomain($model): UserItem
    {
        return new UserItem(
            name: $model->name,
            email: $model->email,
            password: $model->password,
            id: $model->id,
            isVerified: $model->hasVerifiedEmail(),
            isBanned: $model->isBanned(),
            avatar: $model->avatar,
            createdAt: $model->created_at,
        );
    }

    public static function toPersistence($domain): User
    {
        $model = new User([
            // 'id' => $domain->id,
            'name' => $domain->name,
            'email' => $domain->email,
            'password' => $domain->password,
        ]);
        $model->id = $domain->id;

        return $model;
    }
}
