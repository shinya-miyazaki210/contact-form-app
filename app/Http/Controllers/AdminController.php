<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use App\Http\Requests\IndexContactRequest;

class AdminController extends Controller
{
    // 管理画面の一覧・検索
    public function index(IndexContactRequest $request)
    {
        $query = Contact::with(['category', 'tags'])->latest();

        // 名前・メールアドレスで検索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ["%{$keyword}%"]
                    )
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // 性別で検索（0はすべて）
        if ($request->filled('gender') && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }

        // お問い合わせ種類で検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 登録日で検索
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $contacts = $query->paginate(7);
        $categories = Category::orderBy('id')->get();
        $tags = Tag::all();

        return view('admin.index', compact("contacts", "categories", "tags"));
    }

    // お問い合わせ詳細
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return view('admin.show', compact('contact'));
    }

    // お問い合わせを削除
    public function destroy(Contact $contact)
    {
        $contact->tags()->detach();
        $contact->delete();

        return redirect()->route("admin.index");
    }
}
