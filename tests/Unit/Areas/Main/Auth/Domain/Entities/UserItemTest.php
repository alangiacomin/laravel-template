<?php

namespace Tests\Unit\Areas\Main\Auth\Domain\Entities;

use App\Areas\Main\Auth\Domain\Entities\UserItem;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Tests\TestCase;

class UserItemTest extends TestCase
{
    public function test_it_creates_a_valid_user_item(): void
    {
        $createdAt = Carbon::now();

        $user = new UserItem(
            name: 'Mario Rossi',
            email: 'mario.rossi@example.com',
            password: 'secret-password',
            id: 1,
            isVerified: true,
            isBanned: false,
            avatar: 'avatar.png',
            createdAt: $createdAt,
        );

        $this->assertSame(1, $user->id);
        $this->assertSame('Mario Rossi', $user->name);
        $this->assertSame('mario.rossi@example.com', $user->email);
        $this->assertSame('secret-password', $user->password);
        $this->assertTrue($user->isVerified);
        $this->assertFalse($user->isBanned);
        $this->assertSame('avatar.png', $user->avatar);
        $this->assertSame($createdAt, $user->createdAt);
    }

    public function test_it_applies_default_values(): void
    {
        $user = new UserItem(
            name: 'Mario Rossi',
            email: 'mario.rossi@example.com',
            password: 'secret-password',
        );

        $this->assertSame(0, $user->id);
        $this->assertFalse($user->isVerified);
        $this->assertFalse($user->isBanned);
        $this->assertNull($user->avatar);
        $this->assertNull($user->createdAt);
    }

    public function test_it_trims_the_name(): void
    {
        $user = new UserItem(
            name: '  Mario Rossi  ',
            email: 'mario.rossi@example.com',
            password: 'secret-password',
        );

        $this->assertSame('Mario Rossi', $user->name);
    }

    public function test_it_throws_an_exception_when_name_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserItem(
            name: '',
            email: 'mario.rossi@example.com',
            password: 'secret-password',
        );
    }

    public function test_it_throws_an_exception_when_name_is_only_whitespace(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new UserItem(
            name: '   ',
            email: 'mario.rossi@example.com',
            password: 'secret-password',
        );
    }
}
