@extends('layouts.user')
@section('title', 'Jadwal Pelayanan')

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <div class="container mt-4">
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Jadwal Pelayanan Anda</h3>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sesi</th>
                        <th>Tanggal</th>
                        <th>Pemusik</th>
                        <th>Song Leader 1</th>
                        <th>Song Leader 2</th>
                        <th>Deadline Konfirmasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwals as $jadwal)
                        <tr>
                            <td>{{ $jadwal->jadwal }}</td>
                            <td>{{ $jadwal->date }}</td>
                            <td>{{ $jadwal->pemusik->name }}</td>
                            <td>{{ $jadwal->songLeader1->name }}</td>
                            <td>{{ $jadwal->songLeader2->name }}</td>
                            <td>{{ $jadwal->confirmation_deadline }}</td>
                            <td>
                                @if (!$jadwal->is_confirmed && $jadwal->status != 2)
                                    <form action="{{ route('user.confirm-schedule', $jadwal->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                                    </form>

                                    <form action="{{ route('user.reject-schedule', $jadwal->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Jadwal Pelayanan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('user.store.jadwal-pelayanan') }}" method="POST" id="jadwalForm" autocomplete="off">
                    @csrf
                    <div class="form-group">
                        <label for="availability_dates">Pilih Tanggal yang Tersedia:</label>
                        <input type="text" class="form-control" id="availability_dates" name="dates"
                            placeholder="Pilih satu atau lebih tanggal" readonly>
                        @if ($errors->has('dates'))
                            <div class="text-danger mt-1">{{ $errors->first('dates') }}</div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                </form>

                <script>
                    $(document).ready(function() {
                        $('#availability_dates').datepicker({
                            format: 'yyyy-mm-dd',
                            multidate: true,
                            todayHighlight: true,
                        }).on('changeDate', function(e) {
                            $(this).val($('#availability_dates').datepicker('getFormattedDate'));
                        });
                    });
                </script>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Tanggal Kosong Anda</h3>
            </div>
            <div class="card-body">
                @if (count($availabilities) == 0) {{-- Menggunakan count() --}}
                    <p>Anda belum memiliki tanggal kosong yang diinput.</p>
                @else
                    <ul class="list-group">
                        @foreach ($availabilities->sort() as $availability)
                            <li class="list-group-item">{{ \Carbon\Carbon::parse($availability)->format('d-m-Y') }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
@endsection
