<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Configuration;

use LaravelInteraction\Bookmark\Bookmark;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'bookmark.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $bookmark = new Bookmark();
        $this->assertSame('string', $bookmark->getKeyType());
    }

    public function testIncrementing(): void
    {
        $bookmark = new Bookmark();
        $this->assertFalse($bookmark->getIncrementing());
    }

    public function testKeyName(): void
    {
        $bookmark = new Bookmark();
        $this->assertSame('uuid', $bookmark->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        $this->assertIsString($user->bookmarkerBookmarks()->firstOrFail()->getKey());
    }
}
