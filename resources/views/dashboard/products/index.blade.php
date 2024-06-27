@extends('layouts.app')
@section('title', 'Data Produk')

@section('title-header', 'Data Produk')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Produk</li>
@endsection

@section('action_btn')
    <a href="{{ route('products.create') }}" class="btn btn-default">Tambah Data</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-transparent border-0 text-dark">
                    <h2 class="card-title h3">Data Produk</h2>
                    <div class="table-responsive mb-3">
                        {{ $dataTable->table(['class' => 'table table-hover']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{ $dataTable->scripts() }}
@endsection
