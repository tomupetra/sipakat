@extends('layouts.landing')
@section('title', $berita->title)

@section('content')
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>{{ $berita->title }}</h2>
        </div>
        <div class="berita-detail">
            <img src="{{ asset('storage/' . $berita->image) }}" class="img-fluid mb-4" alt="">
            <p class="text-muted">Posted on {{ \Carbon\Carbon::parse($berita->date)->format('F d, Y') }}</p>
            <div class="content-text">{!! $berita->content !!}</div>
        </div>
    </div>

    <style>
        .berita-detail img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .content-text {
            font-size: 1rem;
            line-height: 1.5;
            color: #555;
        }
    </style>
@endsection
