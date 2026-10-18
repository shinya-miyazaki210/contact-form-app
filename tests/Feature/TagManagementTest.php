<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_タグを登録できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/admin/tags', [
                'name' => '新しいタグ',
            ]);

        $response->assertRedirect('/admin');

        $this->assertDatabaseHas('tags', [
            'name' => '新しいタグ',
        ]);
    }
    public function test_タグ編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $tag = \App\Models\Tag::create(["name" => "編集前のタグ"]);

        $response = $this->actingAs($user)
            ->get("/admin/tags/" . $tag->id . "/edit");

        $response->assertStatus(200);
        $response->assertViewIs("admin.tags.edit");
        $response->assertViewHas("tag", function ($viewTag) use ($tag) {
            return $viewTag->id === $tag->id;
        });
    }
    public function test_タグ名を更新できる(): void
    {
        $user = User::factory()->create();
        $tag = \App\Models\Tag::create(["name" => "更新前のタグ"]);

        $response = $this->actingAs($user)
            ->put("/admin/tags/" . $tag->id, [
                "name" => "更新後のタグ",
            ]);

        $response->assertRedirect("/admin");
        $this->assertDatabaseHas("tags", [
            "id" => $tag->id,
            "name" => "更新後のタグ",
        ]);
    }
    public function test_タグを削除できる(): void
    {
        $user = User::factory()->create();
        $tag = \App\Models\Tag::create(["name" => "削除するタグ"]);

        $response = $this->actingAs($user)
            ->delete("/admin/tags/" . $tag->id);

        $response->assertRedirect("/admin");
        $this->assertDatabaseMissing("tags", [
            "id" => $tag->id,
        ]);
    }
}
