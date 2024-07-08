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
        $products = Product::with('productDetails:id,product_id,size,quantity')->latest()->get(['id', 'name']);

        return view('scan.index', compact('products'));
    }

    public function scanStore(RequestInvoiceStore $request)
    {
        $validated = $request->validated();

        $invoice = Invoice::updateOrCreate(
            ['invoice_number' => $validated['invoice_number']],
            ['marketplace' => $request->marketplace]
        );

        $productIds = array_column($request->products, 'product_id');
        $sizes = array_column($request->products, 'size');

        $products = Product::with(['productDetails' => function ($query) use ($sizes) {
            $query->whereIn('size', $sizes);
        }])->whereIn('id', $productIds)->get();

        $payloadInvoiceDetail = [];

        foreach ($request->products as $productRequest) {
            $product = $products->firstWhere('id', $productRequest['product_id']);
            if ($product) {
                $productDetail = $product->productDetails->firstWhere('size', $productRequest['size']);
                if ($productDetail) {

                    if ($productDetail->quantity >= $productRequest['qty']) {
                        $availability = 'tersedia';
                    } else {
                        $availability = 'ketersediaan: ' . $productDetail->quantity;
                    }

                    $payloadInvoiceDetail[] = [
                        'invoice_id' => $invoice->id,
                        'product_id' => $productRequest['product_id'],
                        'size' => $productRequest['size'],
                        'quantity' => $productRequest['qty'],
                        'availability' => $availability,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $decrementValue = min($productDetail->quantity, $productRequest['qty']);
                    $productDetail->decrement('quantity', $decrementValue);
                }
            }
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
