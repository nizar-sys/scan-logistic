<?php

namespace App\DataTables;

use App\Models\Product;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
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
            ->addColumn('action', 'dashboard.products.action')
            ->editColumn(
                'image',
                fn ($product) =>
                $product->image
                    ? '<img src="' . htmlspecialchars(asset('/uploads/images/' . $product->image), ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') . '" class="img-thumbnail" width="100" />'
                    : null
            )
            ->rawColumns(['action', 'image'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Product $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Product $model)
    {
        return $model->newQuery()
            ->select('id','code', 'name', 'image');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('product-table')
            ->columns($this->getColumns())
            ->orderBy(1, 'desc')
            ->parameters([
                'dom' => '<"row"<"col-md-6"><"col-md-6"f>>' .
                    '<"row"<"col-md-6"l><"col-md-6"p>>' .
                    'rt' .
                    '<"row"<"col-md-5"i><"col-md-7"p>>',
                'language' => [
                    'paginate' => [
                        'previous' => '&laquo;',
                        'next'     => '&raquo;',
                    ],
                ]
            ])
            ->ajax([
                'url'  => route('products.index'),
                'type' => 'GET',
                'data' => "function(data){
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
            Column::make('DT_RowIndex')
                ->title('No')
                ->searchable(false)
                ->orderable(false),
            Column::make('code')
                ->title('Kode'),
            Column::make('name')
                ->title('Nama / Motif'),
            Column::make('image')
                ->title('Gambar')
                ->searchable(false)
                ->orderable(false),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Product_' . date('YmdHis');
    }
}
