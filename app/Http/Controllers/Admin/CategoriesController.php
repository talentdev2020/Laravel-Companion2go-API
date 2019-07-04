<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    private function rootCategories() {
        return Category::whereNull('parent_id')->get();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        
        if (!empty($keyword)) {
            $categories = Category::with(['categories'])
                ->where('name', 'LIKE', "%$keyword%")
                ->whereNull('parent_id')
                ->paginate($perPage);
        } else {
            $categories = Category::with(['categories'])
                ->whereNull('parent_id')
                ->paginate($perPage);
        }

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create', [
            'categories' => $this->rootCategories() 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Requests\CategorySaveRequest $request)
    {
        $requestData = $request->all();
        if ($request->file('cover_photo')) {
            $requestData['cover_photo'] = $request->file('cover_photo')->store('cover-photos');
        }
        Category::create($requestData);

        return redirect('admin/categories')->with('success', 'Category added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = $this->rootCategories();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Requests\CategorySaveRequest $request, $id)
    {
        $requestData = $request->all();
        if ($request->file('cover_photo')) {
            $requestData['cover_photo'] = $request->file('cover_photo')->store('cover-photos');
        }
        $category = Category::findOrFail($id);
        $category->update($requestData);
        return redirect('admin/categories')->with('success', 'Category updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Category::destroy($id);

        return redirect('admin/categories')->with('info', 'Category deleted!');
    }
}
