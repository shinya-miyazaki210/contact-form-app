<?php

use App\Http\Controllers\ContactController;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = Category::orderByRaw(
        "CASE content
            WHEN '商品のお届けについて' THEN 1
            WHEN '商品の交換について' THEN 2
            WHEN '商品トラブル' THEN 3
            WHEN 'ショップへのお問い合わせ' THEN 4
            WHEN 'その他' THEN 5
            ELSE 99
        END"
    )->get();

    $tags = Tag::all();

    return view('contact.index', compact('categories', 'tags'));
});

Route::post('/contacts/confirm', [ContactController::class, 'confirm'])
    ->name('contacts.confirm');
Route::post('/contacts', [ContactController::class, 'store'])
    ->name('contacts.store');
Route::get('/thanks', [ContactController::class, 'thanks'])
    ->name('contacts.thanks');