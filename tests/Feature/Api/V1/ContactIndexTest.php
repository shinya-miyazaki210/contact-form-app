<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactIndexTest extends TestCase
{
    use DatabaseTransactions;

    private function createContact(Category $category, string $name, int $gender = 1): Contact
    {
        return Contact::create([
            'first_name' => $name,
            'last_name' => 'テスト',
            'gender' => $gender,
            'email' => uniqid('api_') . '@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'API一覧テスト',
        ]);
    }

    public function test_お問い合わせ一覧を取得できる(): void
    {
        $category = Category::create(['content' => 'APIテスト用']);
        $contact = $this->createContact($category, '山田');

        $this->getJson('/api/v1/contacts')
            ->assertOk()
            ->assertJsonPath('data.0.id', $contact->id)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_キーワードで検索できる(): void
    {
        $category = Category::create(['content' => 'APIテスト用']);
        $this->createContact($category, '山田');
        $this->createContact($category, '佐藤');

        $this->getJson('/api/v1/contacts?keyword=山田')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.first_name', '山田');
    }

    public function test_性別とカテゴリで絞り込める(): void
    {
        $category1 = Category::create(['content' => 'カテゴリA']);
        $category2 = Category::create(['content' => 'カテゴリB']);

        $target = $this->createContact($category1, '山田', 1);
        $this->createContact($category1, '佐藤', 2);
        $this->createContact($category2, '田中', 1);

        $this->getJson(
            '/api/v1/contacts?gender=1&category_id=' . $category1->id
        )
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $target->id);
    }

    public function test_ページネーションが機能する(): void
    {
        $category = Category::create(['content' => 'APIテスト用']);

        for ($i = 0; $i < 5; $i++) {
            $this->createContact($category, '山田' . $i);
        }

        $this->getJson('/api/v1/contacts?per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 5)
            ->assertJsonPath('meta.last_page', 3);
    }

    public function test_不正な検索条件は422になる(): void
    {
        $this->getJson('/api/v1/contacts?gender=9')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['gender']);
    }
}
