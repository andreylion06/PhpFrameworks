<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Get all products
     */
    public function getProducts(): JsonResponse
    {
        return response()->json(['data' => Product::all()], 200);
    }

    /**
     * Get single product by ID
     */
    public function getProductItem($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], 404);
        }

        return response()->json(['data' => $product], 200);
    }

    /**
     * Create a new product
     */
    public function createProduct(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $product = Product::create([
            'name' => $requestData['name'],
            'description' => $requestData['description'],
            'price' => $requestData['price'],
        ]);

        return response()->json(['data' => ['message' => 'Product created', 'id' => $product->id]], 201);
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], 404);
        }

        $requestData = json_decode($request->getContent(), true);
        $product->update([
            'name' => $requestData['name'] ?? $product->name,
            'description' => $requestData['description'] ?? $product->description,
            'price' => $requestData['price'] ?? $product->price,
        ]);

        return response()->json(['data' => ['message' => 'Product updated']], 200);
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['data' => ['error' => 'Not found product by id ' . $id]], 404);
        }

        $product->delete();

        return response()->json(['data' => ['message' => 'Product deleted']], 200);
    }
}
