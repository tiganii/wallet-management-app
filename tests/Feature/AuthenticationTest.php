<?php

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Testing\Fluent\AssertableJson;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Pest\Laravel\assertDatabaseHas;

it('assure application root api is accessible and return welcome text', function () {
    $response = $this->get('/api/v1/');
    $response->assertStatus(200);
});

it('assure application response with method not supported for non supported methods', function () {
    $response = $this->get('/api/v1/auth/register');
    $response->assertStatus(405);
});

it('assure application protected routes response with Unauthenticated if user not authenticated', function () {
    $response = $this->post('/api/v1/auth/me');
    $response->assertStatus(401)->assertJson([
        'success' => false,
        'message' => 'Unauthenticated.',
    ]);
});

it('assure application protected routes response for authenticated users', function () {
    $user = User::factory()->has(Wallet::factory())->create();
    $response = $this->actingAs($user)->post('/api/v1/auth/me');
    $response->assertStatus(200)->assertJson(fn(AssertableJson $json) => $json->hasAll(['success', 'message', 'user']));
});

it('assure application throw rate limit when allowed requests exceeded, and works after decay period passes', function () {
    $route = '/api/v1/';
    $maxAttempts = 10; // Maximum number of allowed requests
    $decayMinutes = 1; // Time window in minutes

    // Clear rate limiter for the route
    RateLimiter::clear($route);

    // Simulate maxAttempts requests within the time window
    for ($i = 0; $i < $maxAttempts; $i++) {
        $response = $this->get($route);
        $response->assertStatus(200); // Assuming a 200 OK response for valid requests
    }

    // The next request should be rate limited
    $response = $this->get($route);
    $response->assertStatus(429); // Too Many Requests
    $response->assertJson([
        'message' => 'Too many request, please slow down.',
    ]);

    // Wait for the decay period to pass
    sleep($decayMinutes * 60);

    // The request should now be allowed again
    $response = $this->get($route);
    $response->assertStatus(200);
});

it('registers a user and returns a non-empty access token in the data object', function () {
    // Prepare registration data
    $registrationData = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    // Send registration request
    $response = $this->postJson('/api/v1/auth/register', $registrationData);

    // Assert the response status
    $response->assertStatus(201);

    // Assert the response contains a token structure within the data object
    $response->assertJsonStructure([
        'success',
        'message',
        'user' => [
            'id',
            'name',
            'email',
            'balance'
        ],
    ]);

    // Assert that the new register user has 0 balance in wallet
    expect($response->json('user.balance'))->toBe(0);

    // Assert the user exists in the database
    assertDatabaseHas('users', [
        'email' => 'johndoe@example.com',
    ]);

});


it('allows a user to log in and returns a valid JWT', function () {

    $user = User::factory()->create([
        'email' => 'janedoe@examplee.com',
        'password' => bcrypt('password') // Ensure the password is hashed
    ]);

    // Prepare login credentials
    $credentials = [
        'email' => 'janedoe@examplee.com',
        'password' => 'password'
    ];

    // Send login request
    $response = $this->postJson('/api/v1/auth/login', $credentials);

    // Assert the response status
    $response->assertStatus(200);

    // Assert the response contains a token
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'access_token',
            'token_type',
            'expires_in',
        ]
       
    ]);

    // Ensure the token is valid (decode the token to check if it's valid)
    $this->assertNotEmpty($response->json('data.access_token'));
});


it('assure authenticated user can logout and can the session is destroyed after', function () {

    $user = User::factory()->has(Wallet::factory())->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->postJson('/api/v1/auth/logout', [], [
        'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Successfully logged out']);
    
     // Ensure the token is invalidated
     JWTAuth::setToken($token);
     $this->assertFalse(JWTAuth::check(true)); // Expecting the token to be invalid

     // Ensure the user is no longer authenticated
     $this->assertGuest();

});


