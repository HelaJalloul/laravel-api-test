<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @var Admin $admin */
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);
    }

    /**
     * Test authentication with invalid credentials.
     *
     * @return void
     */
    public function test_authenticate_invalid_credentials()
    {
        $response = $this->postJson('/', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);
        $response->assertStatus(403);
        $responseContent = html_entity_decode($response->getContent());
        $this->assertStringContainsString("L'adresse mail ou le mot de passe est incorrect", $responseContent);
    }

    /**
     * Test authentication with missing fields.
     *
     * @return void
     */
    public function test_authenticate_missing_fields()
    {
        $response = $this->postJson('/', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(500)
            ->assertSee('Erreur pendant le login');
    }

    /**
     * Test successful authentication.
     *
     * @return void
     */
    public function test_authenticate_successful()
    {
        $response = $this->postJson('/', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'timestamp']);
    }
}
