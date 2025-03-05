@extends('layouts.admin')
@section('title', 'Upload Warta')

@section('content')
    <h1 class="mt-4">Warta Jemaat</h1>
    <form action="{{ route('admin.warta.upload-warta') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="date" class="form-label">Tanggal Renungan</label>
            <input type="date" class="form-control" id="date" name="date" required
                onfocus="this.showPicker()">
        </div>
        <div class="mb-3">
            <label for="warta" class="form-label">Upload Warta Jemaat</label>
            <input type="file" class="form-control @error('warta') is-invalid @enderror" id="warta" name="warta"
                accept="application/pdf" required onchange="validateFileSize(event)">
            <small class="form-text text-muted">(Ukuran Maksimal 1MB)</small>
            @error('warta')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success mt-3">Upload</button>
    </form>

    <div id="fileSizeAlert" class="alert alert-danger mt-2 d-none" role="alert">
        File yang diunggah melebihi ukuran maksimal 1MB.
    </div>

    <script>
        function validateFileSize(event) {
            const file = event.target.files[0];
            const maxSize = 1024 * 1024;

            const alertBox = document.getElementById('fileSizeAlert');

            if (file && file.size > maxSize) {
                alertBox.classList.remove('d-none');
                event.target.value = '';
            } else {
                alertBox.classList.add('d-none');
            }
        }
    </script>
@endsection
