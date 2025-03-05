@extends('layouts.admin')
@section('title', 'Daftar Peminjaman Ruangan')

@section('content')
    <div class="container mt-4">
        <h1>Daftar Peminjaman Ruangan</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Kegiatan</th>
                    <th>Ruangan</th>
                    <th>Pemohon</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $booking->kegiatan }}</td>
                        <td>{{ $booking->ruangan->name }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->start_time }}</td>
                        <td>{{ $booking->end_time }}</td>
                        <td>
                            <span class="badge {{ $booking->status == 'approved' ? 'bg-success' : ($booking->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                {{ $booking->status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.bookings.updateStatus', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" name="status" value="Disetujui" class="btn btn-sm btn-success">Setujui</button>
                                <button type="submit" name="status" value="Ditolak" class="btn btn-sm btn-danger">Tolak</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection