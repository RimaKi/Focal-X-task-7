<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * إعداد البيانات المشتركة.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء دور Admin ودور User
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->executorRole = Role::create(['name' => 'task_executor']);

        // إنشاء مستخدم Admin للقيام بالاختبارات
        $this->adminUser = User::factory()->create([
            'role_id' => $this->adminRole->id,
        ]);

        // مستخدم بصلاحيات منفذ التاسكات
        $this->executorUser = User::factory()->create([
            'role_id' => $this->executorRole->id,
        ]);
    }

    public function test_admin_can_add_user_successfully()
    {
        // البيانات الجديدة للمستخدم
        $newUserData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'national_id' => '1234567890',
            'role_id' => $this->executorRole->id
        ];

        // إجراء الطلب بواسطة مستخدم Admin
        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/add-user', $newUserData);

        // تحقق من نجاح العملية واستجابة JSON
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'user' => [
                        "name" => $newUserData['name'],
                        "email" => $newUserData['email'],
                        "national_id" => $newUserData['national_id'],
                        "role_id" => $this->executorRole->id,
                        "id" => 3,
                        "role" => [
                            "id" => 2,
                            "name" => "task_executor",
                        ]
                    ],
                ],
            ]);

        // التحقق من إضافة المستخدم في قاعدة البيانات
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    //نقص بمعلومات اليوزر
    public function test_add_user_validation_errors()
    {
        // بيانات غير كاملة
        $incompleteData = [
            'name' => 'Incomplete User',
            // مفقود: email و national_id و role_id
        ];

        // إجراء الطلب بواسطة Admin
        $response = $this->actingAs($this->adminUser, 'api')
            ->postJson('/api/add-user', $incompleteData);

        // التحقق من الاستجابة وتفاصيل الخطأ
        $response->assertStatus(402)
            ->assertJson([
                "success" => false,
                "message" => [
                    "email" => [
                        "The email field is required."
                    ],
                    "national_id" => [
                        "The national id field is required."
                    ],
                    "role_id" => [
                        "The role id field is required."
                    ]
                ],
                "details" => []
            ]);
    }

    // رول خطأ
    public function test_non_admin_cannot_add_user()
    {
        // بيانات المستخدم الجديد
        $newUserData = [
            'name' => 'Unauthorized User',
            'email' => 'unauthorized@example.com',
            'national_id' => '1234567891',
            'role_id' => $this->executorRole->id
        ];

        // إجراء الطلب بواسطة مستخدم عادي
        $response = $this->actingAs($this->executorUser)->postJson('/api/add-user', $newUserData);

        // تحقق من فشل العملية
        $response->assertStatus(402)
            ->assertJson([
                'success' => false,
                'message' => "You don't have the right role."
            ]);
    }

}
