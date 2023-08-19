<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Concerns;

use LaravelInteraction\Bookmark\Bookmark;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

/**
 * @internal
 */
final class BookmarkerTest extends TestCase
{
    public function testBookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'bookmarkable_type' => $channel->getMorphClass(),
                'bookmarkable_id' => $channel->getKey(),
            ]
        );
        $user->load('bookmarkerBookmarks');
        $user->unbookmark($channel);
        $user->load('bookmarkerBookmarks');
        $user->bookmark($channel);
    }

    public function testUnbookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'bookmarkable_type' => $channel->getMorphClass(),
                'bookmarkable_id' => $channel->getKey(),
            ]
        );
        $user->unbookmark($channel);
        $this->assertDatabaseMissing(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'bookmarkable_type' => $channel->getMorphClass(),
                'bookmarkable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleBookmark(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        $this->assertDatabaseHas(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'bookmarkable_type' => $channel->getMorphClass(),
                'bookmarkable_id' => $channel->getKey(),
            ]
        );
        $user->toggleBookmark($channel);
        $this->assertDatabaseMissing(
            Bookmark::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'bookmarkable_type' => $channel->getMorphClass(),
                'bookmarkable_id' => $channel->getKey(),
            ]
        );
    }

    public function testBookmarkerBookmarks(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        $this->assertSame(1, $user->bookmarkerBookmarks()->count());
        $this->assertSame(1, $user->bookmarkerBookmarks->count());
    }

    public function testHasBookmarked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        $this->assertTrue($user->hasBookmarked($channel));
        $user->toggleBookmark($channel);
        $user->load('bookmarkerBookmarks');
        $this->assertFalse($user->hasBookmarked($channel));
    }

    public function testHasNotBookmarked(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleBookmark($channel);
        $this->assertFalse($user->hasNotBookmarked($channel));
        $user->toggleBookmark($channel);
        $this->assertTrue($user->hasNotBookmarked($channel));
    }
}
