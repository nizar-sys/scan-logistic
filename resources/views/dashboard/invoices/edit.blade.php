@extends('layouts.app')
@section('title', 'Ubah Data Invoice')

@section('title-header', 'Ubah Data Invoice')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Data Invoice</a></li>
    <li class="breadcrumb-item active">Ubah Data Invoice</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-transparent border-0 text-dark">
                    <h5 class="mb-0">Formulir Ubah Data Invoice</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" role="form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="date">Tanggal</label>
                                    <input type="text" class="form-control @error('date') is-invalid @enderror" id="date"
                                        placeholder="Tanggal Invoice" value="{{ old('date', \Carbon\Carbon::parse(now())->translatedFormat('d F Y')) }}" disabled name="date">

                                    @error('date')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="invoice_number">Nomor Resi</label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                        id="invoice_number" placeholder="Nomor Resi Invoice" value="{{ old('invoice_number', $invoice->invoice_number) }}"
                                        name="invoice_number">

                                    @error('invoice_number')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="marketplace">Marketplace</label>
                                    <select class="form-control @error('marketplace') is-invalid @enderror" id="marketplace"
                                        name="marketplace">
                                        <option value="">Pilih Marketplace</option>
                                        @foreach (['tiktok', 'shopee', 'tokped', 'lazada', 'offline'] as $marketplace)
                                            <option value="{{ $marketplace }}"
                                                {{ old('marketplace', $invoice->marketplace) == $marketplace ? 'selected' : '' }}>
                                                {{ ucfirst($marketplace) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('marketplace')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-sm btn-primary">Ubah</button>
                                <a href="{{route('invoices.index')}}" class="btn btn-sm btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
