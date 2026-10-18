<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use DatabaseTransactions;
    public function test_未ログインなら管理画面にアクセスできない(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
    public function test_ログイン済みなら管理画面にアクセスできる(): void
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get("/admin");

        $response->assertStatus(200);
    }
}
