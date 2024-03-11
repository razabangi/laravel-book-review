@extends('layouts.app')
@section('content')
    <h1 class="mb-10 text-2xl">Books</h1>

    <form method="GET" action="{{ route('books.index') }}" class="mb-4 flex items-center space-x-2">
        <input type="search" name="title" placeholder="Search By Title" class="input h-10" value="{{ request('title') }}">
        <input type="submit" class="btn h-10" value="Search">
        <input type="hidden" name="filter" value="{{ request('filter') }}">
        <a href="{{ route('books.index') }}" class="btn h-10">Clear</a>
    </form>
    <div class="filter-container mb-4 flex">
        @foreach ($filters as $key => $label)
            <a href="{{ route('books.index', [...request()->query, 'filter' => $key]) }}" @class([
                'filter-item-active' => $key === request('filter') || (request('filter') === null && $key === ''),
                'filter-item'
            ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <ul>
        @forelse ($books as $book)
            <li class="mb-4">
                <div class="book-item">
                    <div class="flex flex-wrap items-center justify-between">
                        <div class="w-full flex-grow sm:w-auto">
                            <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
                            <span class="book-author">By {{ $book->author }}</span>
                        </div>
                        <div>
                            <div class="book-rating">
                                {{ number_format($book->reviews_avg_rating, 1) }}
                            </div>
                            <div class="book-review-count">
                                out of {{ $book->reviews_count }} {{ str()->plural('review', $book->reviews_count) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="mb-4">
                <div class="empty-book-item">
                    <p class="empty-text">No books found</p>
                    <a href="{{ route('books.index') }}" class="reset-link">Reset criteria</a>
                </div>
            </li>
        @endforelse
    </ul>
@endsection
