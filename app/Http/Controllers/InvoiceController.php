<?php

namespace App\Http\Controllers;

use App\DataTables\InvoiceDataTable;
use App\DataTables\Scopes\DateFilterScope;
use App\Models\Invoice;
use App\Http\Requests\RequestStoreInvoice;
use App\Http\Requests\RequestUpdateInvoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(InvoiceDataTable $dataTable, Request $request)
    {
        return $dataTable
            ->addScopes([
                new DateFilterScope($request),
            ])
            ->render('dashboard.invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestStoreInvoice $request)
    {
        $validated = $request->validated();

        $invoice = Invoice::updateOrCreate(
            ['invoice_number' => $validated['invoice_number']],
            ['marketplace' => $request->marketplace]
        );

        $payloadInvoiceDetail = [];

        foreach ($request->product_id as $key => $product) {
            $payloadInvoiceDetail[] = [
                'invoice_id' => $invoice->id,
                'product_id' => $product,
                'size' => $request->size[$key],
                'quantity' => $request->qty[$key],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        InvoiceDetail::insert($payloadInvoiceDetail);

        return redirect(route('invoices.index'))->with('success', 'Invoice berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Invoice::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);

        return view('dashboard.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RequestUpdateInvoice $request, $id)
    {
        $validated = $request->validated() + [
            'updated_at' => now(),
        ];

        $invoice = Invoice::findOrFail($id);

        $invoice->update($validated);

        return redirect(route('invoices.index'))->with('success', 'Invoice berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        $invoice->delete();

        return back()->with('success', 'Invoice berhasil dihapus.');
    }

    public function reports(Request $request)
    {
        Carbon::setLocale('id');
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $zip = new ZipArchive();
        $timestamp = now()->format('Y-m-d-H-i-s');
        $zipFileName = 'invoices-' . $timestamp . '.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            Invoice::orderByDesc('id')
                ->with('details:id,invoice_id,product_id,size,quantity', 'details.product:id,code,name')
                ->when($startDate && !$endDate, function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })
                ->when($endDate && !$startDate, function ($query) use ($endDate) {
                    $query->where('created_at', '<=', $endDate);
                })
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->chunk(250, function ($invoices) use ($zip, $startDate, $endDate, $timestamp) {
                    static $chunkIndex = 0;

                    $pdf = PDF::loadView('dashboard.invoices.reports', [
                        'invoices' => $invoices,
                        'startDate' => $startDate,
                        'endDate' => $endDate
                    ]);

                    $pdfContent = $pdf->output();
                    $pdfFileName = 'invoices-' . $timestamp . '-' . $chunkIndex . '.pdf';
                    $zip->addFromString($pdfFileName, $pdfContent);

                    $chunkIndex++;
                });

            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            Log::error('Failed to create zip file for tracking records reports.');
            return redirect(route('invoices.index'))->with('error', 'Gagal membuat laporan.');
        }
    }

    public function print($invoice_number)
    {
        $invoice = Invoice::where('invoice_number', $invoice_number)
            ->with('details:id,invoice_id,product_id,size,quantity,availability', 'details.product:id,code,name,image')
            ->firstOrFail();
        $invoice->update(['status_print' => 1]);

        $pdf = PDF::loadView('dashboard.invoices.print', compact('invoice'));
        // pdf settings a4
        $pdf->setPaper('a4');

        return $pdf->stream('invoice-' . $invoice_number . '.pdf');
    }
}
