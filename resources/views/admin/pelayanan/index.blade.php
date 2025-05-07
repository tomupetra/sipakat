    @extends('layouts.admin')
    @section('title', 'Jadwal Pelayanan')

    @section('content')
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelola Jadwal Pelayanan</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Kelola Jadwal Pelayanan</li>
            </ol>
            <div class="container">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <button id="generateScheduleButton" class="btn btn-success mb-3">Buat Jadwal</button>
                        <button id="nextMonthScheduleButton" class="btn btn-primary mb-3">Buat Jadwal Bulan
                            Selanjutnya</button>
                    </div>
                </div>

                @if ($jadwals->isEmpty())
                    <div class="alert alert-warning">
                        Tidak ada jadwal pelayanan untuk bulan ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    {{-- <th>Minggu</th> --}}
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
                                        {{-- <td>{{ $weekCounter - 1 }}</td> --}}
                                        <td>{{ \Carbon\Carbon::parse($jadwal->date)->format('d-m-Y') }}</td>
                                        <td>{{ $jadwal->jadwal }}</td>
                                        <td>
                                            {{ $jadwal->pemusik->name ?? '-' }}
                                            @if ($jadwal->status_pemusik == 1)
                                                <i class="fas fa-check-circle text-success ms-2"
                                                    title="Sudah Konfirmasi"></i>
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
                                                <i class="fas fa-check-circle text-success ms-2"
                                                    title="Sudah Konfirmasi"></i>
                                            @else
                                                <i class="fas fa-exclamation-circle text-warning ms-2"
                                                    title="Belum Konfirmasi"></i>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $jadwal->songLeader2->name ?? '-' }}
                                            @if ($jadwal->status_sl2 == 1)
                                                <i class="fas fa-check-circle text-success ms-2"
                                                    title="Sudah Konfirmasi"></i>
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
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal" data-id="{{ $jadwal->id }}">Hapus</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="scheduleExistsModal" tabindex="-1" aria-labelledby="scheduleExistsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="scheduleExistsModalLabel">Informasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Jadwal untuk bulan ini sudah dibuat.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus jadwal ini?
                    </div>
                    <div class="modal-footer">
                        <form id="deleteForm" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var deleteModal = document.getElementById('deleteModal');
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var form = document.getElementById('deleteForm');
                    form.action = '/admin/delete-jadwal/' + id;
                });

                fetch('{{ route('admin.check-schedule-current-month') }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            document.getElementById('generateScheduleButton').addEventListener('click', function() {
                                var myModal = new bootstrap.Modal(document.getElementById(
                                    'scheduleExistsModal'));
                                myModal.show();
                            });
                        }
                    });

                fetch('{{ route('admin.check-next-month-schedule') }}')
                    .then(response => response.json())
                    .then(data => {
                        const button = document.getElementById('nextMonthScheduleButton');
                        if (data.exists) {
                            button.textContent = 'Lihat Jadwal Bulan Selanjutnya';
                            button.addEventListener('click', function() {
                                window.location.href = '{{ route('admin.show-next-month-schedule') }}';
                            });
                        } else {
                            button.addEventListener('click', function() {
                                window.location.href = '{{ route('admin.generate-next-month-schedule') }}';
                            });
                        }
                    });
            });
        </script>
    @endsection
