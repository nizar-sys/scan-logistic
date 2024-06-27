<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestInvoiceStore;
use App\Http\Requests\RequestTrackingRecord;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Product;
use App\Models\TrackingRecord;
use App\Models\User;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function dashboard()
    {
        $userCount = User::count();
        $trackingRecordCount = TrackingRecord::count();
        $productCount = Product::count();
        $invoiceCount = Invoice::count();

        return view('dashboard.index', compact('userCount', 'trackingRecordCount', 'productCount', 'invoiceCount'));
    }

    public function scan()
    {
        $products = Product::latest()->get(['id', 'name']);

        return view('scan.index', compact('products'));
    }

    public function scanStore(RequestInvoiceStore $request)
    {
        $validated = $request->validated();

        $invoice = Invoice::updateOrCreate(
            ['invoice_number' => $validated['invoice_number']],
            ['marketplace' => $request->marketplace]
        );

        $payloadInvoiceDetail = [];

        foreach ($request->products as $product) {
            $payloadInvoiceDetail[] = [
                'invoice_id' => $invoice->id,
                'product_id' => $product['product_id'],
                'size' => $product['size'],
                'quantity' => $product['qty'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        InvoiceDetail::insert($payloadInvoiceDetail);

        return response()->json([
            'message' => 'Data invoice berhasil ditambahkan.',
        ]);
    }

    public function scanPengiriman()
    {
        return view('scan.pengiriman');
    }

    public function scanPengirimanStore(RequestTrackingRecord $request)
    {
        $validated = $request->validated();

        TrackingRecord::create($validated);

        return response()->json([
            'message' => 'Resi pengiriman berhasil ditambahkan.',
        ]);
    }
}
