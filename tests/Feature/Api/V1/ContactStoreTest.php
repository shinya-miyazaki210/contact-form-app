<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせを作成できる(): void
    {
        $category = Category::create([
            'content' => 'API作成テスト用カテゴリ',
        ]);

        $tag = Tag::create([
            'name' => 'API作成テスト用タグ',
        ]);

        $response = $this->postJson('/api/v1/contacts', [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'api-store@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'API作成テストです。',
            'tag_ids' => [$tag->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.first_name', '山田')
            ->assertJsonPath('data.email', 'api-store@example.com');

        $this->assertDatabaseHas('contacts', [
            'email' => 'api-store@example.com',
            'category_id' => $category->id,
        ]);

        $contact = Contact::where('email', 'api-store@example.com')->firstOrFail();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_必須項目が未入力なら422になる(): void
    {
        $this->postJson('/api/v1/contacts', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'category_id',
                'detail',
            ]);
    }
}
