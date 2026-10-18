<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_お問い合わせ入力画面が表示される(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('contact.index');
    }
}
