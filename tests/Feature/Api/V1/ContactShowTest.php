<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactShowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせ詳細を取得できる(): void
    {
        $category = Category::create([
            'content' => 'API詳細テスト用カテゴリ',
        ]);

        $tag = Tag::create([
            'name' => 'API詳細テスト用タグ',
        ]);

        $contact = Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'api-show@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '詳細取得のテストです。',
        ]);

        $contact->tags()->attach($tag->id);

        $this->getJson('/api/v1/contacts/'.$contact->id)
            ->assertOk()
            ->assertJsonPath('data.id', $contact->id)
            ->assertJsonPath('data.first_name', '山田')
            ->assertJsonPath('data.email', 'api-show@example.com')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'category',
                    'tags',
                ],
            ]);
    }

    public function test_存在しないお問い合わせは404になる(): void
    {
        $this->getJson('/api/v1/contacts/999999999')
            ->assertNotFound();
    }
}
