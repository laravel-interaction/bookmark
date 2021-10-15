<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Bookmark\Events\Bookmarked;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

/**
 * @internal
 */
final class BookmarkedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Bookmarked::class]);
        $user->bookmark($channel);
        Event::assertDispatchedTimes(Bookmarked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Bookmarked::class]);
        $user->bookmark($channel);
        $user->bookmark($channel);
        $user->bookmark($channel);
        Event::assertDispatchedTimes(Bookmarked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Bookmarked::class]);
        $user->toggleBookmark($channel);
        Event::assertDispatchedTimes(Bookmarked::class);
    }
}
