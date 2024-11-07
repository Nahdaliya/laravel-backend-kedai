<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Retrieve all products, ordered by descending ID
        $products = Product::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Product',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|min:3',
            'price' => 'required|integer',
            'stock' => 'required|integer',
            'category' => 'required|in:food,drink,snack',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'is_favorite' => 'boolean' // Optional boolean field for favorite status
        ]);

        // Handle image upload
        $filename = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/products', $filename);

        // Create a new product with the provided data
        $product = Product::create([
            'name' => $request->name,
            'price' => (int) $request->price,
            'stock' => (int) $request->stock,
            'category' => $request->category,
            'image' => $filename,
            'is_favorite' => $request->is_favorite ?? false
        ]);

        // Return success response if product creation was successful
        if ($product) {
            return response()->json([
                'success' => true,
                'message' => 'Product Created',
                'data' => $product
            ], 201);
        }

        // Return failure response if product creation failed
        return response()->json([
            'success' => false,
            'message' => 'Product Failed to Save',
        ], 409);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json([
                'success' => true,
                'data' => $product
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ], 404);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|min:3',
            'price' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'category' => 'nullable|in:food,drink,snack',
            'image' => 'nullable|image|mimes:png,jpg,jpeg',
            'is_favorite' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            // Delete the old image
            if ($product->image) {
                Storage::delete('public/products/' . $product->image);
            }
            // Store the new image
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->image = $filename;
        }

        $product->update($request->only(['name', 'price', 'stock', 'category', 'is_favorite']));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($product->image) {
            Storage::delete('public/products/' . $product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
