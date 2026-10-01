<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test POST /pos dengan qty bernilai 0.
     */
    public function test_post_pos_fails_when_qty_is_zero(): void
    {
        $response = $this->postJson('/pos', [
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 0,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.qty']);
    }

    /**
     * Test POST /pos dengan product_id yang tidak ada.
     */
    public function test_post_pos_fails_when_product_id_does_not_exist(): void
    {
        $response = $this->postJson('/pos', [
            'items' => [
                [
                    'product_id' => 9999,
                    'qty' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);
    }
}
