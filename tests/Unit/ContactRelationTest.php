<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactRelationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせからカテゴリを取得できる(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = new Contact([
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $contact->category);
        $this->assertEquals($category->id, $contact->category->id);
    }

    public function test_カテゴリからお問い合わせを取得できる(): void
    {
        $category = Category::create([
            'content' => 'サービスについて',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせのテストです。',
        ]);

        $this->assertTrue($category->contacts->contains($contact));
        $this->assertEquals(1, $category->contacts->count());
    }

    public function test_お問い合わせから複数のタグを取得できる(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせのテストです。',
        ]);

        $tag1 = Tag::create(['name' => '質問']);
        $tag2 = Tag::create(['name' => '確認']);

        $contact->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertEquals(2, $contact->tags->count());
        $this->assertTrue($contact->tags->contains($tag1));
        $this->assertTrue($contact->tags->contains($tag2));
    }

    public function test_タグから複数のお問い合わせを取得できる(): void
    {
        $category = Category::create([
            'content' => '商品について',
        ]);

        $tag = Tag::create(['name' => '重要']);

        $contact1 = Contact::create([
            'category_id' => $category->id,
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test1@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'detail' => 'お問い合わせ1',
        ]);

        $contact2 = Contact::create([
            'category_id' => $category->id,
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'test2@example.com',
            'tel' => '08012345678',
            'address' => '東京都新宿区',
            'detail' => 'お問い合わせ2',
        ]);

        $tag->contacts()->attach([$contact1->id, $contact2->id]);

        $this->assertEquals(2, $tag->contacts->count());
        $this->assertTrue($tag->contacts->contains($contact1));
        $this->assertTrue($tag->contacts->contains($contact2));
    }
}
