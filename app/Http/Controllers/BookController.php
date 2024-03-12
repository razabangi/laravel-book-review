<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = $request->title;
        $filter = $request->filter ?? '';

        $books = Book::search($title);

        $books = match ($filter) {
            'popular_last_month' => $books->popularLastMonth(),
            'popular_last_6months' => $books->popularLast6Months(),
            'highest_rated_last_month' => $books->highestRatedLastMonth(),
            'highest_rated_last_6months' => $books->highestRatedLast6Months(),
            default => $books->latest()->withReviewCounts()->withHighestRating(),
        };

        $cacheKey = "book:$filter:$title";

        $books = cache()->remember($cacheKey, 3600, fn() => $books->get());

        $filters = config('books.filters');

        return view('books.index', [
            'books' => $books,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = "book:$id";

        $book = cache()->remember($cacheKey, 3600, function() use ($id) {
            return Book::with([
                'reviews' => fn($q) => $q->latest()
            ])->withReviewCounts()->withHighestRating()->find($id);
        });

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
