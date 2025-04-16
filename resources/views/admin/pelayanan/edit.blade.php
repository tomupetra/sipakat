@extends('layouts.admin')

@section('title', 'Edit Jadwal Pelayanan')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Jadwal Pelayanan</h1>
        <form action="{{ route('admin.update-jadwal', $jadwal->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Menandakan ini adalah permintaan PUT untuk update -->

            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ $jadwal->date }}" readonly>
            </div>

            <div class="form-group">
                <label for="jadwal">Sesi</label>
                <input type="text" class="form-control" id="jadwal" name="jadwal" value="{{ $jadwal->jadwal }}"
                    readonly>
            </div>

            <div class="form-group">
                <label for="id_pemusik">Pemusik</label>
                <select class="form-control" id="id_pemusik" name="id_pemusik">
                    @foreach ($keyboardists as $user)
                        <option value="{{ $user->id }}" {{ $jadwal->id_pemusik == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_sl1">Song Leader 1</label>
                <select class="form-control" id="id_sl1" name="id_sl1">
                    @foreach ($songLeaders as $user)
                        <option value="{{ $user->id }}" {{ $jadwal->id_sl1 == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="id_sl2">Song Leader 2</label>
                <select class="form-control" id="id_sl2" name="id_sl2">
                    @foreach ($songLeaders as $user)
                        <option value="{{ $user->id }}" {{ $jadwal->id_sl2 == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Simpan</button>
        </form>
    </div>
@endsection
