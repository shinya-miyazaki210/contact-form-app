<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_タグ名が未入力ならエラーになる(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make(
            ['name' => ''],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_タグ名が50文字を超えるとエラーになる(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make(
            ['name' => str_repeat('あ', 51)],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_登録済みのタグ名はエラーになる(): void
    {
        \App\Models\Tag::create(['name' => 'テストタグ']);

        $request = new StoreTagRequest();

        $validator = Validator::make(
            ['name' => 'テストタグ'],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_正常なタグ名はバリデーションを通過する(): void
    {
        $request = new StoreTagRequest();

        $validator = Validator::make(
            ['name' => '新しいテストタグ'],
            $request->rules()
        );

        $this->assertFalse($validator->fails(), $validator->errors()->first());
    }
}
