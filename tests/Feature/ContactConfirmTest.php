<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactConfirmTest extends TestCase
{
    use DatabaseTransactions;

    public function test_正しい入力でお問い合わせ確認画面が表示される(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $response = $this->post('/contacts/confirm', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => '',
            'category_id' => $category->id,
            'detail' => 'お問い合わせのテストです。',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
    }
}
