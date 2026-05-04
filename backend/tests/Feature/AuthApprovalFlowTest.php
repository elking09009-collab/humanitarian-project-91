<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_pending_account(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Pending User',
            'email' => 'pending@example.com',
            'password' => '123456',
            'role' => 'volunteer',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'pending@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_pending_user_cannot_login_before_approval(): void
    {
        User::factory()->create([
            'email' => 'pending-login@example.com',
            'password' => bcrypt('123456'),
            'role' => 'volunteer',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'pending-login@example.com',
            'password' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_rejected_user_receives_reason_in_login_response(): void
    {
        User::factory()->create([
            'email' => 'rejected@example.com',
            'password' => bcrypt('123456'),
            'role' => 'volunteer',
            'status' => 'rejected',
            'rejection_reason' => 'بيانات غير مكتملة',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'rejected@example.com',
            'password' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_approved_user_can_login_and_receive_token(): void
    {
        User::factory()->create([
            'email' => 'approved@example.com',
            'password' => bcrypt('123456'),
            'role' => 'volunteer',
            'status' => 'approved',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'approved@example.com',
            'password' => '123456',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user', 'token']);
    }
}
