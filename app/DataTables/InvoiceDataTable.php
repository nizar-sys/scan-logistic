<?php

namespace App\DataTables;

use App\Models\Invoice;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class InvoiceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', 'dashboard.invoices.action')
            ->rawColumns(['action', 'status_print'])
            ->addIndexColumn()
            ->editColumn('created_at', function ($invoice) {
                return $invoice->created_at->translatedFormat('d F Y | H:i');
            })
            ->editColumn('marketplace', fn ($invoice) => ucfirst($invoice->marketplace))
            ->editColumn('status_print', fn ($invoice) => $invoice->status_print ? '<span class="badge badge-success">Sudah Print</span>' : '<span class="badge badge-danger">Belum Print</span>')
            ->editColumn('details.product.name', fn ($invoice) => $invoice->details->map(fn ($detail) => $detail->product->name)->implode(', '));
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Invoice $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Invoice $model)
    {
        return $model->newQuery()
            ->with('details', 'details.product')
            ->select('id', 'invoice_number', 'marketplace', 'created_at', 'status_print');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('invoices-table')
            ->columns($this->getColumns())
            ->orderBy(1)
            ->parameters([
                'dom' => '<"row"<"col-md-6"B><"col-md-6"f>>' .
                    '<"row"<"col-md-6"l><"col-md-6"p>>' .
                    'rt' .
                    '<"row"<"col-md-5"i><"col-md-7"p>>',
                'buttons' => [
                    [
                        'extend' => 'excelHtml5',
                        'text' => '<i class="fas fa-file-excel"></i>',
                        'className' => 'btn btn-sm ml-1 btn-success',
                        'filename' => $this->filename(),
                        'exportOptions' => [
                            'columns' => [0, 1, 2, 3, 4],
                        ],
                        'sheetName' => $this->filename(),
                    ],

                    [
                        'extend' => 'pdf',
                        'text' => '<i class="fas fa-file-pdf"></i>',
                        'className' => 'btn btn-sm ml-1 btn-danger',
                        'exportOptions' => [
                            'columns' => [0, 1, 2, 3, 4],
                        ],
                        'filename' => $this->filename(),
                    ],

                    [
                        'text' => '<i class="fas fa-sync"></i>',
                        'className' => 'btn btn-sm ml-1 btn-info',
                        'action' => 'function(){
                            $("input, select, textarea").val("");
                            var table = window.LaravelDataTables["invoices-table"];
                            table.ajax.reload();
                            table.search("").columns().search("").draw();
                        }',
                    ],

                ],
                'language' => [
                    'paginate' => [
                        'previous' => '&laquo;',
                        'next'     => '&raquo;',
                    ],
                ]
            ])
            ->ajax([
                'url'  => route('invoices.index'),
                'type' => 'GET',
                'data' => "function(data){
                    _token                  = '{{ csrf_token() }}',
                    data.start_date      = $('input[name=start_date]').val();
                    data.end_date        = $('input[name=end_date]').val();
                }",
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')->title('No')
                ->searchable(false)
                ->orderable(false)
                ->width(20),
            Column::make('created_at')
                ->title('Tanggal')
                ->searchable(false),
            Column::make('invoice_number')
                ->title('Nomor Resi'),
            Column::make('marketplace')
                ->title('Marketplace'),
            Column::make('details.product.name')
                ->title('Nama/Motif Baju')
                ->searchable(false)
                ->orderable(false),
            Column::make('status_print')
                ->title('Status Print')
                ->searchable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center')
                ->title('Action'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Invoice_' . date('YmdHis');
    }
}
