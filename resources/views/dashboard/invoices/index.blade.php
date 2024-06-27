@extends('layouts.app')
@section('title', 'Data Invoice')

@section('title-header', 'Data Invoice')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Data Invoice</li>
@endsection

@section('action_btn')
    @php
        $startDate = request()->start_date ?? null;
        $endDate = request()->end_date ?? null;

        $queryParams = [];

        if ($startDate) {
            $queryParams['start_date'] = $startDate;
        }

        if ($endDate) {
            $queryParams['end_date'] = $endDate;
        }

        $url = route('invoices.reports', $queryParams);

    @endphp
    <a href="{{ $url }}" target="__blank" class="btn btn-sm btn-danger" id="btn-report">Buat Laporan</a>
    <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-default">Tambah Data</a>
    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#filter-date-modal">Filter</button>
    @if ($startDate || $endDate)
        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-danger">Reset</a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-transparent border-0 text-dark">
                    <h2 class="card-title h3">Data Invoice</h2>
                    <div class="table-responsive mb-3">
                        {{ $dataTable->table(['class' => 'table table-hover table-striped'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter Range Date -->
    <div class="modal fade" id="filter-date-modal" tabindex="-1" role="dialog" aria-labelledby="filter-date-modal-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filter-date-modal-label">Filter Range Date</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ old('start_date', request()->start_date) }}">
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ old('start_date', request()->end_date) }}">
                    </div>
                    <button class="btn btn-primary" id="filter-btn">Filter</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            var table = $('#invoices-table').DataTable();

            function drawTable() {
                table.draw();
            }

            $('#filter-btn').on('click', function(e) {
                e.preventDefault();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var urlReport = "{{ route('invoices.reports') }}";

                if (startDate) {
                    urlReport += '?start_date=' + startDate;
                }

                if (endDate) {
                    urlReport += '&end_date=' + endDate;
                }

                $('#btn-report').attr('href', urlReport);
                drawTable();
                $('#filter-date-modal').modal('hide');
            });
        });
    </script>
@endsection
