<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApiStoreContactRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_必須項目が未入力ならエラーになる(): void
    {
        $request = new StoreContactRequest;
        $validator = Validator::make([], $request->rules());

        foreach ([
            'first_name', 'last_name', 'gender', 'email',
            'tel', 'address', 'category_id', 'detail',
        ] as $field) {
            $this->assertTrue(
                $validator->errors()->has($field),
                "{$field} の必須エラーがありません"
            );
        }
    }

    public function test_不正なメール形式は拒否される(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['email' => 'invalid-email'],
            ['email' => $request->rules()['email']]
        );

        $this->assertTrue($validator->errors()->has('email'));
    }

    public function test_ハイフン付き電話番号は拒否される(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['tel' => '090-1234-5678'],
            ['tel' => $request->rules()['tel']]
        );

        $this->assertTrue($validator->errors()->has('tel'));
    }

    public function test_存在しないカテゴリは拒否される(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['category_id' => 999999999],
            ['category_id' => $request->rules()['category_id']]
        );

        $this->assertTrue($validator->errors()->has('category_id'));
    }

    public function test_存在しないタグは拒否される(): void
    {
        $request = new StoreContactRequest;

        $rules = $request->rules();

        $validator = Validator::make(
            ['tag_ids' => [999999999]],
            [
                'tag_ids' => $rules['tag_ids'],
                'tag_ids.*' => $rules['tag_ids.*'],
            ]
        );

        $this->assertTrue($validator->errors()->has('tag_ids.0'));
    }

    public function test_正常な入力は通過する(): void
    {
        $category = Category::create([
            'content' => 'APIテスト用カテゴリ',
        ]);

        $tag = Tag::create([
            'name' => 'APIテスト用タグ',
        ]);

        $request = new StoreContactRequest;

        $validator = Validator::make(
            [
                'first_name' => '山田',
                'last_name' => '太郎',
                'gender' => '1',
                'email' => 'api-test@example.com',
                'tel' => '09012345678',
                'address' => '東京都渋谷区',
                'building' => '',
                'category_id' => $category->id,
                'detail' => 'APIのテストです。',
                'tag_ids' => [$tag->id],
            ],
            $request->rules()
        );

        $this->assertFalse(
            $validator->fails(),
            $validator->errors()->first()
        );
    }
}
