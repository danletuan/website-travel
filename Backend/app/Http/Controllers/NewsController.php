<?php

namespace App\Http\Controllers;

use App\Services\BaseService;
use Illuminate\Http\Request;
use App\Services\ArticleService;
use App\Models\News;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;


class NewsController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->service = new BaseService(new News());
    }

    public function index()
    {
        try {
            $posts = $this->service->getAll();
            return response()->json($posts);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = $this->service->getById($id);
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy dữ liệu'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:news,slug',
                'content' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'status' => 'required|in:0,1,2',
            ]);

            $post = $this->service->create($validatedData);
            return response()->json($post, 201);
        } catch (ValidationException $e) {
            \Log::error('Lỗi xảy ra khi lưu bài viết: ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Lỗi xảy ra khi lưu bài viết: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    // Add unique rule conditionally
                    Rule::unique('news', 'slug')->ignore($id),
                ],
                'content' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
                'status' => 'sometimes|required|in:0,1,2',
            ]);

            $post = $this->service->update($validatedData, $id);
            return response()->json($post);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy dữ liệu'], 404);
        } catch (ValidationException $e) {
            \Log::error('Lỗi xảy ra ' . $e->getMessage());
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Lỗi xảy ra ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            \Log::error('Lỗi xảy ra ' . $e->getMessage());
            return response()->json(['message' => 'Không tìm thấy dữ liệu'], 404);
        } catch (\Exception $e) {
            \Log::error('Lỗi xảy ra ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra, vui lòng thử lại sau.'], 500);
        }
    }

    public function getPostsByStatus($status)
    {
        try {
            $posts = News::where('status', $status)
                ->get();
            return response()->json($posts);
        } catch (\Exception $e) {
            // Ghi lại lỗi vào log và trả về phản hồi lỗi
            \Log::error('Lỗi khi lấy bài viết theo trạng thái: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy bài viết.'], 500);
        }
    }

    public function getPostsByCategory($category_id)
    {
        try {
            $posts = News::where('category_id', $category_id)
                ->get();
            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi lấy bài viết theo danh mục: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy bài viết.'], 500);
        }
    }

    public function getPostsBySlug($slug)
    {
        try {
            $posts = News::where('slug', $slug)
                ->get();
            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi lấy bài viết theo danh mục: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy bài viết.'], 500);
        }
    }


    public function updateDraft(Request $request, $id)
    {
        try {
            $post = News::find($id);

            if (!$post) {
                return response()->json(['message' => 'Không tìm thấy bài viết'], 404);
            }

            $draftData = json_encode([
                'category_id' => $request->input('category_id'),
                'title' => $request->input('title'),
                'slug' => $request->input('slug'),
                'content' => $request->input('content'),
                'status' => $request->input('status'),
                'img' => $request->input('img'),
            ]);

            $post->draft = $draftData;
            $post->save();

            return response()->json(['message' => 'Cập nhật draft thành công', 'draft' => $draftData], 200);
        } catch (\Exception $e) {
            // Ghi lại lỗi vào log và trả về phản hồi lỗi
            \Log::error('Lỗi khi cập nhật draft bài viết: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật draft.'], 500);
        }
    }

    public function createDraft(Request $request)
    {
        try {
            // Validate dữ liệu
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:news,slug',
                'content' => 'required',
                'image' => 'nullable|string',
            ]);

            // Tạo bản ghi mới trong bảng news
            $post = new News();
            $post->category_id = $validatedData['category_id'];
            $post->title = $validatedData['title'];
            $post->slug = $validatedData['slug'];
            $post->content = $validatedData['content'];
            $post->image = $validatedData['image'] ?? null; // Nếu không có hình ảnh, đặt giá trị null
            $post->draft = json_encode([
                'category_id' => $validatedData['category_id'],
                'title' => $validatedData['title'],
                'slug' => $validatedData['slug'],
                'content' => $validatedData['content'],
                'image' => $validatedData['image'] ?? null,
            ]);
            $post->status = 0; // Đảm bảo rằng status được đặt là draft (0)
            $post->save();

            return response()->json(['message' => 'Tạo draft thành công', 'draft' => $post->draft], 201);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi tạo draft bài viết: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi tạo draft.'], 500);
        }
    }





    // protected $articleService;
    // public function __construct(ArticleService $articleService)
    // {
    //     $this->articleService = $articleService;
    // }

    // public function index(Request $request)
    // {
    //     $category = $request->query('category', 'Adventure Travel');
    //     $news = $this->articleService->getAllNews($category);
    //     $categories = Category::all();

    //     return view('home.news', compact('news', 'category', 'categories'));
    // }

    // public function show($slug)
    // {
    //     // Sử dụng service để lấy tin tức theo slug
    //     $news = $this->articleService->getNewsBySlug($slug);

    //     // Truyền dữ liệu tới view
    //     return view('home.news-detail', [
    //         'news' => $news
    //     ]);
    // }
}

