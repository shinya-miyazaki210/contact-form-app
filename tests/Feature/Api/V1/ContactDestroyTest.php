<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactDestroyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせとタグの関連付けを削除できる(): void
    {
        $category = Category::create([
            'content' => 'API削除テスト用カテゴリ',
        ]);

        $tag = Tag::create([
            'name' => 'API削除テスト用タグ',
        ]);

        $contact = Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'api-destroy@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '削除テストです。',
        ]);

        $contact->tags()->attach($tag->id);

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);

        $this->deleteJson('/api/v1/contacts/' . $contact->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        $this->assertDatabaseMissing('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    public function test_存在しないお問い合わせの削除は404になる(): void
    {
        $this->deleteJson('/api/v1/contacts/999999999')
            ->assertNotFound();
    }
}
