<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせを送信すると保存される(): void
    {
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $response = $this->post('/contacts', [
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

        $response->assertRedirect('/thanks');

        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'email' => 'test@example.com',
            'category_id' => $category->id,
        ]);
    }
    public function test_お問い合わせ完了画面が表示される(): void
    {
        $response = $this->get("/thanks");

        $response->assertStatus(200);
        $response->assertViewIs("contact.thanks");
    }
}
