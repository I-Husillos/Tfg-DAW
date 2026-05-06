<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.category_stored'),
                'data' => $category,
            ], 201);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_stored'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $updatedCategory = $this->categoryService->update($category, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.category_updated'),
                'data' => $updatedCategory,
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_updated'));
    }

    public function destroy(Category $category, \Illuminate\Http\Request $request)
    {
        abort_if($category->user_id !== Auth::id(), 403);

        if ($category->transactions()->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('app.category_has_transactions'),
                ], 422);
            }

            return redirect()
                ->route('categories.index')
                ->with('error', __('app.category_has_transactions'));
        }

        $category->children()->update(['parent_id' => null]);
        $this->categoryService->destroy($category);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('app.category_deleted'),
            ]);
        }

        return redirect()
            ->route('categories.index')
            ->with('success', __('app.category_deleted'));
    }
}
