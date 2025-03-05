@extends('layouts.admin')
@section('title', 'Edit Akun')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Kelola Akun</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ url('admin/kelolaakun') }}">Kelola Akun</a></li>
            <li class="breadcrumb-item active">Edit Akun</li>
        </ol>
        <div class="row mt-4">
            <!-- Form Tambah Akun -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            @csrf
                            <!-- Input Nama -->
                            <div class="form-floating mb-3">
                                <input required class="form-control" id="name" name="name" type="text"
                                    placeholder="Masukkan nama Anda" />
                                <label for="name">Nama</label>
                            </div>

                            <!-- Input Email -->
                            <div class="form-floating mb-3">
                                <input required class="form-control" id="email" name="email" type="email"
                                    placeholder="email@example.com" />
                                <label for="email">Email</label>
                            </div>

                            <!-- Input Password -->
                            <div class="form-floating mb-3">
                                <input required class="form-control" id="password" name="password" type="password"
                                    placeholder="Masukkan password" />
                                <label for="password">Password</label>
                            </div>

                            <!-- Input Jenis Pelayanan -->
                            <div class="form-floating mb-3">
                                <select required class="form-control" id="id_tugas" name="id_tugas">
                                    <option value="" disabled selected>Pilih Jenis Pelayanan</option>
                                    <option value="1">Pemusik</option>
                                    <option value="2">Song Leader</option>
                                </select>
                                <label for="id_tugas">Jenis Pelayanan</label>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-4 mb-0">
                                <div class="d-flex justify-content-start">
                                    <button class="btn btn-primary w-auto" type="submit">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
