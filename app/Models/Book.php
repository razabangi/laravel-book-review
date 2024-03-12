<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Book
 *
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author'
    ];

    function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeWithReviewCounts(Builder $query, $from = null, $to = null): Builder|QueryBuilder {
        return $query->withCount([
            'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ]);
    }

    public function scopeWithHighestRating(Builder $query, $from = null, $to = null): Builder|QueryBuilder {
        return $query->withAvg([
            'reviews' => fn (Builder $q) => $this->dateRangeFilter($q, $from, $to)
        ], 'rating');
    }

    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withReviewCounts()->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withHighestRating()->orderBy('reviews_avg_rating', 'desc');
    }

    private function dateRangeFilter(Builder $q, $from = null, $to = null)
    {
        if ($from && !$to) {
            $q->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $q->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }
    }

    public function scopeMinReview(Builder $query, int $minReview): Builder|QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReview);
    }

    public static function search($title)
    {
        return $title ? static::where('title', 'like', "%$title%") : static::query();
    }

    public function scopePopularLastMonth(Builder $query): Builder|QueryBuilder {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReview(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder|QueryBuilder {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReview(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReview(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder|QueryBuilder {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReview(5);
    }

    protected static function booted()
    {
        static::updated(
            fn(Book $book) => cache()->forget("book:$book->id")
        );

        static::deleted(
            fn(Book $book) => cache()->forget("book:$book->id")
        );
    }
}
