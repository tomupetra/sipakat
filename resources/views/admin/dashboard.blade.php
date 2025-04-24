@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="icon mb-3">
                        <i class="fas fa-music fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">Jumlah Pemusik</h5>
                    <h2>{{ $jumlahPemusik }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="icon mb-3">
                        <i class="fas fa-microphone fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">Jumlah Song Leader</h5>
                    <h2>{{ $jumlahSongLeader }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="icon mb-3">
                        <i class="fas fa-door-open fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title">Peminjaman Belum Validasi</h5>
                    <h2>{{ $jumlahPeminjamanBelumValidasi }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="icon mb-3">
                        <i class="fas fa-calendar-check fa-2x text-danger"></i>
                    </div>
                    <h5 class="card-title">Jadwal Belum Dikonfirmasi</h5>
                    <h2>{{ $jumlahJadwalBelumDikonfirmasi }}</h2>
                </div>
            </div>
        </div>
    </div>
@endsection
