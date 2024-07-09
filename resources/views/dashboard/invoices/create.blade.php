@extends('layouts.app')
@section('title', 'Tambah Data Invoice')

@section('title-header', 'Tambah Data Invoice')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Data Invoice</a></li>
    <li class="breadcrumb-item active">Tambah Data Invoice</li>
@endsection

@section('c_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container--default .select2-selection--single {
            height: 50px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-transparent border-0 text-dark">
                    <h5 class="mb-0">Formulir Tambah Data Invoice</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.store') }}" method="POST" role="form"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="date">Tanggal</label>
                                    <input type="text" class="form-control @error('date') is-invalid @enderror"
                                        id="date" placeholder="Tanggal Invoice"
                                        value="{{ old('date', \Carbon\Carbon::parse(now())->translatedFormat('d F Y')) }}"
                                        disabled name="date">

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
                                        id="invoice_number" placeholder="Nomor Resi Invoice"
                                        value="{{ old('invoice_number') }}" name="invoice_number">

                                    @error('invoice_number')
                                        <div class="d-block invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-3">
                                    <label for="marketplace">Marketplace</label>
                                    <select class="js-example-basic-single @error('marketplace') is-invalid @enderror"
                                        id="marketplace" name="marketplace">
                                        <option value="">Pilih Marketplace</option>
                                        @foreach (['tiktok', 'shopee', 'tokped', 'lazada', 'offline'] as $marketplace)
                                            <option value="{{ $marketplace }}"
                                                {{ old('marketplace') == $marketplace ? 'selected' : '' }}>
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
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name">Daftar Isi Kotak P3K</label>
                                    <div class="table-responsive">
                                        <table class="table table-borderless" id="dynamic_field">
                                            <thead>
                                                <tr style="background: #DDF5FF;">
                                                    <th>Nama / Motif Baju</th>
                                                    <th>Ukuran</th>
                                                    <th>Jumlah</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="item-row">
                                                    <td>
                                                        <select class="select-product" name="product_id[]" required>
                                                            <option value="">Pilih Baju</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="size[]" class="js-example-basic-single" required>
                                                            <option value="">Pilih Ukuran</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="qty[]" class="js-example-basic-single" required>
                                                            <option value="">Pilih Jumlah</option>
                                                            @for ($i = 1; $i <= 50; $i++)
                                                                <option value="{{ $i }}">{{ $i }}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-sm item-remove">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-left">
                                                        <button type="button" class="btn btn-outline-primary btn-md"
                                                            id="add">
                                                            Tambah produk
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-sm btn-primary">Tambah</button>
                                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
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
        const urlRoot = "https://dowear.dimas.co.id";
        // const urlRoot = "{{ url('/') }}";
    </script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                width: '100%'
            });

            // Initialize Select2 for existing elements
            initializeSelect2($('.select-product'));

            // Add new row
            $('#add').click(function() {
                var newRow = `<tr class="item-row">
                    <td>
                        <select class="select-product" name="product_id[]" required>
                            <option value="">Pilih Baju</option>
                        </select>
                    </td>
                    <td>
                        <select name="size[]" class="js-example-basic-single" required>
                            <option value="">Pilih Ukuran</option>
                        </select>
                    </td>
                    <td>
                        <select name="qty[]" class="js-example-basic-single" required>
                            <option value="">Pilih Jumlah</option>
                            @for ($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm item-remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
                $('#dynamic_field tbody').append(newRow);
                // Initialize Select2 for new elements
                initializeSelect2($('.select-product').last());
                $('.js-example-basic-single').select2({
                    width: '100%'
                });

                $('.select-product').last().on('select2:select', function(e) {
                    var data = e.params.data;
                    var tr = $(this).closest('tr');
                    var sizeDropdown = tr.find('select[name="size[]"]');

                    $.ajax({
                        url: `${urlRoot}/api/products/${data.id}`,
                        type: 'GET',
                        success: function(response) {
                            sizeDropdown.empty();
                            sizeDropdown.append(
                                `<option value="">Pilih Ukuran</option>`);
                            response.forEach(function(size) {
                                sizeDropdown.append(
                                    `<option value="${size.size}">${size.size}</option>`
                                );
                            });
                        }
                    });
                });
            });

            // Remove row
            $(document).on('click', '.item-remove', function() {
                $(this).closest('tr').remove();
            });

            // Initialize Select2 function
            function initializeSelect2(element) {
                element.select2({
                    ajax: {
                        url: '/api/products',
                        dataType: 'json',
                        delay: 250, // tambahkan delay untuk mengurangi jumlah permintaan
                        data: function(params) {
                            return {
                                q: params.term // parameter pencarian
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(product) {
                                    return {
                                        id: product.id,
                                        text: product.name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    width: '100%'
                });
            }

            $('.select-product').on('select2:select', function(e) {
                var data = e.params.data;
                var tr = $(this).closest('tr');
                var sizeDropdown = tr.find('select[name="size[]"]');

                $.ajax({
                    url: `${urlRoot}/api/products/${data.id}`,
                    type: 'GET',
                    success: function(response) {
                        sizeDropdown.empty();
                        sizeDropdown.append(`<option value="">Pilih Ukuran</option>`);
                        response.forEach(function(size) {
                            sizeDropdown.append(
                                `<option value="${size.size}">${size.size}</option>`
                            );
                        });
                    }
                });
            });
        });
    </script>
@endsection
