<?php

namespace Tests\Unit\Areas\Main\Auth\Infrastructure\Mappers;

use App\Areas\Main\Auth\Infrastructure\Mappers\UserItemMapper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserItemMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_maps_a_user_model_to_a_domain_entity(): void
    {
        $model = User::factory()->create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'email_verified_at' => now(),
            'banned_at' => null,
            'avatar' => 'avatar.png',
        ]);

        $userItem = UserItemMapper::toDomain($model);

        $this->assertSame($model->id, $userItem->id);
        $this->assertSame('Mario Rossi', $userItem->name);
        $this->assertSame('mario.rossi@example.com', $userItem->email);
        $this->assertSame($model->password, $userItem->password);
        $this->assertTrue($userItem->isVerified);
        $this->assertFalse($userItem->isBanned);
        $this->assertSame('avatar.png', $userItem->avatar);
        $this->assertEquals($model->created_at, $userItem->createdAt);
    }

    public function test_it_maps_a_banned_and_unverified_user(): void
    {
        $model = User::factory()->unverified()->create([
            'banned_at' => now(),
        ]);

        $userItem = UserItemMapper::toDomain($model);

        $this->assertFalse($userItem->isVerified);
        $this->assertTrue($userItem->isBanned);
    }

    public function test_it_maps_a_domain_entity_to_a_persistence_model(): void
    {
        $sourceModel = User::factory()->create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
        ]);
        $userItem = UserItemMapper::toDomain($sourceModel);

        $model = UserItemMapper::toPersistence($userItem);

        $this->assertInstanceOf(User::class, $model);
        $this->assertSame($sourceModel->id, $model->id);
        $this->assertSame('Mario Rossi', $model->name);
        $this->assertSame('mario.rossi@example.com', $model->email);
    }
}
