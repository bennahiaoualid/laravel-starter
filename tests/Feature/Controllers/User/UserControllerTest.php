<?php

namespace Tests\Feature\Controllers\User;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
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

        $otherUser = User::factory()->create();
        $data = $this->createTestData();

        // Test all routes that require authentication
        $indexResponse = $this->get(route('index'));
        $listResponse = $this->get(route('settings.users.index'));
        $storeResponse = $this->post(route('settings.users.store'), $data);
        $editResponse = $this->get(route('settings.users.edit', $otherUser));
        $updateResponse = $this->patch(route('settings.users.update', $otherUser), $data);
        $toggleStatusResponse = $this->post(route('settings.users.toggle-status'), ['id' => $otherUser->id]);

        // Assert all redirect to login
        $indexResponse->assertRedirect(route('login'));
        $listResponse->assertRedirect(route('login'));
        $storeResponse->assertRedirect(route('login'));
        $editResponse->assertRedirect(route('login'));
        $updateResponse->assertRedirect(route('login'));
        $toggleStatusResponse->assertRedirect(route('login'));
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

        // Give all permissions (user is authenticated but inactive)
        $inactiveUser->givePermissionTo('view user');
        $inactiveUser->givePermissionTo('add user');
        $inactiveUser->givePermissionTo('update user');
        $inactiveUser->givePermissionTo('delete user');

        $otherUser = User::factory()->create();
        $data = $this->createTestData();

        // Test all routes that require active user
        $indexResponse = $this->get(route('index'));
        $listResponse = $this->get(route('settings.users.index'));
        $storeResponse = $this->post(route('settings.users.store'), $data);
        $editResponse = $this->get(route('settings.users.edit', $otherUser));
        $updateResponse = $this->patch(route('settings.users.update', $otherUser), $data);
        $toggleStatusResponse = $this->post(route('settings.users.toggle-status'), ['id' => $otherUser->id]);

        // Assert all redirect to login with error (active middleware redirects inactive users)
        $indexResponse->assertRedirect(route('login'));
        $listResponse->assertRedirect(route('login'));
        $storeResponse->assertRedirect(route('login'));
        $editResponse->assertRedirect(route('login'));
        $updateResponse->assertRedirect(route('login'));
        $toggleStatusResponse->assertRedirect(route('login'));
    }

    // ==================== PERMISSION TESTS ====================

    /**
     * ⚠️ Permission Testing Rule: One scenario per method
     * User is authenticated and active, but NO permission
     */
    public function test_list_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('view user'); // Skip this

        $response = $this->get(route('settings.users.index'));

        $response->assertStatus(403);
    }

    public function test_store_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('add user'); // Skip this

        $data = $this->createTestData();
        $response = $this->post(route('settings.users.store'), $data);

        $response->assertStatus(403);
    }

    public function test_edit_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('update user'); // Skip this

        $otherUser = User::factory()->create();
        $response = $this->get(route('settings.users.edit', $otherUser));

        $response->assertStatus(403);
    }

    public function test_update_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('update user'); // Skip this

        $otherUser = User::factory()->create();
        $data = $this->createTestData();
        $response = $this->patch(route('settings.users.update', $otherUser), $data);

        $response->assertStatus(403);
    }

    public function test_toggle_status_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('delete user'); // Skip this

        $otherUser = User::factory()->create();
        $response = $this->post(route('settings.users.toggle-status'), ['id' => $otherUser->id]);

        $response->assertStatus(403);
    }

    // ==================== VALIDATION TESTS ====================

    /**
     * ⚠️ CRITICAL RULE: ONE comprehensive validation test per method
     * Violate ALL validation rules at once
     */
    public function test_store_fails_with_invalid_data()
    {
        $this->user->givePermissionTo('add user');

        // Get a valid role ID for testing
        $role = \Spatie\Permission\Models\Role::whereNotIn('name', ['owner'])->first();
        if (! $role) {
            $role = \Spatie\Permission\Models\Role::create(['name' => 'test_role', 'guard_name' => 'web']);
        }

        // Create data that violates ALL validation rules
        // name: required|string|min:3|max:60 -> missing (required violation) or send 70 characters (max violation) or 2 characters (min violation)
        // email: required|string|lowercase|email|max:255|unique:users,email -> missing (required violation) or invalid email or duplicate
        // password: required|min:8 -> missing (required violation) or 7 characters (min violation)
        // role: required|in:... -> missing (required violation) or invalid role ID

        $existingUser = User::factory()->create();

        $data = [
            // 'name' => missing (required violation)
            'email' => 'INVALID_EMAIL', // email violation (not lowercase, not valid email)
            'password' => 'short', // min:8 violation
            'role' => 99999, // in:... violation (invalid role ID)
        ];

        $response = $this->post(route('settings.users.store'), $data);

        // ⚠️ IMPORTANT: Always use errorBag when FormRequest is named
        // FormRequest: StoreUserRequest -> errorBag: 'createUser'
        $response->assertSessionHasErrors([
            'name', // required
            'email', // email, lowercase
            'password', // min:8
            'role', // in:...
        ], errorBag: 'createUser');

        // Assert nothing was created
        $this->assertDatabaseMissing('users', ['email' => 'INVALID_EMAIL']);
    }

    /**
     * ⚠️ CRITICAL RULE: ONE comprehensive validation test per method
     * Violate ALL validation rules at once
     */
    public function test_update_fails_with_invalid_data()
    {
        $this->user->givePermissionTo('update user');
        $otherUser = User::factory()->create();
        $originalName = $otherUser->name;

        // Get a valid role ID for testing
        $role = \Spatie\Permission\Models\Role::whereNotIn('name', ['owner'])->first();
        if (! $role) {
            $role = \Spatie\Permission\Models\Role::create(['name' => 'test_role', 'guard_name' => 'web']);
        }

        // Create data that violates ALL validation rules
        // name: required|string|min:3|max:60 -> missing (required violation) or send 70 characters (max violation) or 2 characters (min violation)
        // email: required|string|lowercase|email|max:255|unique:users,email,{id} -> missing (required violation) or invalid email or duplicate
        // role: required|in:... -> missing (required violation) or invalid role ID

        $existingUser = User::factory()->create();

        $data = [
            // 'name' => missing (required violation)
            'email' => 'INVALID_EMAIL', // email violation (not lowercase, not valid email)
            'role' => 99999, // in:... violation (invalid role ID)
        ];

        $response = $this->patch(route('settings.users.update', $otherUser), $data);

        // ⚠️ IMPORTANT: Always use errorBag when FormRequest is named
        // FormRequest: UpdateUserRequest -> errorBag: 'updateUser'
        $response->assertSessionHasErrors([
            'name', // required
            'email', // email, lowercase
            'role', // in:...
        ], errorBag: 'updateUser');

        // Assert user was not updated
        $otherUser->refresh();
        $this->assertEquals($originalName, $otherUser->name);
    }

    // ==================== SUCCESS SCENARIOS ====================

    public function test_index_success()
    {
        // User is authenticated, active, and has permission (index doesn't require permission)
        $response = $this->get(route('index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user.dashboard');
    }

    public function test_list_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('view user');

        $response = $this->get(route('settings.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user.list');
    }

    public function test_store_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('add user');

        // Get a valid role ID for testing
        $role = Role::whereNotIn('name', ['owner'])->first();
        if (! $role) {
            $role = Role::create(['name' => 'test_role', 'guard_name' => 'web']);
        }

        $data = $this->createTestData([
            'role' => $role->id,
        ]);

        $response = $this->post(route('settings.users.store'), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Assert role was assigned
        $createdUser = User::where('email', $data['email'])->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->hasRole($role->name));
    }

    public function test_edit_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('update user');
        $otherUser = User::factory()->create();

        $response = $this->get(route('settings.users.edit', $otherUser));

        $response->assertStatus(200);
        $response->assertViewIs('pages.user.edit-user');
        $response->assertViewHas('user', $otherUser);
    }

    public function test_edit_fails_when_editing_self()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('update user');

        // Try to edit self (should return 404)
        $response = $this->get(route('settings.users.edit', $this->user));

        $response->assertStatus(404);
    }

    public function test_update_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('update user');
        $otherUser = User::factory()->create();

        // Get a valid role ID for testing
        $role = \Spatie\Permission\Models\Role::whereNotIn('name', ['owner'])->first();
        if (! $role) {
            $role = \Spatie\Permission\Models\Role::create(['name' => 'test_role', 'guard_name' => 'web']);
        }

        $data = $this->createTestData([
            'role' => $role->id,
        ]);
        // Remove password from update data
        unset($data['password']);

        $response = $this->patch(route('settings.users.update', $otherUser), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database
        $otherUser->refresh();
        $this->assertEquals($data['name'], $otherUser->name);
        $this->assertEquals($data['email'], $otherUser->email);

        // Assert role was updated
        $this->assertTrue($otherUser->hasRole($role->name));
    }

    public function test_toggle_status_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('delete user');
        $otherUser = User::factory()->create(['is_active' => true]);
        $originalStatus = $otherUser->is_active;

        $response = $this->post(route('settings.users.toggle-status'), ['id' => $otherUser->id]);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database - status should be toggled
        $otherUser->refresh();
        $this->assertNotEquals($originalStatus, $otherUser->is_active);
    }

    public function test_toggle_status_fails_when_toggling_self()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('delete user');
        $originalStatus = $this->user->is_active;

        $response = $this->post(route('settings.users.toggle-status'), ['id' => $this->user->id]);

        // Assert HTTP Response - should redirect back with error
        $response->assertRedirectBack();

        // Assert Database - status should NOT be changed
        $this->user->refresh();
        $this->assertEquals($originalStatus, $this->user->is_active);

        // Assert error message
        $this->assertStringContainsString(
            'You cannot deactivate yourself',
            session()->get('messages')[0]['message']
        );
    }

    // ==================== HELPER METHODS ====================

    /**
     * Create test data for requests
     *
     * @param  array  $override  Override default values
     */
    private function createTestData(array $override = []): array
    {
        return array_merge([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'Password123@',
            'role' => 1, // Will be overridden in tests
        ], $override);
    }
}
