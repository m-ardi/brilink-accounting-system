<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rute root (/) harus mengarahkan user yang belum login ke halaman login.
     */
    public function test_root_redirects_unauthenticated_user_to_login(): void
    {
        $response = $this->get('/');

        // / → redirect ke /dashboard → redirect ke /login (karena belum auth)
        $response->assertRedirect();
    }

    /**
     * Rute root (/) harus mengarahkan user yang sudah login ke dashboard.
     */
    public function test_root_redirects_authenticated_user_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
