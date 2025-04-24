@extends('layouts.admin')
@section('title', 'Edit Berita')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Edit Berita</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/berita/list') }}">Berita</a></li>
            <li class="breadcrumb-item active">Edit Berita</li>
        </ol>

        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <form action="{{ url('admin/berita/edit/' . $getRecord->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date" name="date" required
                                value="{{ date('Y-m-d', strtotime($getRecord->created_at)) }}" onfocus="this.showPicker()">
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" class="form-control" id="title" name="title" required
                                value="{{ $getRecord->title }}">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Isi Berita</label>
                            <textarea class="form-control" id="content" name="content" required>{{ $getRecord->content }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar</label>
                            @if ($getRecord->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/berita/' . $getRecord->image) }}" alt="Current Image"
                                        style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                onchange="validateImageSize(event)">
                            <small class="form-text text-muted">(Ukuran Maksimal 2MB)</small>
                        </div>
                        <div id="imageSizeAlert" class="alert alert-danger mt-2 d-none" role="alert">
                            Gambar yang diunggah melebihi ukuran maksimal 2MB.
                        </div>
                        <button type="submit" class="btn btn-primary">Update Berita</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateImageSize(event) {
            const file = event.target.files[0];
            if (file.size > 2 * 1024 * 1024) {
                document.getElementById('imageSizeAlert').classList.remove('d-none');
                event.target.value = '';
            } else {
                document.getElementById('imageSizeAlert').classList.add('d-none');
            }
        }
    </script>
@endsection
