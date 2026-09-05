<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Internship;
use App\Models\Milestone;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternshipWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $supervisor;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->supervisor = User::factory()->create([
            'role' => 'supervisor',
        ]);
        $this->supervisor->supervisorProfile()->create([
            'organization_name' => 'Nekede Tech Hub',
            'contact_person' => 'Director Okoro',
            'phone_number' => '08099998888',
        ]);

        $this->student = User::factory()->create([
            'role' => 'student',
        ]);
        $this->student->studentProfile()->create([
            'matriculation_number' => 'FPNE/ND/2026/102',
            'department' => 'Computer Science',
            'cgpa' => 3.75,
            'skills' => ['PHP', 'Laravel', 'MySQL'],
        ]);
    }

    public function test_supervisor_can_create_internship(): void
    {
        $response = $this->actingAs($this->supervisor)->post('/internships', [
            'title' => 'Laravel Web Developer',
            'description' => 'Build micro-internship features',
            'required_skills' => 'PHP, Laravel, MySQL',
            'location_type' => 'remote',
            'duration_days' => 14,
            'stipend' => 50000,
            'max_applicants' => 5,
        ]);

        $response->assertRedirect('/internships');
        $this->assertDatabaseHas('internships', [
            'title' => 'Laravel Web Developer',
            'supervisor_id' => $this->supervisor->id,
            'status' => 'draft',
        ]);
    }

    public function test_student_can_apply_to_published_internship(): void
    {
        $internship = Internship::create([
            'supervisor_id' => $this->supervisor->id,
            'title' => 'Frontend Developer',
            'description' => 'Develop Blade views',
            'required_skills' => ['HTML', 'CSS', 'Bootstrap'],
            'location_type' => 'remote',
            'duration_days' => 10,
            'stipend' => 30000,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->student)->post("/internships/{$internship->id}/apply", [
            'cover_note' => 'I am very excited to work on this project!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'internship_id' => $internship->id,
            'student_id' => $this->student->id,
            'status' => 'applied',
        ]);
    }

    public function test_supervisor_can_accept_application(): void
    {
        $internship = Internship::create([
            'supervisor_id' => $this->supervisor->id,
            'title' => 'Fullstack Developer',
            'description' => 'Build end-to-end features',
            'required_skills' => ['Laravel'],
            'location_type' => 'hybrid',
            'duration_days' => 20,
            'stipend' => 75000,
            'status' => 'published',
        ]);

        $application = Application::create([
            'internship_id' => $internship->id,
            'student_id' => $this->student->id,
            'cover_note' => 'Ready to start immediately.',
            'status' => 'applied',
        ]);

        $response = $this->actingAs($this->supervisor)->patch("/applications/{$application->id}/status", [
            'status' => 'accepted',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'accepted',
        ]);
    }
}
