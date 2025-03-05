@extends('layouts.admin')
@section('title', 'Gallery')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Gallery</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Gallery</li>
        </ol>
        <div class="row mb-4">
            <div class="col-md-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload Foto</button>
            </div>
        </div>
        <div class="row">
            @foreach ($images as $value)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="{{ asset('storage/' . $value->image) }}" alt="Card image cap">
                        <div class="card-body">
                            <button class="btn btn-danger btn-sm" onclick="showDeleteModal({{ $value->id }})">Hapus</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Upload Photo Modal -->
        <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.galeri.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="image" class="form-label">Pilih Foto</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus foto ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form id="deleteForm" action="" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showDeleteModal(id) {
                var deleteForm = document.getElementById('deleteForm');
                deleteForm.action = '/admin/galeri/delete/' + id;
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
@endsection