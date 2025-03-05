@extends('layouts.admin')
@section('title', 'Edit Renungan')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Renungan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('/admin/renungan/list') }}">Renungan</a></li>
            <li class="breadcrumb-item active">Edit Renungan</li>
        </ol>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="date" class="form-label">Tanggal Renungan</label>
                            <input type="date" value="{{ $getRecord->date }}" class="form-control" id="date" name="date" required
                                onfocus="this.showPicker()">
                        </div>
                        <div class="mb-3">
                            <label for="ayat_harian" class="form-label">Ayat Harian</label>
                            <textarea class="form-control" id="ayat_harian" name="ayat_harian" required>{{ $getRecord->ayat_harian }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="bacaan_pagi" class="form-label">Bacaan Pagi</label>
                            <input type="text" value="{{ $getRecord->bacaan_pagi }}" class="form-control" id="bacaan_pagi" name="bacaan_pagi" required>
                        </div>
                        <div class="mb-3">
                            <label for="bacaan_malam" class="form-label">Bacaan Malam</label>
                            <input type="text" value="{{ $getRecord->bacaan_malam }}" class="form-control" id="bacaan_malam" name="bacaan_malam" required>
                        </div>
                        <div class="mb-3">
                            <label for="lagu_ende" class="form-label">Lagu/Ende</label>
                            <input type="text" value="{{ $getRecord->lagu_ende }}" class="form-control" id="lagu_ende" name="lagu_ende" required>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" value="{{ $getRecord->title }}" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="pell" id="editor">
                            <label for="content" class="form-label">Isi Renungan</label>
                            <textarea class="form-control" id="content" name="content" required>{{ $getRecord->content }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="validateImageSize(event)">
                            <small class="form-text text-muted">(Ukuran Maksimal 2MB)</small>
                        </div>
                        <div id="imageSizeAlert" class="alert alert-danger mt-2 d-none" role="alert">
                            Gambar yang diunggah melebihi ukuran maksimal 2MB.
                        </div>
                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function validateImageSize(event) {
            const file = event.target.files[0]; // Ambil file yang dipilih
            const maxSize = 2048 * 1024; // Maksimal ukuran dalam byte (2048KB)

            const alertBox = document.getElementById('imageSizeAlert');

            if (file && file.size > maxSize) {
                // Tampilkan notifikasi jika ukuran file lebih besar dari 2MB
                alertBox.classList.remove('d-none');
                event.target.value = ''; // Reset input file
            } else {
                // Sembunyikan notifikasi jika ukuran file valid
                alertBox.classList.add('d-none');
            }
        }
    </script>
@endsection
