<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $result = Category::with(['categories'])
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->orderBy('order', 'ASC')
            ->get();
            
        return response()->json([
            'success' => true, 
            'data' => $result
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request, $category)
    {
        $result = Category::with(['categories'])
            ->where('id', $category)
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->first();
        
        if (empty($result)) 
        {
            return response()->json(['success' => false], 404);
        }
            
        return response()->json([
            'success' => true, 
            'data' => $result
        ]);
    }
}
