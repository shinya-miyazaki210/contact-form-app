<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // お問い合わせカテゴリの仮データ
    $categories = collect([
        (object) ['id' => 1, 'content' => '商品について'],
        (object) ['id' => 2, 'content' => 'サービスについて'],
        (object) ['id' => 3, 'content' => 'その他'],
    ]);

    // タグの仮データ
    $tags = collect([
        (object) ['id' => 1, 'name' => '商品'],
        (object) ['id' => 2, 'name' => 'サービス'],
        (object) ['id' => 3, 'name' => 'その他'],
    ]);

    return view('contact.index', compact('categories', 'tags'));
});
