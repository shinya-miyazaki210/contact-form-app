<?php

namespace Tests\Unit;

use App\Http\Requests\Api\V1\IndexContactRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ApiIndexContactRequestTest extends TestCase
{
    public function test_不正なキーワードは拒否される(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['keyword' => str_repeat('あ', 256)],
            $request->rules()
        );

        $this->assertTrue($validator->errors()->has('keyword'));
    }

    public function test_不正な性別は拒否される(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['gender' => '9'],
            $request->rules()
        );

        $this->assertTrue($validator->errors()->has('gender'));
    }

    public function test_存在しないカテゴリは拒否される(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['category_id' => 999999999],
            $request->rules()
        );

        $this->assertTrue($validator->errors()->has('category_id'));
    }

    public function test_不正な日付は拒否される(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['date' => '2026/10/18'],
            $request->rules()
        );

        $this->assertTrue($validator->errors()->has('date'));
    }

    public function test_不正なページ件数は拒否される(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            ['per_page' => 101],
            $request->rules()
        );

        $this->assertTrue($validator->errors()->has('per_page'));
    }

    public function test_正常な検索条件は通過する(): void
    {
        $request = new IndexContactRequest;

        $validator = Validator::make(
            [
                'keyword' => '田中',
                'gender' => '1',
                'date' => '2026-10-18',
                'per_page' => 20,
            ],
            $request->rules()
        );

        $this->assertFalse($validator->fails());
    }
}
