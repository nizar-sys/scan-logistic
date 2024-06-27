<?php

namespace App\Http\Controllers;

use App\DataTables\ProductDataTable;
use App\Http\Requests\RequestStoreProduct;
use App\Http\Requests\RequestUpdateProduct;
use App\Models\Product;
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
        $validated = $request->validated() + [
            'created_at' => now(),
        ];

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $validated['image'] = $fileName;

            // move file
            $request->image->move(public_path('uploads/images'), $fileName);
        }

        Product::create($validated);

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
        $product = Product::findOrFail($id);

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
        $validated = $request->validated() + [
            'updated_at' => now(),
        ];

        $product = Product::findOrFail($id);

        $validated['image'] = $product->image;

        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $validated['image'] = $fileName;

            // move file
            $request->image->move(public_path('uploads/images'), $fileName);

            // delete old file
            $oldPath = public_path('/uploads/images/' . $product->image);
            if (file_exists($oldPath) && $product->image != 'image.png') {
                unlink($oldPath);
            }
        }

        $product->update($validated);

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
}
