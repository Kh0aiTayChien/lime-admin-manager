<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request, $conditionView)
    {
        $searchType = $request->input('search_type');
        $keyword = $request->input('search');
        $paginate = $request->input('paginate');
        if ($conditionView === 'index') {
            $query = Article::query();
        } elseif ($conditionView ==='trash') {
            $query = Article::onlyTrashed();
        }

        if ($searchType == 'title') {
            $query->where('title', 'like', '%' . $keyword . '%');
        } elseif ($searchType == 'content') {
            $query->where('content', 'like', '%' . $keyword . '%');
        }

        $articles = $query->paginate($paginate);
        return view('admin/article/index',
            compact('articles', 'conditionView' , 'paginate', 'searchType', 'conditionView'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin/article/create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|unique:articles,title',
            'slug' => 'required|string',
            'content' => 'required',
        ], [
            'title.unique' => 'Tiêu đề đã tồn tại',
            'title.required' => 'Không được để trống'
        ]);
        $article = new Article();
        $article->category_id = $request->input('category');
        $article->title = $validatedData['title'];
        $article->slug = $validatedData['slug'];
        $article->content = $validatedData['content'];
        $article->status = $request->input('status');
        if ($article->status == 1) {
            $article->order_number = $request->input('order_number') ?? 1;
        } else {
            $article->order_number = null;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('upload/images'), $imageName); // Lưu ảnh vào thư mục trên server
            $article->image = '/upload/images/' . $imageName; // Lưu đường dẫn của ảnh vào cột image trong bảng Product
        }
        $article->save();
        $conditionView = 'index';
        return redirect()->route('articles.index', ['conditionView' => $conditionView])->with('success', 'Word created successfully!');
    }

    public function show($id)
    {

    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $categories = Category::all();
        return view('admin/article/edit', ['article' => $article, 'categories' => $categories]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|',
            'slug' => 'required|string',
            'content' => 'required',
        ], [
            'title.unique' => 'Tiêu đề đã tồn tại',
            'title.required' => 'Không được để trống'
        ]);
        $article = Article::findOrFail($id);
        $article->category_id = $request->input('category');
        $article->title = $validatedData['title'];
        $article->slug = $validatedData['slug'];
        $article->content = $validatedData['content'];
        $article->status = $request->input('status');
        if ($article->status == 1) {
            $article->order_number = $request->input('order_number') ?? 1;
        } else {
            $article->order_number = null;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/images'), $imageName); // Lưu ảnh vào thư mục trên server
            $article->image = '/uploads/images/' . $imageName; // Lưu đường dẫn của ảnh vào cột image trong bảng Product
        }
        $article->save();
        $conditionView = 'index';
        return redirect()->route('articles.index', ['conditionView' => $conditionView])->with('success', 'Word created successfully!');
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
    }

    public function forceDestroy($id){
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->forceDelete();
    }

    public function restore($id){
        $article = Article::onlyTrashed()->findOrFail($id);
        $article->restore();
    }
    public function OrderNumber(Request $request)
    {
        $orderNumber = $request->input('orderNumber');
        $id = $request->input('id');
        $order = Article::find($id);

        // Kiểm tra xem order có class status = 1 không
        if ($order->status == 1) {
            // Kiểm tra xem orderNumber có bị trùng với các order khác không
            $validator = Validator::make(
                ['order_number' => $orderNumber],
                ['order_number' => Rule::unique('articles', 'order_number')->ignore($id)]
            );
            if ($validator->fails()) {
                // Trả về lỗi nếu orderNumber bị trùng
                return response()->json([
                    'success' => false,
                    'message' => 'Số thứ tự đã tồn tại',
                    'olderNumber' => $order->order_number
                ]);
            }

            // Cập nhật order_number
            $order->order_number = $orderNumber;
            $order->save();

            // Trả về kết quả là thành công và order_number mới
            return response()->json([
                'success' => true,
                'newOrderNumber' => $orderNumber
            ]);
        } else {
            // Trả về lỗi nếu order có class status khác 1
            return response()->json([
                'success' => false,
                'message' => 'Cannot update order number for this order',
            ]);
        }
    }

    public function trashBin(Request $request)
    {
        $conditionView = 'trash';
        $searchType = $request->input('search_type');
        $keyword = $request->input('search');
        $paginate = $request->input('paginate');
        $query = Article::onlyTrashed();

        if ($searchType == 'title') {
            $query->where('title', 'like', '%' . $keyword . '%');
        } elseif ($searchType == 'content') {
            $query->where('content', 'like', '%' . $keyword . '%');
        }

        $articles = $query->paginate($paginate);
        $articles->appends(['paginate' => $paginate]);

        return view('admin/article/index', compact('articles', 'conditionView'));
    }
}
