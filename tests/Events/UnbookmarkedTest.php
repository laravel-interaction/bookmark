<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Bookmark\Events\Unbookmarked;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

/**
 * @internal
 */
final class UnbookmarkedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        Event::fake([Unbookmarked::class]);
        $user->unbookmark($channel);
        Event::assertDispatchedTimes(Unbookmarked::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->bookmark($channel);
        Event::fake([Unbookmarked::class]);
        $user->unbookmark($channel);
        $user->unbookmark($channel);
        Event::assertDispatchedTimes(Unbookmarked::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unbookmarked::class]);
        $user->toggleBookmark($channel);
        $user->toggleBookmark($channel);
        Event::assertDispatchedTimes(Unbookmarked::class);
    }
}
