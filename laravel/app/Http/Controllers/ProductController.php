<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function getProducts()
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $products = Product::all();

        return response()->json(['data' => $products], 200);
    }

    public function getProductItem($id)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found by id ' . $id], 404);
        }

        return response()->json(['data' => $product], 200);
    }

    public function createProduct(Request $request)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created',
            'data' => $product,
        ], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $this->authorizeRole(['ROLE_MANAGER']);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found by id ' . $id], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $product->update($validated);

        return response()->json(['message' => 'Product updated', 'data' => $product], 200);
    }

    public function deleteProduct($id)
    {
        $this->authorizeRole(['ROLE_ADMIN']);

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found by id ' . $id], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
