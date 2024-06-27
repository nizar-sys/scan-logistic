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
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" role="form"
                        enctype="multipart/form-data">
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
                            <input type="file" class="form-control @error('image') is-invalid @enderror dropify" id="image"
                                placeholder="Gambar Produk" name="image">

                            @error('image')
                                <div class="d-block invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
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
    </script>
@endsection
