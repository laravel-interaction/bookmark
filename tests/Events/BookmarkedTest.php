<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelBookmark\Events\Bookmarked;
use Zing\LaravelBookmark\Tests\Models\Channel;
use Zing\LaravelBookmark\Tests\Models\User;
use Zing\LaravelBookmark\Tests\TestCase;

class BookmarkedTest extends TestCase
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
