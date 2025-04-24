@extends('layouts.landing')
@section('title', $renungan->title)

@section('content')
    <section id="renungan-detail" class="renungan-detail">
        <div class="container" data-aos="fade-up">
            <div class="section-title">
                <h2>{{ $renungan->title }}</h2>
            </div>
            <div class="renungan-content">
                <img src="{{ asset('images/' . $renungan->image) }}" class="img-fluid mb-4" alt="">
                <p><strong>Tanggal Renungan:</strong> {{ $renungan->date }}</p>
                <p><strong>Lagu/Ende:</strong> {{ $renungan->lagu_ende }}</p>
                <p><strong>Ayat Harian:</strong> {{ $renungan->ayat_harian }}</p>
                <p><strong>Bacaan Pagi:</strong> {{ $renungan->bacaan_pagi }}</p>
                <p><strong>Bacaan Malam:</strong> {{ $renungan->bacaan_malam }}</p>
                <div class="content-text">
                    <p><strong>Isi Renungan:</strong></p>
                    {!! $renungan->content !!}
                </div>
            </div>
        </div>
    </section>

    <style>
        .section-title h2 {
            font-size: 2rem;
            /* margin-bottom: 10px; */
            color: #333;
            text-align: center;
        }

        .renungan-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
        }

        .renungan-content img {
            max-width: 50%;
            height: auto;
            border-radius: 8px;
            display: block;
            margin: 0 auto;
        }

        .content-text {
            font-size: 1rem;
            line-height: 1.5;
            color: #555;
        }

        .text-muted {
            color: #888;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
@endsection
