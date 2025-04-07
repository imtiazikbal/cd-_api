<?php

namespace App\Http\Controllers\API\V1\Client\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Media;
use App\Traits\sendApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    use sendApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = 40;

        if (request()->query('perPage')) {
            $perPage = request()->query('perPage');
        }

        $categories = Category::query()->with('category_image')
            ->where('parent_id', 0)
            ->where('shop_id', $request->header('shop-id'))
            ->orderByDesc('id')
            ->paginate($perPage);

        return $this->sendApiResponse($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $query = Category::query()->where('shop_id', $request->header('shop-id'))
            ->where('name', $request->input('name'))
            ->first();

        if ($query) {
            throw validationException::withMessages(['category' => 'This category already exist']);
        }

        $category = Category::query()->create([
            'name'      => $request->input('name'),
            'slug'      => Str::slug($request->input('name')),
            'shop_id'   => $request->header('shop-id'),
            'parent_id' => $request->input('parent_id') ?: 0,
            'status'    => $request->input('status'),
        ]);

        if ($request->hasFile('category_image')) {

            Media::upload($category, $request->file('category_image'), $this->path($request), 'category');
        }
        $category->load('category_image');

        return $this->sendApiResponse($category, 'Category created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $category = Category::with('category_image', 'subcategory')
            ->where('id', $id)
            ->where('shop_id', $request->header('shop-id'))
            ->first();

        if (!$category) {
            return $this->sendApiResponse('', 'No category found');
        }

        return $this->sendApiResponse($category);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::query()->with('category_image')->find($id);

        if (!$category) {
            return $this->sendApiResponse('', 'No category found');
        }
        $data = $request->except('category_image', 'user_id');

        if ($request->filled('name')) {
            $data['slug'] = Str::slug($request->input('name'));
        }
        $category->update($data);

        if ($request->hasFile('category_image')) {
            if ($category->category_image !== null) {
                $category->category_image->replaceWith($request->file('category_image'), $this->path($request));
            }
            Media::upload($category, $request->file('category_image'), $this->path($request), 'category');
        }
        $category->load('category_image');

        return $this->sendApiResponse($category, 'category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = Category::with('category_image')->find($id);

        if (!$category) {
            return $this->sendApiResponse('', 'Category not found', 'NotFound');
        }

        if ($category->category_image !== null) {
            $category->category_image->delete();
        }
        $category->delete();

        return $this->sendApiResponse('', 'Category deleted successfully');
    }

    public function path($request): string
    {
        return Category::FILEPATH . $request->header('id');
    }
}
