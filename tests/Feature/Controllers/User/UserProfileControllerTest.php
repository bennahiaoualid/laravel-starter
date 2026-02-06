<?php

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var User */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Set application locale
        $this->refreshApplicationWithLocale('en');

        // Create test data that will be used across multiple tests
        $this->user = User::factory()->create();

        // Seed roles if needed
        $this->seed(RoleSeeder::class);

        // Authenticate as user for tests that require authentication
        $this->actingAs($this->user, 'web');

        // Fake external services
        Bus::fake();
        Mail::fake();
        Notification::fake();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // ==================== AUTHENTICATION & AUTHORIZATION TESTS ====================

    /**
     * ⚠️ CRITICAL RULE: Test ALL routes in ONE method for authentication
     */
    public function test_all_routes_fail_without_authentication()
    {
        // Don't authenticate
        Auth::logout();

        // Test all routes that require authentication
        $editResponse = $this->get(route('profile.edit'));
        $destroyResponse = $this->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        // Assert all redirect to login
        $editResponse->assertRedirect(route('login'));
        $destroyResponse->assertRedirect(route('login'));
    }

    /**
     * ⚠️ CRITICAL RULE: Test ALL routes in ONE method for inactive user
     * Inactive user = user is authenticated but is_active = false
     */
    public function test_all_routes_fail_with_inactive_user()
    {
        // Create inactive user (authenticated but is_active = false)
        $inactiveUser = User::factory()->create(['is_active' => false]);
        $this->actingAs($inactiveUser, 'web');

        // Test all routes that require active user
        $editResponse = $this->get(route('profile.edit'));
        $destroyResponse = $this->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        // Assert all redirect to login with error (active middleware redirects inactive users)
        $editResponse->assertRedirect(route('login'));
        $destroyResponse->assertRedirect(route('login'));
    }

    // ==================== SUCCESS SCENARIOS ====================

    public function test_edit_success()
    {
        // User is authenticated and active (profile routes don't require permissions)
        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user.profile.edit');
        $response->assertViewHas('user', $this->user);
    }

    public function test_edit_success_with_two_factor_secret()
    {
        // User is authenticated and active with two factor secret
        $this->user->update([
            'two_factor_secret' => encrypt('test_secret_key'),
        ]);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user.profile.edit');
        $response->assertViewHas('user', $this->user);
        $response->assertViewHas('twoFactorSecret');
        $response->assertViewHas('twoFactorQrCode');
    }

    public function test_destroy_success()
    {
        // User is authenticated and active
        $userPassword = 'password123';
        $this->user->update([
            'password' => Hash::make($userPassword),
        ]);

        $userId = $this->user->id;

        $response = $this->delete(route('profile.destroy'), [
            'password' => $userPassword,
        ]);

        // Assert HTTP Response - should redirect to home
        $response->assertRedirect('/');

        // Assert Database - user should be deleted
        $this->assertDatabaseMissing('users', ['id' => $userId]);

        // Assert user is logged out
        $this->assertGuest('web');
    }

    // ==================== VALIDATION TESTS ====================

    /**
     * ⚠️ CRITICAL RULE: ONE comprehensive validation test per method
     * Violate ALL validation rules at once
     */
    public function test_destroy_fails_with_invalid_data()
    {
        // User is authenticated and active
        $userPassword = 'password123';
        $this->user->update([
            'password' => Hash::make($userPassword),
        ]);

        // Create data that violates ALL validation rules
        // password: required|current_password -> missing (required violation) or wrong password

        $data = [
            // 'password' => missing (required violation)
        ];

        $response = $this->delete(route('profile.destroy'), $data);

        // ⚠️ IMPORTANT: Always use errorBag when FormRequest is named
        // FormRequest uses errorBag: 'userDeletion'
        $response->assertSessionHasErrors([
            'password', // required
        ], errorBag: 'userDeletion');

        // Assert user was NOT deleted
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    public function test_destroy_fails_with_wrong_password()
    {
        // User is authenticated and active
        $userPassword = 'password123';
        $this->user->update([
            'password' => Hash::make($userPassword),
        ]);

        $response = $this->delete(route('profile.destroy'), [
            'password' => 'wrong_password',
        ]);

        // Assert validation error
        $response->assertSessionHasErrors([
            'password', // current_password validation
        ], errorBag: 'userDeletion');

        // Assert user was NOT deleted
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }
}
