<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // Show product list page
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->get();
        return view('products.list', compact('products'));
    }

    // Show create product form
    public function create()
    {
        return view('products.create');
    }

    // Store product
    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Assuming max file size is 2MB
        ];

        // Validate input
        $validator = Validator::make($request->all(), $rules);

        // Redirect back if validation fails
        if ($validator->fails()) {
            return redirect()->route('products.create')->withInput()->withErrors($validator);
        }

        // Create new product
        $product = new Product();
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();

        // Store image if provided
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        // Redirect to index page with success message
        return redirect()->route('products.index')->with('success', 'Product added successfully');
    }

    // Show edit product form
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        // Find product by ID
        $product = Product::findOrFail($id);

        // Validation rules
        $rules = [
            'name' => 'required|min:5',
            'sku' => 'required|min:3',
            'price' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Assuming max file size is 2MB
        ];

        // Validate input
        $validator = Validator::make($request->all(), $rules);

        // Redirect back if validation fails
        if ($validator->fails()) {
            return redirect()->route('products.edit', $product->id)->withInput()->withErrors($validator);
        }

        // Update product
        $product->name = $request->name;
        $product->sku = $request->sku;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();

        // Store image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                File::delete(public_path('uploads/products/' . $product->image));
            }
            // Store new image
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $product->image = $imageName;
            $product->save();
        }

        // Redirect to index page with success message
        return redirect()->route('products.index')->with('success', 'Product updated successfully Moyan');
    }

    // Delete and Destroy product
    public function destroy($id)
    {
        // Find product by ID
        $product = Product::findOrFail($id);

        // Delete image file if exists
        if ($product->image) {
            File::delete(public_path('uploads/products/' . $product->image));
        }

        // Delete product from DB
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully By Moyan');
    }
}

