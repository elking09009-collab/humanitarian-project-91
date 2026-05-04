<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_submit_volunteer_application(): void
    {
        $response = $this->postJson('/api/volunteer-applications', [
            'volunteer_name' => 'متطوع تجريبي',
            'phone' => '01000000000',
            'city' => 'القاهرة',
            'age' => 27,
            'specialties' => 'طبي، تعليمي',
            'notes' => 'أستطيع التطوع مساءً.',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['message', 'id']);

        $this->assertDatabaseHas('volunteer_applications', [
            'volunteer_name' => 'متطوع تجريبي',
            'phone' => '01000000000',
            'city' => 'القاهرة',
            'status' => 'pending',
        ]);
    }

    public function test_volunteer_application_stats_include_total_counts(): void
    {
        $this->postJson('/api/volunteer-applications', [
            'volunteer_name' => 'متطوع 1',
            'phone' => '01000000001',
            'city' => 'الجيزة',
        ])->assertCreated();

        $this->postJson('/api/volunteer-applications', [
            'volunteer_name' => 'متطوع 2',
            'phone' => '01000000002',
            'city' => 'الإسكندرية',
        ])->assertCreated();

        $this->getJson('/api/volunteer-applications/stats')
            ->assertOk()
            ->assertJson([
                'total' => 2,
                'pending' => 2,
                'approved' => 0,
            ]);
    }
}