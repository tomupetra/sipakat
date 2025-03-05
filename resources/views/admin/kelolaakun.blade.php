@extends('layouts.admin')
@section('title', 'Kelola Akun')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Kelola Akun</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/kelolaakun') }}">Kelola Akun</a> /</li>
        </ol>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <a href="{{ url('admin/add') }}" class="btn btn-success">Tambah Akun</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Tugas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getRecord as $value)
                                <tr>
                                    <th scope="row">{{ $value->name }}</th>
                                    <td>{{ $value->email }}</td>
                                    @php
                                        $tugasMapping = [
                                            1 => 'Pemusik',
                                            2 => 'Song Leader',
                                        ];
                                    @endphp
                                    <td>{{ $tugasMapping[$value->id_tugas] ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('admin/edit/' . $value->id) }}"
                                            class="btn btn-primary btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="showDeleteModal({{ $value->id }})">Hapus</button>
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
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus akun ini?
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
                deleteForm.action = '/admin/delete/' + id;
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
    @endsection
