<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_student_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Student',
            'email' => 'student@fpne.edu.ng',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'matriculation_number' => 'FPNE/CS/2026/001',
            'department' => 'Computer Science',
            'cgpa' => 3.85,
            'skills' => 'PHP, Laravel, MySQL, HTML',
            'availability' => 'Full-Time',
            'portfolio_links' => 'https://github.com/johnstudent',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'student@fpne.edu.ng',
            'role' => 'student',
        ]);
        $this->assertDatabaseHas('student_profiles', [
            'matriculation_number' => 'FPNE/CS/2026/001',
        ]);
    }

    public function test_supervisor_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr. Supervisor',
            'email' => 'supervisor@techcorp.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'supervisor',
            'organization_name' => 'Tech Corp',
            'contact_person' => 'Jane Doe',
            'phone_number' => '+2348012345678',
            'company_profile' => 'Leading software innovations',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'supervisor@techcorp.com',
            'role' => 'supervisor',
        ]);
        $this->assertDatabaseHas('supervisor_profiles', [
            'organization_name' => 'Tech Corp',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect('/login');
        $this->assertGuest();
    }
}
