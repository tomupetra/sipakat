@extends('layouts.admin')
@section('title', 'List Warta')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">List Warta</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">List Warta</li>
        </ol>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <a href="{{ route('admin.warta.create') }}" class="btn btn-success">Tambah Warta</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="datatablesSimple" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>File</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getRecord as $value)
                                <tr>
                                    <td>{{ $value->date }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#pdfModal{{ $value->id }}">
                                            Lihat Warta
                                        </button>

                                        <!-- PDF Modal -->
                                        <div class="modal fade" id="pdfModal{{ $value->id }}" tabindex="-1" aria-labelledby="pdfModalLabel{{ $value->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="pdfModalLabel{{ $value->id }}">Warta Jemaat</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <iframe src="{{ asset('warta/' . $value->file_name) }}" width="100%" height="600px"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ asset('warta/' . $value->file_name) }}" class="btn btn-success btn-sm" download>Unduh</a>
                                        <button class="btn btn-danger btn-sm" onclick="showDeleteModal({{ $value->id }})">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Warta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus warta ini?
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
                deleteForm.action = '/admin/warta/destroy/' + id;
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
@endsection
