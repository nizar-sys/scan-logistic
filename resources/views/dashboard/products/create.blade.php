@extends('layouts.app')
@section('title', 'Tambah Data Produk')

@section('title-header', 'Tambah Data Produk')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Data Produk</a></li>
    <li class="breadcrumb-item active">Tambah Data Produk</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-transparent border-0 text-dark">
                    <h5 class="mb-0">Formulir Tambah Data Produk</h5>
                </div>
                <form action="{{ route('products.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    <div class="card-body">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="code">Kode</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="code" placeholder="Kode Produk" value="{{ old('code') }}" name="code">

                                    @error('code')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="name">Nama / Motif</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" placeholder="Nama / Motif Produk" value="{{ old('name') }}"
                                        name="name">

                                    @error('name')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="image">Gambar</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror dropify"
                                id="image" placeholder="Gambar Produk" name="image">

                            @error('image')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="">Detail Produk</label>
                            <div class="table-responsive">
                                <table id="productDetailTable" class="table table-hover table-borderless">
                                    <thead>
                                        <tr style="background: #DDF5FF;">
                                            <th>Ukuran</th>
                                            <th>Stok</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" placeholder="Ukuran Produk"
                                                    name="product_detail[size][]" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" placeholder="Stok Produk"
                                                    name="product_detail[quantity][]" required>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row"><i
                                                        class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="addRowBtn" class="btn btn-primary btn-sm mt-3"><i
                                    class="fas fa-plus"></i> Tambah Detail</button>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong happended.'
            }
        });

        $(document).ready(function() {
            $('#addRowBtn').on('click', function() {
                const newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control" placeholder="Ukuran Produk" name="product_detail[size][]" required>
                        </td>
                        <td>
                            <input type="number" class="form-control" placeholder="Stok Produk" name="product_detail[quantity][]" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#productDetailTable tbody').append(newRow);
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endsection
