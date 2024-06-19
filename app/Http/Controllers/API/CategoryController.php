<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    // Lấy danh mục cha
    public function getParent() {
        $category = Categories::whereNull('parentID')->get();
        return response()->json($category);

    }
    // Lấy danh mục con
    public function getSubCategories($id) {
        $category = DB::select('CALL GetSubcategories(?)', [$id]);
        return response()->json($category);
    }

    public function getAll() {
        $categories = DB::select('CALL GetAllCategories()');
        return response()->json($categories, 200);
    }
    public function getPaginatedCategories(Request $request)
    {
        $page = $request->input('page'); 
        $pageSize = $request->input('pageSize'); 
        $categories = DB::select("CALL GetCategoriesPaginate(?, ?, @totalPages)", [$page, $pageSize]);
        $totalPages = DB::select("SELECT @totalPages as totalPages")[0]->totalPages;
        
        return response()->json([
            'categories' => $categories,
            'totalPages' => $totalPages
        ]);
    }
    public function create (Request $request) {
        $name = $request->input('name'); 
        $parentID = $request->input('parentID'); 
        DB::statement("CALL createCategory(?, ?)", [$name, $parentID]);
        return response()->json(['message' => 'Thêm thành công', 'status' => 200]);

    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parentID' => 'nullable|integer|exists:categories,id'
        ]);

        $name = $request->input('name');
        $parentID = $request->input('parentID');

        DB::statement('CALL UpdateCategory(?, ?, ?)', [$id, $name, $parentID]);

        return response()->json(['status' => 200,'message' => 'Sửa thành công !']);
    }

    public function delete($id) {
        $category = Categories::find($id);
        if($category) {
            $category->delete();
            return response()->json(['status' => 200, 'message' => 'Xóa thành công !']);
        }
    }
   
}
