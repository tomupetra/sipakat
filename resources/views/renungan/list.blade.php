@extends('layouts.admin')
@section('title', 'Renungan')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">List Renungan</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/renungan/list') }}">Renungan</a></li>
            <li class="breadcrumb-item active">List Renungan</li>
        </ol>
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-12">
                    <a href="{{ url('admin/renungan/tambah') }}" class="btn btn-success">Tambah Renungan</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="datatablesSimple" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Ayat Harian</th>
                                <th>Judul</th>
                                <th>Isi Renungan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getRecord as $value)
                                <tr>
                                    <td>{{ $value->date }}</td>
                                    <td>{{ Str::limit($value->ayat_harian, 25) }}</td>
                                    <td>{{ Str::limit($value->title, 15) }}</td>
                                    <td>
                                        {{Str::limit($value->content, 30)}}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="showDetailModal({{ $value->id }})">Lihat Detail</button>
                                        <a href="{{ url('admin/renungan/edit/' . $value->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm" onclick="showDeleteModal({{ $value->id }})">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel">Detail Renungan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="detailContent">
                            <!-- Detail content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Renungan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus renungan ini?
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
            function showDetailModal(id) {
                fetch(`/admin/renungan/detail/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        const detailContent = document.getElementById('detailContent');
                        detailContent.innerHTML = `
                            <p><strong>Tanggal:</strong> ${data.date}</p>
                            <p><strong>Ayat Harian:</strong> ${data.ayat_harian}</p>
                            <p><strong>Bacaan Pagi:</strong> ${data.bacaan_pagi}</p>
                            <p><strong>Bacaan Malam:</strong> ${data.bacaan_malam}</p>
                            <p><strong>Lagu/Ende:</strong> ${data.lagu_ende}</p>
                            <p><strong>Judul:</strong> ${data.title}</p>
                            <p><strong>Isi Renungan:</strong> ${data.content}</p>
                            ${data.image ? `<p><strong>Gambar:</strong><br><img src="/images/${data.image}" class="img-fluid" alt="Gambar Renungan"></p>` : ''}
                        `;
                        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                        detailModal.show();
                    });
            }

            function showDeleteModal(id) {
                var deleteForm = document.getElementById('deleteForm');
                deleteForm.action = '/admin/renungan/delete/' + id;
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
@endsection