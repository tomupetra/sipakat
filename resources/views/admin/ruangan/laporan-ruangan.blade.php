@extends('layouts.admin')
@section('title', 'Laporan Peminjaman Ruangan')

@section('content')
    <div class="container">
        <h1>Laporan Peminjaman Ruangan</h1>
        <form method="GET" action="{{ route('admin.laporan-ruangan') }}" class="row mb-4 align-items-end">
            <div class="col-md-3">
                <label for="tanggal" class="form-label">Tampilkan pada tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <label for="ruangan" class="form-label">Filter Ruangan:</label>
                <input type="text" id="ruangan" name="ruangan" class="form-control" value="{{ request('ruangan') }}">
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">Cari Nama/Keterangan/Status:</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary me-1">Terapkan</button>
                <a href="{{ route('admin.laporan-ruangan') }}" class="btn btn-secondary me-1">Clear</a>
                <a href="{{ route('admin.laporan-ruangan.export-pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    {{-- <th>Tanggal</th> --}}
                    <th>Nama Peminjam</th>
                    <th>Ruangan</th>
                    <th>Kegiatan</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporan as $item)
                    <tr>
                        {{-- <td>{{ $item->tanggal->format('d-m-Y') }}</td> --}}
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->ruangan->name ?? '-' }}</td>
                        <td>{{ $item->kegiatan }}</td>
                        <td>{{ $item->start_time }}</td>
                        <td>{{ $item->end_time }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $laporan->links() }}
    </div>
@endsection
