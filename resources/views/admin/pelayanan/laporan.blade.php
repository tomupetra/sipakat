@extends('layouts.admin')
@section('title', 'Riwayat Pelayanan')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Riwayat Jadwal Pelayanan</h1>

        <form method="GET" action="{{ route('laporan.pelayanan') }}" class="row mb-4 align-items-end">
            <div class="col-md-3">
                <label for="tanggal" class="form-label">Tampilkan sebelum tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label">Filter Bulan:</label>
                <input type="month" id="bulan" name="bulan" class="form-control" value="{{ request('bulan') }}">
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label">Cari Nama atau Sesi:</label>
                <input type="text" id="search" name="search" class="form-control" value="{{ request('search') }}">
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary me-1">Terapkan</button>
                <a href="{{ route('laporan.pelayanan') }}" class="btn btn-secondary me-1">Clear</a>
                <a href="{{ route('laporan.exportPDF', request()->query()) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Sesi</th>
                        <th>Pemusik</th>
                        <th>Song Leader 1</th>
                        <th>Song Leader 2</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporan as $jadwal)
                        <tr>
                            <td>{{ $jadwal->date }}</td>
                            <td>{{ $jadwal->jadwal }}</td>
                            <td>{{ $jadwal->pemusik->name ?? '-' }}</td>
                            <td>{{ $jadwal->songLeader1->name ?? '-' }}</td>
                            <td>{{ $jadwal->songLeader2->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $laporan->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
