<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Simuler le stockage des images
//        Storage::fake('images');

        // Créer un admin
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        Profile::factory()->count(15)->create(['statut' => 1]);

        // Authentifier l'admin et obtenir un token
        $response = $this->postJson('/', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $this->headers = [
            'Authorization' => 'Bearer ' . $response->json('token'),
            'Accept' => 'application/json'
        ];
    }

    /**
     * Test getting active profiles (paginated).
     *
     * @return void
     */
    public function test_get_active_profiles()
    {
        $response = $this->getJson('/profiles');
        $response->assertStatus(200);
        // TODO count resultats
    }

    /**
     * Test creating a profile with valid data.
     *
     * @return void
     */
    public function test_create_profile()
    {
        // Envoyer une requête pour créer un profil avec le token
        $response = $this->withHeaders($this->headers)->post('/profile', [
            'nom' => 'Doe',
            'prenom' => 'John',
            'image' => UploadedFile::fake()->image('profile.jpg'),
            'statut' => 2
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test updating an existing profile.
     *
     * @return void
     */
    public function test_update_profile()
    {
        $response = $this->withHeaders($this->headers)->patch("/profile/1", [
            'nom' => 'Smith',
            'prenom' => 'Jane',
            'statut' => 2,
            'image' => UploadedFile::fake()->image('newprofile.jpg')
        ]);

        $response->assertStatus(200)
            ->assertJson(['nom' => 'Smith', 'prenom' => 'Jane', 'statut' => 2]);

//        Storage::disk('images')
//            ->assertExists($response->json('image'));
    }

    /**
     * Test deleting a non-existing profile.
     *
     * @return void
     */
    public function test_delete_non_existing_profile()
    {
        $response = $this->withHeaders($this->headers)->deleteJson('/profile/999');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Profil non trouvé.']);
    }

    /**
     * Test deleting a profile.
     *
     * @return void
     */
    public function test_delete_profile()
    {
        $response = $this->withHeaders($this->headers)->deleteJson("/profile/1");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Profil supprimé avec succès.']);

        $this->assertDatabaseMissing('profiles', ['id' => 1]);
    }
}
