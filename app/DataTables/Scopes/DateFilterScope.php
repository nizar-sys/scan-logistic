<?php

namespace App\DataTables\Scopes;

use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTableScope;

class DateFilterScope implements DataTableScope
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request  = $request;
    }

    /**
     * Apply a query scope.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function apply($query)
    {
        $request = $this->request;

        return $query->when($request->start_date && !$request->end_date, function ($query) use ($request) {
            $startDateTime = $request->start_date . ' 00:00:00';
            $query->where('created_at', '>=', $startDateTime);
        })
            ->when($request->end_date && !$request->start_date, function ($query) use ($request) {
                $endDateTime = $request->end_date . ' 23:59:59';
                $query->where('created_at', '<=', $endDateTime);
            })
            ->when($request->start_date && $request->end_date, function ($query) use ($request) {
                $startDateTime = $request->start_date . ' 00:00:00';
                $endDateTime = $request->end_date . ' 23:59:59';
                $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
            });
    }
}
