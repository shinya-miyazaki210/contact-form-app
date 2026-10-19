<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminDetailTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせ詳細画面を表示できる(): void
    {
        $user = User::factory()->create();

        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        $contact = Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'detail@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '詳細表示のテストです。',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/contacts/'.$contact->id);

        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertViewHas('contact', function ($viewContact) use ($contact) {
            return $viewContact->id === $contact->id;
        });
    }
}
