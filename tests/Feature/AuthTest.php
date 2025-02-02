<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $connectionsToTransact = [];

    protected $passwordClient;
    protected $plainSecret;


    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
        $this->artisan('passport:keys', ['--force' => true]);
        $clientRepository = new ClientRepository();
        $this->passwordClient = $clientRepository->create(
            null,
            'Password Client',
            config('app.url'),
            null,
            false,
            true
        );
        $this->plainSecret = $this->passwordClient->plainSecret;
        $this->assertDatabaseHas('oauth_clients', [
            'id' => $this->passwordClient->id,
            'password_client' => 1
        ]);
    }


    #[Test]
    public function it_can_register_a_user_with_valid_credentials()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    #[Test]
    public function registration_fails_with_invalid_name_email_and_password()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => '',
            'email' => 'invalid',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password'
            ]);
    }

    #[Test]
    public function registration_fails_with_duplicate_email()
    {
        // Create existing user first
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function registration_fails_with_missing_required_fields()
    {
        $response = $this->postJson('/api/v1/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password'
            ]);
    }


    #[Test]
    public function it_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->plainSecret,
            'username' => $user->email,
            'password' => 'password',
            'scope' => '',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token'
            ]);
    }

    #[Test]
    public function login_fails_with_nonexistent_user()
    {
        $response = $this->postJson('/oauth/token', array_merge([
            'grant_type' => 'password',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->passwordClient->secret,
            'scope' => '',
        ], [
            'username' => 'nonexistent@example.com',
            'password' => 'password'
        ]));

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Client authentication failed'
            ]);
    }

    #[Test]
    public function login_fails_with_incorrect_password()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/oauth/token', array_merge([
            'grant_type' => 'password',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->passwordClient->secret,
            'scope' => '',
        ], [
            'username' => 'user@example.com',
            'password' => 'wrong-password'
        ]));

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Client authentication failed'
            ]);
    }

    #[Test]
    public function login_fails_with_invalid_client_secret()
    {
        $response = $this->postJson('/oauth/token', array_merge([
            'grant_type' => 'password',
            'client_id' => $this->passwordClient->id,
            'scope' => '',
        ], [
            'client_secret' => 'invalid-secret',
            'username' => 'user@example.com',
            'password' => 'password'
        ]));

        $response->assertStatus(401)
            ->assertJson(['message' => 'Client authentication failed']);
    }

    #[Test]
    public function login_fails_with_invalid_grant_type()
    {
        $response = $this->postJson('/oauth/token', array_merge([
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->passwordClient->secret,
            'scope' => '',
        ], [
            'grant_type' => 'invalid_grant',
            'username' => 'user@example.com',
            'password' => 'password'
        ]));

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'The authorization grant type is not supported by the authorization server.'
            ]);
    }

    #[Test]
    public function login_fails_with_missing_required_fields()
    {
        $response = $this->postJson('/oauth/token', [
            // Empty payload
        ]);

        $response->assertStatus(400)
            ->assertJson([
                "error" => "unsupported_grant_type"
            ]);
    }

    #[Test]
    public function it_can_refresh_access_token()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);

        $loginResponse = $this->postJson('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->plainSecret,
            'username' => 'user@example.com',
            'password' => 'password',
            'scope' => '',
        ]);

        $refreshToken = $loginResponse->json('refresh_token');

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->plainSecret,
            'refresh_token' => $refreshToken,
            'scope' => '',
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token'
            ]);
    }

    #[Test]
    public function refresh_token_fails_with_invalid_refresh_token()
    {
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->plainSecret,
            'refresh_token' => 'invalid-token',
            'scope' => '',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'The refresh token is invalid.'
            ]);
    }

    #[Test]
    public function refresh_token_fails_with_invalid_client_secret()
    {
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->passwordClient->id,
            'client_secret' => 'invalid-secret',
            'refresh_token' => 'dummy-token',
            'scope' => '',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Client authentication failed'
            ]);
    }

    #[Test]
    public function refresh_token_fails_with_invalid_grant_type()
    {
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'invalid_grant',
            'client_id' => $this->passwordClient->id,
            'client_secret' => $this->plainSecret,
            'refresh_token' => 'dummy-token',
            'scope' => '',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'The authorization grant type is not supported by the authorization server.'
            ]);
    }

    #[Test]
    public function refresh_token_fails_with_missing_required_fields()
    {
        $response = $this->postJson('/oauth/token', [
            // Intentionally empty payload
        ]);

        $response->assertStatus(400)
            ->assertJson([
                "error" => "unsupported_grant_type"
            ]);
    }


    #[Test]
    public function it_fails_to_logout_when_unauthenticated()
    {
        $response = $this->postJson('/api/v1/logout');
        $response->assertStatus(500)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }


    #[Test]
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $routes = [
            ['post', '/api/v1/logout']
        ];

        foreach ($routes as $route) {
            $response = $this->{$route[0] . 'Json'}($route[1]);
            $response->assertStatus(500);
        }
    }
}
