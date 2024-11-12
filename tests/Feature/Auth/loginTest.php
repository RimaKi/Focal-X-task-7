<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class loginTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic feature test example.
     */
    public function test_login_successfully(): void
    {
        $user = User::factory()->create()->load('role');

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'trans.method.POST.success', // حسب Middleware الخاص بك
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'national_id' => $user->national_id,
                        'role_id' => $user->role_id,
                        'role' => $user->role
                    ],
                    'authorisation' => [
                        'type' => 'bearer',
                    ],
                ],
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'name',
                        'email',
                        'national_id',
                        'role_id',
                        'role'
                    ],
                    'authorisation' => [
                        'token',
                        'type',
                    ],
                ],
            ]);
    }

    public function test_login_fails_with_non_existing_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'bla@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(402)
            ->assertJson([
                'success' => false,
                "message" => [
                    "email" => [
                        "The selected email is invalid."
                    ]
                ],
                'details' => []
            ]);
    }

    // خطأ في بيانات تسجيل الدخول (خطأ بكلمة السر)
    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized',
                'details' => []
            ]);
    }

    //التحقق من أن security headers تم إضافته بشكل صحيح بواسطة Middleware JsonResponse
    public function test_response_contains_security_headers()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertHeader('X-Frame-Options', 'deny')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Cross-Origin-Embedder-Policy', 'require-corp')
            ->assertHeader('Content-Security-Policy', "default-src 'none'; style-src 'self'; form-action 'self'")
            ->assertHeader('X-XSS-Protection', '1; mode=block');

        if (config('app.env') === 'production') {
            $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}
