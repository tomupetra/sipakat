@extends('layouts.user')
@section('title', 'Dashboard')

@section('content')
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4" style="height: 130px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-calendar-alt fa-2x me-2"></i>
                        Jadwal Pelayanan
                    </span>
                    <span class="fs-3">{{ $totalJadwalPelayanan ?? 0 }}</span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('user.jadwal-pelayanan') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4" style="height: 130px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-exclamation-circle fa-2x me-2"></i>
                        Belum Konfirmasi Pelayanan
                    </span>
                    <span class="fs-3">{{ $jadwalPelayananBelumKonfirmasi ?? 0 }}</span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('user.jadwal-pelayanan') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4" style="height: 130px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-check-circle fa-2x me-2"></i>
                        Jadwal Ruangan Dikonfirmasi
                    </span>
                    <span class="fs-3">{{ $jadwalRuanganDikonfirmasi ?? 0 }}</span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ url('user/jadwal-ruangan') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4" style="height: 130px;">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <span>
                        <i class="fas fa-times-circle fa-2x me-2"></i>
                        Jadwal Ruangan Ditolak
                    </span>
                    <span class="fs-3">{{ $jadwalRuanganBelumKonfirmasi ?? 0 }}</span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ url('user/jadwal-ruangan') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
@endsection
