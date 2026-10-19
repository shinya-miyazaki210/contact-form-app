<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_必須項目が未入力ならエラーになる(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());

        foreach ([
            'first_name',
            'last_name',
            'gender',
            'email',
            'tel',
            'address',
            'category_id',
            'detail',
        ] as $field) {
            $this->assertTrue(
                $validator->errors()->has($field),
                "{$field} の必須エラーがありません"
            );
        }
    }

    public function test_不正なメール形式はバリデーションエラーになる(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['email' => 'invalid-email'],
            ['email' => $request->rules()['email']]
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('email'));
    }

    public function test_不正な電話番号はバリデーションエラーになる(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['tel' => '090-1234-5678'],
            ['tel' => $request->rules()['tel']]
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel'));
    }

    public function test_お問い合わせ内容が120文字を超えるとエラーになる(): void
    {
        $request = new StoreContactRequest;

        $validator = Validator::make(
            ['detail' => str_repeat('あ', 121)],
            ['detail' => $request->rules()['detail']]
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('detail'));
    }

    public function test_正常なお問い合わせ入力はバリデーションを通過する(): void
    {
        $category = Category::create([
            'content' => 'テスト用カテゴリ',
        ]);

        $request = new StoreContactRequest;

        $validator = Validator::make(
            [
                'first_name' => '山田',
                'last_name' => '太郎',
                'gender' => '1',
                'email' => 'test@example.com',
                'tel' => '09012345678',
                'address' => '東京都渋谷区',
                'building' => '',
                'category_id' => $category->id,
                'detail' => 'お問い合わせのテストです。',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails(), $validator->errors()->first());
    }
}
