@extends('layouts.landing')
@section('title', 'Renungan')

@section('content')
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>Renungan</h2>
        </div>
        <div class="row">
            @foreach ($renungan as $item)
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="renungan-item">
                        <img src="{{ asset('storage/' . $item->image) }}" class="img-fluid" alt="">
                        <div class="renungan-content">
                            <h3><a href="{{ url('renungan/detail/' . $item->id) }}">{{ $item->title }}</a></h3>
                            <p>{{ $item->date }}</p>
                            <p>{{ Str::limit(strip_tags($item->content), 100) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
