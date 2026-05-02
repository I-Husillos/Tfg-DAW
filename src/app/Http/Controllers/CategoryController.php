<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index()
    {
        // Las categorías las carga DataTables vía API.
        return view('categories.index');
    }

    public function create()
    {
        // Solo categorías de primer nivel como posibles padres.
        // Una subcategoría no puede ser padre de otra.
        $parents = Category::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->store($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_stored'));
    }

    public function edit(Category $category)
    {
        abort_if($category->user_id !== Auth::id(), 403);

        $parents = Category::where('user_id', Auth::id())
            ->whereNull('parent_id')
            // Excluimos la propia categoría para que no
            // pueda ser padre de sí misma.
            ->where('id', '!=', $category->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_updated'));
    }

    public function destroy(Category $category)
    {
        abort_if($category->user_id !== Auth::id(), 403);

        // Si tiene transacciones asociadas, no se puede eliminar
        // directamente. Se advierte al usuario para que las
        // reasigne antes.
        if ($category->transactions()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', __('app.category_has_transactions'));
        }

        // Si tiene subcategorías, las desvinculamos poniendo
        // su parent_id a null antes de eliminar la padre.
        $category->children()->update(['parent_id' => null]);

        $this->categoryService->destroy($category);

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_deleted'));
    }
}
