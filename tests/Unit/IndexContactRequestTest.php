<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    public function test_不正な性別はバリデーションエラーになる(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['gender' => '9'],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('gender'));
    }

    public function test_存在しないカテゴリ_i_dはバリデーションエラーになる(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['category_id' => 999999999],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('category_id'));
    }

    public function test_不正な日付形式はバリデーションエラーになる(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['date' => '2026/10/15'],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('date'));
    }

    public function test_正常な検索条件はバリデーションを通過する(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            [
                'keyword' => '田中',
                'gender' => '1',
                'date' => '2026-10-15',
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }
}
