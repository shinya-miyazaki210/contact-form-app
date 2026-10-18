<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactExportTest extends TestCase
{
    use DatabaseTransactions;

    private function createContact(Category $category, string $name, int $gender): Contact
    {
        return Contact::create([
            'first_name' => $name,
            'last_name' => 'テスト',
            'gender' => $gender,
            'email' => uniqid('csv_') . '@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'category_id' => $category->id,
            'detail' => 'CSVエクスポートテスト',
        ]);
    }

    private function csvContent($response): string
    {
        ob_start();
        $response->baseResponse->sendContent();

        return ob_get_clean();
    }

    public function test_ログイン済みならCSVをダウンロードできる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/contacts/export');

        $response->assertOk();
        $this->assertStringContainsString(
            'contacts.csv',
            $response->headers->get('Content-Disposition')
        );
        $this->assertStringContainsString(
            'text/csv',
            $response->headers->get('Content-Type')
        );
    }

    public function test_CSVにBOMとヘッダーとデータが含まれる(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'CSVテストカテゴリ']);
        $this->createContact($category, '山田', 1);

        $response = $this->actingAs($user)->get('/contacts/export');
        $response->assertOk();

        $csv = $this->csvContent($response);

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString(
            'ID,氏名,性別,メール,電話,住所,建物,カテゴリ,内容,作成日時',
            $csv
        );
        $this->assertStringContainsString('山田 テスト', $csv);
        $this->assertStringContainsString('男性', $csv);
        $this->assertStringContainsString('CSVテストカテゴリ', $csv);
    }

    public function test_性別でCSVの出力対象を絞り込める(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'CSVテストカテゴリ']);

        $this->createContact($category, '男性対象', 1);
        $this->createContact($category, '女性対象', 2);

        $response = $this->actingAs($user)
            ->get('/contacts/export?gender=1');

        $response->assertOk();

        $csv = $this->csvContent($response);

        $this->assertStringContainsString('男性対象 テスト', $csv);
        $this->assertStringNotContainsString('女性対象 テスト', $csv);
    }

    public function test_未ログインならログイン画面へ移動する(): void
    {
        $this->get('/contacts/export')
            ->assertRedirect('/login');
    }
}
