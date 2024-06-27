<?php

namespace App\Http\Controllers;

use App\DataTables\Scopes\DateFilterScope;
use App\DataTables\TrackingRecordDataTable;
use App\Models\TrackingRecord;
use App\Http\Requests\RequestTrackingRecord;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class TrackingRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TrackingRecordDataTable $dataTable, Request $request)
    {
        return $dataTable
            ->addScopes([
                new DateFilterScope($request),
            ])
            ->render('dashboard.tracking_records.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.tracking_records.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestTrackingRecord $request)
    {
        $validated = $request->validated();

        TrackingRecord::create($validated);

        return redirect(route('tracking-records.index'))->with('success', 'Nomor resi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return TrackingRecord::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trackingRecord = TrackingRecord::findOrFail($id);

        return view('dashboard.tracking_records.edit', compact('trackingRecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RequestTrackingRecord $request, $id)
    {
        $validated = $request->validated() + [
            'updated_at' => now(),
        ];

        $trackingRecord = TrackingRecord::findOrFail($id);

        $trackingRecord->update($validated);

        return redirect(route('tracking-records.index'))->with('success', 'Nomor resi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trackingRecord = TrackingRecord::findOrFail($id);

        $trackingRecord->delete();

        return redirect(route('tracking-records.index'))->with('success', 'Nomor resi berhasil dihapus.');
    }

    public function reports(Request $request)
    {
        Carbon::setLocale('id');
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $zip = new ZipArchive();
        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipFileName = 'tracking-records-' . $timestamp . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            TrackingRecord::orderByDesc('id')
                ->when($startDate && !$endDate, function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })
                ->when($endDate && !$startDate, function ($query) use ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                })
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->chunk(500, function ($trackingRecordsChunk) use ($zip, $startDate, $endDate, $timestamp) {
                    static $chunkIndex = 0;

                    $pdf = PDF::loadView('dashboard.tracking_records.reports', [
                        'trackingRecords' => $trackingRecordsChunk,
                        'startDate' => $startDate,
                        'endDate' => $endDate
                    ]);

                    $pdfContent = $pdf->output();
                    $pdfFileName = 'tracking-records-' . $timestamp . '-' . $chunkIndex . '.pdf';
                    $zip->addFromString($pdfFileName, $pdfContent);

                    $chunkIndex++;
                });

            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            Log::error('Failed to create zip file for tracking records reports.');
            return redirect(route('tracking-records.index'))->with('error', 'Gagal membuat laporan.');
        }
    }
}
