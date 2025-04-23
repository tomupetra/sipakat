@extends('layouts.admin')
@section('title', 'Jadwal Pelayanan Bulan Selanjutnya')

@section('content')
    <div class="container-fluid px-4">
        @php
            \Carbon\Carbon::setLocale('id');
            $nextMonthName = \Carbon\Carbon::now()->addMonth()->translatedFormat('F');
        @endphp
        <h1 class="mt-4">Jadwal Pelayanan Bulan {{ $nextMonthName }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Jadwal Pelayanan Bulan {{ $nextMonthName }}</li>
        </ol>
        <div class="container">
            @if ($jadwals->isEmpty())
                <div class="alert alert-warning">
                    Tidak ada jadwal pelayanan untuk bulan {{ $nextMonthName }}.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Sesi</th>
                                <th>Pemusik</th>
                                <th>Song Leader 1</th>
                                <th>Song Leader 2</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentWeek = null;
                                $weekCounter = 1;
                            @endphp
                            @foreach ($jadwals as $jadwal)
                                @php
                                    $weekOfMonth = \Carbon\Carbon::parse($jadwal->date)->weekOfMonth;
                                @endphp

                                @if ($currentWeek !== $weekOfMonth)
                                    <tr>
                                        <td colspan="7" class="table-secondary text-center">
                                            <strong>Minggu ke-{{ $weekCounter }}</strong>
                                        </td>
                                    </tr>
                                    @php
                                        $currentWeek = $weekOfMonth;
                                        $weekCounter++;
                                    @endphp
                                @endif

                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($jadwal->date)->format('d-m-Y') }}</td>
                                    <td>{{ $jadwal->jadwal }}</td>
                                    <td>
                                        {{ $jadwal->pemusik->name ?? '-' }}
                                        @if ($jadwal->status_pemusik == 1)
                                            <i class="fas fa-check-circle text-success ms-2" title="Sudah Konfirmasi"></i>
                                        @elseif ($jadwal->status_pemusik == 2)
                                            <i class="fas fa-times-circle text-danger ms-2" title="Ditolak"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Belum Konfirmasi"></i>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $jadwal->songLeader1->name ?? '-' }}
                                        @if ($jadwal->status_sl1 == 1)
                                            <i class="fas fa-check-circle text-success ms-2" title="Sudah Konfirmasi"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Belum Konfirmasi"></i>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $jadwal->songLeader2->name ?? '-' }}
                                        @if ($jadwal->status_sl2 == 1)
                                            <i class="fas fa-check-circle text-success ms-2" title="Sudah Konfirmasi"></i>
                                        @elseif ($jadwal->status_sl2 == 2)
                                            <i class="fas fa-times-circle text-danger ms-2" title="Ditolak"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Belum Konfirmasi"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($jadwal->is_confirmed == 0)
                                            <span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>
                                        @elseif($jadwal->is_confirmed == 1)
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif ($jadwal->is_confirmed == 2)
                                            <span class="badge bg-danger">Perlu Perbaikan Jadwal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.edit-jadwal', $jadwal->id) }}"
                                            class="btn btn-primary btn-sm">Ganti</a>
                                        <form action="{{ route('admin.delete-jadwal', $jadwal->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
