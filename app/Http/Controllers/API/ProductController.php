<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ProductController extends Controller
{
    public function create(Request $request)
    {
        // Xác thực dữ liệu request
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'author' => 'required|string|max:255',
            'discount' => 'required|integer',

            'description' => 'nullable|string',
            'parentID' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
        $file = null;
        if ($request->has('image')) {
            $fileName = Str::random(32).".".$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('uploads/product/'), $fileName);
            $file = 'uploads/product/' . $fileName;
        }
    
        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'author' => $request->input('author'),
            'discount' => $request->input('discount'),
            'description' => $request->input('description'),
            'parentID' => $request->input('parentID'),
            'image' => $file,
        ]);
    
        return response()->json([
            'status' => 200,
            'message' => 'Tạo sản phẩm thành công',
            'product' => $product,
        ]);
    }
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {  
            if ($request->has('name') && $request->input('name') !== $product->name) {
                $product->name = $request->input('name');
            }
            if ($request->has('price') && $request->input('price') !== $product->price) {
                $product->price = $request->input('price');
            }
            if ($request->has('quantity') && $request->input('quantity') !== $product->quantity) {
                $product->quantity = $request->input('quantity');
            }
            if ($request->has('description') && $request->input('description') !== $product->description) {
                $product->description = $request->input('description');
            }
            if ($request->has('parentID') && $request->input('parentID') !== $product->parentID) {
                $product->parentID = $request->input('parentID');
            }
            $oldImagePath = $product->image;
    
            if ($request->hasFile('image')) {
                if (file_exists(public_path($oldImagePath))) {
                    unlink(public_path($oldImagePath));
                }
                $fileName = Str::random(32).".".$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('uploads/product/'), $fileName);
                $product->image = 'uploads/product/' . $fileName;
            }
    
            $product->update();
        }
    
        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }
    public function index() {
        $products = Product::all();
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }
    public function getProductsPaginate(Request $request)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $totalPages = 0;
        $products = DB::select('CALL GetProductWithPaginate(?, ?, @totalPages)', [$page, $pageSize]);
        $totalPagesResult = DB::select('SELECT @totalPages AS totalPages');
        $totalPages = $totalPagesResult[0]->totalPages;
        return response()->json([
            'products' => $products,
            'totalPages' => $totalPages,
        ]);
    }

    public function getProductCart(Request $request) {
        $listIdJson = $request->input('list');
        $listId = json_decode($listIdJson, true);
        $products = Product::whereIn('id', $listId)->get();
        return response()->json([
            'products' => $products
        ]);

    }
    public function showProductDetail($id)
    {
        $product = DB::select('CALL GetProductById(?)', [$id]);

        if (empty($product)) {
            return response()->json(['message' => 'Không có thông tin về sản phẩm'], 404);
        }

        return response()->json($product[0]);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $products = Product::where('name', 'like', "%$searchTerm%")->get();

        return response()->json(['products' => $products]);
    }

    public function advancedSearch(Request $request)
    {
        $sortOrder = $request->input('sortOrder', 'asc');
        $priceRange = $request->input('priceRange', 1);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
    
        $totalPages = 0;
        $products = DB::select(
            'CALL AdvanceSearch(?, ?, ?, ?, @total_pages)',
            [$sortOrder, $priceRange, $page, $pageSize]
        );
    
        $totalPages = DB::select('SELECT @total_pages as total_pages')[0]->total_pages;
    
        return response()->json([
            'products' => $products,
            'totalPages' => $totalPages
        ]);
    }
    public function getTrendyProduct(Request $request)
    {
        $type = $request->query('type');

        switch ($type) {
            case 'latest':
                $products = Product::orderBy('created_at', 'desc')->take(8)->get();
                break;

            case 'most_viewed':
                $products = Product::orderBy('view', 'desc')->take(8)->get();
                break;

            case 'discount':
                $products = Product::orderBy('discount', 'desc')->take(8)->get();
                break;

            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        return response()->json($products);
    }

    public function bestsellers()
    {
        $results = DB::select('CALL GetTopSellingProducts()');
        $totalQuantity = DB::table('orderdetails')->sum('quantity');
        $topProductsTotalQuantity = array_reduce($results, function($carry, $item) {
            return $carry + $item->total_quantity;
        }, 0);
        $otherQuantity = $totalQuantity - $topProductsTotalQuantity;

        return response()->json(['results'=> $results,'totalQuantity' => $totalQuantity, 'otherQuantity' => $otherQuantity]);
    }

    public function getProductsByCategory(Request $request, $categoryId)
    {
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);

        $totalPages = 0;

        $products = DB::select('CALL GetProductByCategory(?, ?, ?, @totalPages)', [$categoryId, $page, $pageSize]);
        $totalPages = DB::select('SELECT @totalPages AS totalPages');

        return response()->json([
            'products' => $products,
            'totalPages' => $totalPages[0]->totalPages
        ]);
    }
   

    
}
