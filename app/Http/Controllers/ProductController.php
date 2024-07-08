<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Http\Requests\RequestStoreProduct;
use App\Http\Requests\RequestUpdateProduct;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductDataTable $dataTable)
    {
        return $dataTable->render('dashboard.products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RequestStoreProduct $request)
    {
        $validated = $request->validated() + ['created_at' => now()];

        $payloadProductDetail = [];

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $validated['image'] = $fileName;

            $request->image->move(public_path('uploads/images'), $fileName);
        }

        $product = Product::create($validated);

        $sizes = $request->input('product_detail.size', []);
        $quantities = $request->input('product_detail.quantity', []);

        foreach ($sizes as $index => $size) {
            if (isset($quantities[$index])) {
                $payloadProductDetail[] = [
                    'product_id' => 1,
                    'size' => $size,
                    'quantity' => $quantities[$index],
                    'created_at' => now(),
                ];
            }
        }

        $product->productDetails()->createMany($payloadProductDetail);

        return redirect(route('products.index'))->with('success', 'Data produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with('productDetails:id,product_id,size,quantity')->findOrFail($id);

        return view('dashboard.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RequestUpdateProduct $request, $id)
    {
        $validated = $request->validated() + ['updated_at' => now()];

        $product = Product::with('productDetails')->findOrFail($id);

        $validated['image'] = $product->image;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $validated['image'] = $fileName;

            $request->image->move(public_path('uploads/images'), $fileName);

            $oldPath = public_path('/uploads/images/' . $product->image);
            if (file_exists($oldPath) && $product->image != 'image.png') {
                unlink($oldPath);
            }
        }

        $product->update($validated);

        $sizes = $request->input('product_detail.size', []);
        $quantities = $request->input('product_detail.quantity', []);
        $productDetailIds = $request->input('product_detail.id', []);

        $payloadProductDetail = [];

        $incomingDetailIds = [];

        foreach ($sizes as $index => $size) {
            if (isset($quantities[$index])) {
                $id = $productDetailIds[$index] ?? null;
                $incomingDetailIds[] = $id;

                $payloadProductDetail[] = [
                    'id' => $id,
                    'product_id' => $product->id,
                    'size' => $size,
                    'quantity' => $quantities[$index],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        ProductDetail::upsert(
            $payloadProductDetail,
            ['id'], // Kolom unik
            ['size', 'quantity', 'updated_at'] // Kolom yang akan diperbarui
        );

        $existingDetailIds = $product->productDetails->pluck('id')->toArray();

        $detailIdsToDelete = array_diff($existingDetailIds, $incomingDetailIds);

        if (!empty($detailIdsToDelete)) {
            ProductDetail::whereIn('id', $detailIdsToDelete)->delete();
        }

        return redirect(route('products.index'))->with('success', 'Data produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        // delete old file
        $oldPath = public_path('/uploads/images/' . $product->image);
        if (file_exists($oldPath) && $product->image != 'image.png') {
            unlink($oldPath);
        }
        $product->delete();

        return redirect(route('products.index'))->with('success', 'Data produk berhasil dihapus.');
    }

    public function getProducts(Request $request)
    {
        $search = $request->input('q');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            })
            ->latest()
            ->get(['id', 'name']);

        return response()->json($products);
    }

    public function productDetail($productId)
    {
        $product = ProductDetail::where('product_id', $productId)->get(['id', 'product_id', 'size', 'quantity']);

        return response()->json($product);
    }
}
