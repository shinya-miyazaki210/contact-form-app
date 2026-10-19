<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactUpdateTest extends TestCase
{
    use DatabaseTransactions;

    private function createContact(Category $category): Contact
    {
        return Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'before-update@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '更新前のお問い合わせ',
        ]);
    }

    public function test_お問い合わせとタグを更新できる(): void
    {
        $category = Category::create([
            'content' => 'API更新テスト用カテゴリ',
        ]);

        $oldTag = Tag::create(['name' => '更新前タグ']);
        $newTag = Tag::create(['name' => '更新後タグ']);

        $contact = $this->createContact($category);
        $contact->tags()->attach($oldTag->id);

        $response = $this->putJson('/api/v1/contacts/'.$contact->id, [
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'after-update@example.com',
            'tel' => '08012345678',
            'address' => '大阪府',
            'category_id' => $category->id,
            'detail' => '更新後のお問い合わせ',
            'tag_ids' => [$newTag->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.first_name', '佐藤')
            ->assertJsonPath('data.email', 'after-update@example.com');

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '佐藤',
            'email' => 'after-update@example.com',
        ]);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $newTag->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $oldTag->id,
        ]);
    }

    public function test_存在しないお問い合わせの更新は404になる(): void
    {
        $this->putJson('/api/v1/contacts/999999999', [])
            ->assertNotFound();
    }

    public function test_不正な入力は422になり元のデータは変わらない(): void
    {
        $category = Category::create([
            'content' => 'API更新テスト用カテゴリ',
        ]);

        $contact = $this->createContact($category);

        $this->putJson('/api/v1/contacts/'.$contact->id, [
            'gender' => 9,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['gender']);

        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'first_name' => '山田',
            'gender' => 1,
            'email' => 'before-update@example.com',
        ]);
    }
}
