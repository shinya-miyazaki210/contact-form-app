<?php

namespace Tests\Unit;

use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTagRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_タグ名が未入力ならエラーになる(): void
    {
        $request = new UpdateTagRequest();

        $validator = Validator::make(
            ['name' => ''],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_タグ名が50文字を超えるとエラーになる(): void
    {
        $request = new UpdateTagRequest();

        $validator = Validator::make(
            ['name' => str_repeat('あ', 51)],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_ほかのタグと名前が重複するとエラーになる(): void
    {
        $tagA = Tag::create(['name' => 'タグA']);
        Tag::create(['name' => 'タグB']);

        $request = new UpdateTagRequest();
        $request->setRouteResolver(function () use ($tagA) {
            $route = new \Illuminate\Routing\Route('PUT', '/admin/tags/{tag}', []);
            $route->bind($request = \Illuminate\Http\Request::create('/admin/tags/' . $tagA->id, 'PUT'));
            $route->setParameter('tag', $tagA);

            return $route;
        });

        $validator = Validator::make(
            ['name' => 'タグB'],
            $request->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_自分自身のタグ名なら更新できる(): void
    {
        $tag = Tag::create(['name' => '既存タグ']);

        $request = new UpdateTagRequest();
        $request->setRouteResolver(function () use ($tag) {
            $route = new \Illuminate\Routing\Route('PUT', '/admin/tags/{tag}', []);
            $route->bind(\Illuminate\Http\Request::create('/admin/tags/' . $tag->id, 'PUT'));
            $route->setParameter('tag', $tag);

            return $route;
        });

        $validator = Validator::make(
            ['name' => '既存タグ'],
            $request->rules()
        );

        $this->assertFalse($validator->fails(), $validator->errors()->first());
    }

    public function test_新しいタグ名なら更新できる(): void
    {
        $tag = Tag::create(['name' => '変更前タグ']);

        $request = new UpdateTagRequest();
        $request->setRouteResolver(function () use ($tag) {
            $route = new \Illuminate\Routing\Route('PUT', '/admin/tags/{tag}', []);
            $route->bind(\Illuminate\Http\Request::create('/admin/tags/' . $tag->id, 'PUT'));
            $route->setParameter('tag', $tag);

            return $route;
        });

        $validator = Validator::make(
            ['name' => '変更後タグ'],
            $request->rules()
        );

        $this->assertFalse($validator->fails(), $validator->errors()->first());
    }
}
