<?php

namespace App\DataTables;

use App\Models\TrackingRecord;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class TrackingRecordDataTable extends DataTable
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
            ->addColumn('action', 'dashboard.tracking_records.action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->editColumn('created_at', function ($trackingRecord) {
                return $trackingRecord->created_at->translatedFormat('d F Y | H:i');
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\TrackingRecord $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(TrackingRecord $model)
    {
        return $model->newQuery()
            ->select('id', 'tracking_number', 'created_at');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('trackingrecord-table')
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
                            'columns' => [0, 1, 2],
                        ],
                        'sheetName' => $this->filename(),
                    ],

                    [
                        'extend' => 'pdf',
                        'text' => '<i class="fas fa-file-pdf"></i>',
                        'className' => 'btn btn-sm ml-1 btn-danger',
                        'exportOptions' => [
                            'columns' => [0, 1, 2],
                        ],
                        'filename' => $this->filename(),
                    ],

                    [
                        'text' => '<i class="fas fa-sync"></i>',
                        'className' => 'btn btn-sm ml-1 btn-info',
                        'action' => 'function(){
                            $("input, select, textarea").val("");
                            var table = window.LaravelDataTables["trackingrecord-table"];
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
                'url'  => route('tracking-records.index'),
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
            Column::make('tracking_number')
                ->title('Nomor Resi'),
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
        return 'DataPengiriman_' . date('YmdHis');
    }
}
