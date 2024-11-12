<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم وهمي للتجربة
        $this->user = User::factory()->create([
            'password' => Hash::make('OldPassword123!'),
            'role_id' => Role::create(['name' => 'task_executor'])->id
        ]);

//         تسجيل الدخول للمستخدم لاستخدام التوكين
//        $this->actingAs($this->user, 'api');
    }

    /** @test */
//    public function test_changes_password_successfully()
//    {
//        $response = $this->postJson('/api/change-password', [
//            'old_password' => 'OldPassword123!',
//            'password' => 'NewPassword123@',
//            'password_confirmation' => 'NewPassword123@',
//        ]);
//
//        $response->assertStatus(200)
//            ->assertJson([
//                'success' => true,
//                'message' => 'Done'
//            ]);
//
//        // التحقق من أن كلمة المرور قد تم تحديثها
//        $this->assertTrue(Hash::check('NewPassword123@', $this->user->fresh()->password));
//    }


    public function test_refresh_token_success()
    {
        $token = Auth::login($this->user);

        $response = $this->actingAs($this->user, 'api')->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['user', 'authorisation' => ['token', 'type']],
                'message'
            ])
            ->assertJson(['success' => true]);
    }

    public function test_refresh_token_failure()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid_token' ])
            ->postJson('/api/refresh');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'trans.unauthenticated'
            ]);
    }

    public function test_logout_success()
    {
        $token = Auth::login($this->user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully logged out',
            ]);
    }

}
