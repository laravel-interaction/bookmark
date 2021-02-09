<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Concerns;

use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;
use Mockery;

class BookmarkableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[
            [Channel::class],
            [User::class],
        ];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testBookmarkableBookmarks(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkableBookmarks()->count());
        self::assertSame(1, $model->bookmarkableBookmarks->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testBookmarkersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $user->unbookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $model->loadCount('bookmarkers');
        self::assertSame(0, $model->bookmarkersCount());
    }

    public function data(): array
    {
        return [
            [0, '0', '0', '0'],
            [1, '1', '1', '1'],
            [12, '12', '12', '12'],
            [123, '123', '123', '123'],
            [12345, '12.3K', '12.35K', '12.34K'],
            [1234567, '1.2M', '1.23M', '1.23M'],
            [123456789, '123.5M', '123.46M', '123.46M'],
            [12345678901, '12.3B', '12.35B', '12.35B'],
            [1234567890123, '1.2T', '1.23T', '1.23T'],
            [1234567890123456, '1.2Qa', '1.23Qa', '1.23Qa'],
            [1234567890123456789, '1.2Qi', '1.23Qi', '1.23Qi'],
        ];
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testBookmarkersCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $model = Mockery::mock(Channel::class);
        $model->shouldReceive('bookmarkersCountForHumans')->passthru();
        $model->shouldReceive('bookmarkersCount')->andReturn($actual);
        self::assertSame($onePrecision, $model->bookmarkersCountForHumans());
        self::assertSame($twoPrecision, $model->bookmarkersCountForHumans(2));
        self::assertSame($halfDown, $model->bookmarkersCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testIsBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isBookmarkedBy($model));
        $user->bookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertFalse($model->isBookmarkedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotBookmarkedBy($model));
        $user->bookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertTrue($model->isNotBookmarkedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testBookmarkers(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkers()->count());
        $user->unbookmark($model);
        self::assertSame(0, $model->bookmarkers()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $modelClass::query()->whereBookmarkedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereBookmarkedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotBookmarkedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotBookmarkedBy($other)->count());
    }
}
