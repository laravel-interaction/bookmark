<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelBookmark\Events\Unbookmarked;
use Zing\LaravelBookmark\Tests\Models\Channel;
use Zing\LaravelBookmark\Tests\Models\User;
use Zing\LaravelBookmark\Tests\TestCase;

class UnbookmarkedTest extends TestCase
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
