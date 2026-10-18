<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_名前でお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'content' => '商品のお届けについて',
        ]);

        Contact::create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => '山田さんのお問い合わせ',
        ]);

        Contact::create([
            'first_name' => '佐藤',
            'last_name' => '花子',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '09087654321',
            'address' => '大阪府',
            'category_id' => $category->id,
            'detail' => '佐藤さんのお問い合わせ',
        ]);

        $response = $this->actingAs($user)
            ->get('/admin?keyword=山田');

        $response->assertStatus(200);
        $response->assertViewHas('contacts', function ($contacts) {
            return $contacts->count() === 1
                && $contacts->first()->first_name === '山田';
        });
    }
    public function test_メールアドレスでお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::create(["content" => "商品のお届けについて"]);

        Contact::create([
            "first_name" => "山田",
            "last_name" => "太郎",
            "gender" => 1,
            "email" => "yamada@example.com",
            "tel" => "09012345678",
            "address" => "東京都",
            "category_id" => $category->id,
            "detail" => "お問い合わせテスト",
        ]);

        $response = $this->actingAs($user)->get("/admin?keyword=yamada");

        $response->assertStatus(200);
        $response->assertViewHas("contacts", function ($contacts) {
            return $contacts->count() === 1
                && $contacts->first()->email === "yamada@example.com";
        });
    }
    public function test_性別でお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::create(["content" => "商品のお届けについて"]);

        foreach ([1, 2] as $gender) {
            Contact::create([
                "first_name" => "山田",
                "last_name" => "テスト" . $gender,
                "gender" => $gender,
                "email" => "gender" . $gender . "@example.com",
                "tel" => "09012345678",
                "address" => "東京都",
                "category_id" => $category->id,
                "detail" => "性別検索テスト",
            ]);
        }

        $response = $this->actingAs($user)->get("/admin?gender=1");

        $response->assertStatus(200);
        $response->assertViewHas("contacts", function ($contacts) {
            return $contacts->count() === 1
                && (int) $contacts->first()->gender === 1;
        });
    }
    public function test_お問い合わせ種類で検索できる(): void
    {
        $user = User::factory()->create();
        $category1 = Category::create(["content" => "商品のお届けについて"]);
        $category2 = Category::create(["content" => "商品の交換について"]);

        foreach ([$category1, $category2] as $category) {
            Contact::create([
                "first_name" => "山田",
                "last_name" => "太郎",
                "gender" => 1,
                "email" => "test" . $category->id . "@example.com",
                "tel" => "09012345678",
                "address" => "東京都",
                "category_id" => $category->id,
                "detail" => "種類検索テスト",
            ]);
        }

        $response = $this->actingAs($user)
            ->get("/admin?category_id=" . $category1->id);

        $response->assertStatus(200);
        $response->assertViewHas("contacts", function ($contacts) use ($category1) {
            return $contacts->count() === 1
                && $contacts->first()->category_id === $category1->id;
        });
    }
    public function test_登録日でお問い合わせを検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::create(["content" => "商品のお届けについて"]);

        foreach (["2026-10-01", "2026-10-02"] as $date) {
            $contact = Contact::create([
                "first_name" => "山田",
                "last_name" => "太郎",
                "gender" => 1,
                "email" => "test" . str_replace("-", "", $date) . "@example.com",
                "tel" => "09012345678",
                "address" => "東京都",
                "category_id" => $category->id,
                "detail" => "登録日検索テスト",
            ]);

            $contact->created_at = $date . " 12:00:00";
            $contact->save();
        }

        $response = $this->actingAs($user)->get("/admin?date=2026-10-01");

        $response->assertStatus(200);
        $response->assertViewHas("contacts", function ($contacts) {
            return $contacts->count() === 1
                && $contacts->first()->created_at->format("Y-m-d") === "2026-10-01";
        });
    }
    public function test_お問い合わせ一覧は1ページ7件で表示される(): void
    {
        $user = User::factory()->create();
        $category = Category::create(["content" => "商品のお届けについて"]);

        for ($i = 1; $i <= 10; $i++) {
            Contact::create([
                "first_name" => "山田",
                "last_name" => "テスト" . $i,
                "gender" => 1,
                "email" => "pagination" . $i . "@example.com",
                "tel" => "09012345678",
                "address" => "東京都",
                "category_id" => $category->id,
                "detail" => "ページネーションテスト",
            ]);
        }

        $response = $this->actingAs($user)->get("/admin");

        $response->assertStatus(200);
        $response->assertViewHas("contacts", function ($contacts) {
            return $contacts->count() === 7
                && $contacts->total() === 10
                && $contacts->lastPage() === 2;
        });
    }
}
