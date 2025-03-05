@extends('layouts.landing')
@section('title', 'Berita')

@section('content')
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>Berita</h2>
        </div>
        <div class="row">
            @foreach ($berita as $value)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="{{ asset('storage/' . $value->image) }}" alt="Card image cap">
                        <div class="card-body">
                            <h2 class="card-title">{{ $value->title }}</h2>
                            <p class="card-text">{{ Str::limit($value->content, 200) }}</p>
                            <a href="{{ url('berita/' . $value->id) }}" class="btn btn-primary">Read More &rarr;</a>
                        </div>
                        <div class="card-footer text-muted">
                            Posted on {{ \Carbon\Carbon::parse($value->date)->format('F d, Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $berita->links() }}
        </div>
    </div>
@endsection
