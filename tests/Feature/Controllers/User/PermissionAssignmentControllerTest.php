<?php

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionAssignmentControllerTest extends TestCase
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

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permission from seeder
        $permission = Permission::where('name', 'view user')->first();

        // Test all routes that require authentication
        $indexResponse = $this->get(route('settings.permissions.index'));
        $showRoleResponse = $this->get(route('settings.permissions.role', $role));
        $assignResponse = $this->post(route('settings.permissions.assign', $role), ['permission_ids' => [$permission->id]]);
        $revokeResponse = $this->post(route('settings.permissions.revoke', $role), ['permission_ids' => [$permission->id]]);

        // Assert all redirect to login
        $indexResponse->assertRedirect(route('login'));
        $showRoleResponse->assertRedirect(route('login'));
        $assignResponse->assertRedirect(route('login'));
        $revokeResponse->assertRedirect(route('login'));
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
        $inactiveUser->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permission from seeder
        $permission = Permission::where('name', 'view user')->first();

        // Test all routes that require active user
        $indexResponse = $this->get(route('settings.permissions.index'));
        $showRoleResponse = $this->get(route('settings.permissions.role', $role));
        $assignResponse = $this->post(route('settings.permissions.assign', $role), ['permission_ids' => [$permission->id]]);
        $revokeResponse = $this->post(route('settings.permissions.revoke', $role), ['permission_ids' => [$permission->id]]);

        // Assert all redirect to login with error (active middleware redirects inactive users)
        $indexResponse->assertRedirect(route('login'));
        $showRoleResponse->assertRedirect(route('login'));
        $assignResponse->assertRedirect(route('login'));
        $revokeResponse->assertRedirect(route('login'));
    }

    // ==================== PERMISSION TESTS ====================

    /**
     * ⚠️ Permission Testing Rule: One scenario per method
     * User is authenticated and active, but NO permission
     */
    public function test_index_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('manage roles'); // Skip this

        $response = $this->get(route('settings.permissions.index'));

        $response->assertStatus(403);
    }

    public function test_show_role_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('manage roles'); // Skip this

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        $response = $this->get(route('settings.permissions.role', $role));

        $response->assertStatus(403);
    }

    public function test_assign_permissions_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('manage roles'); // Skip this

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permission from seeder
        $permission = Permission::where('name', 'view user')->first();
        $data = ['permission_ids' => [$permission->id]];

        $response = $this->post(route('settings.permissions.assign', $role), $data);

        $response->assertStatus(403);
    }

    public function test_revoke_permissions_fails_without_permission()
    {
        // User is authenticated and active, but NO permission
        // $this->user->givePermissionTo('manage roles'); // Skip this

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permission from seeder
        $permission = Permission::where('name', 'view user')->first();
        $data = ['permission_ids' => [$permission->id]];

        $response = $this->post(route('settings.permissions.revoke', $role), $data);

        $response->assertStatus(403);
    }

    // ==================== VALIDATION TESTS ====================

    /**
     * ⚠️ CRITICAL RULE: ONE comprehensive validation test per method
     * Note: assignPermissions uses plain Request, not FormRequest, so validation is handled in service
     * Service throws InvalidArgumentException for empty permission_ids
     */
    public function test_assign_permissions_fails_with_empty_permission_ids()
    {
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        // Empty permission_ids array
        $data = ['permission_ids' => []];

        $response = $this->post(route('settings.permissions.assign', $role), $data);

        // Service throws exception, controller catches and flashes error
        $response->assertRedirectBack();
        $this->assertStringContainsString(
            trans('messages.permissions.assignment_failed'),
            session()->get('messages')[0]['message']
        );
    }

    public function test_assign_permissions_fails_with_invalid_permission_ids()
    {
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        // Invalid permission IDs (non-existent)
        $data = ['permission_ids' => [99999, 99998]];

        $response = $this->post(route('settings.permissions.assign', $role), $data);

        // Service throws exception, controller catches and flashes error
        $response->assertRedirectBack();
        $this->assertStringContainsString(
            trans('messages.permissions.assignment_failed'),
            session()->get('messages')[0]['message']
        );
    }

    public function test_revoke_permissions_fails_with_empty_permission_ids()
    {
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);

        // Empty permission_ids array
        $data = ['permission_ids' => []];

        $response = $this->post(route('settings.permissions.revoke', $role), $data);

        // Service throws exception, controller catches and flashes error
        $response->assertRedirectBack();
        $this->assertStringContainsString(
            trans('messages.permissions.revocation_failed'),
            session()->get('messages')[0]['message']
        );
    }

    // ==================== SUCCESS SCENARIOS ====================

    public function test_index_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $response = $this->get(route('settings.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.permissions.index');
        $response->assertViewHas('roles');
        $response->assertViewHas('permissions');
    }

    public function test_show_role_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permission from seeder
        $permission = Permission::where('name', 'view user')->first();
        $role->givePermissionTo($permission);

        $response = $this->get(route('settings.permissions.role', $role));

        $response->assertStatus(200);
        $response->assertViewIs('pages.permissions.role-permissions');
        $response->assertViewHas('role');
        $response->assertViewHas('allPermissions');
        $response->assertViewHas('rolePermissions');
    }

    public function test_assign_permissions_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permissions from seeder
        $permission1 = Permission::where('name', 'view user')->first();
        $permission2 = Permission::where('name', 'add user')->first();

        $data = ['permission_ids' => [$permission1->id, $permission2->id]];

        $response = $this->post(route('settings.permissions.assign', $role), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database - Check that permissions were assigned
        $this->assertTrue($role->fresh()->hasPermissionTo($permission1));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission2));

        // Assert Flash Message
        $this->assertStringContainsString(
            trans('messages.permissions.assigned_successfully'),
            session()->get('messages')[0]['message']
        );
    }

    public function test_assign_permissions_syncs_existing_permissions()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permissions from seeder
        $permission1 = Permission::where('name', 'view user')->first();
        $permission2 = Permission::where('name', 'add user')->first();
        $permission3 = Permission::where('name', 'update user')->first();

        // Initially assign permission1 and permission2
        $role->givePermissionTo([$permission1, $permission2]);

        // Now sync with permission2 and permission3 (should replace, not add)
        $data = ['permission_ids' => [$permission2->id, $permission3->id]];

        $response = $this->post(route('settings.permissions.assign', $role), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database - Check that permissions were synced (permission1 removed, permission2 and permission3 assigned)
        $this->assertFalse($role->fresh()->hasPermissionTo($permission1));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission2));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission3));
    }

    public function test_revoke_permissions_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permissions from seeder
        $permission1 = Permission::where('name', 'view user')->first();
        $permission2 = Permission::where('name', 'add user')->first();

        // Initially assign permissions
        $role->givePermissionTo([$permission1, $permission2]);

        // Revoke permission1
        $data = ['permission_ids' => [$permission1->id]];

        $response = $this->post(route('settings.permissions.revoke', $role), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database - Check that permission1 was revoked but permission2 remains
        $this->assertFalse($role->fresh()->hasPermissionTo($permission1));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission2));

        // Assert Flash Message
        $this->assertStringContainsString(
            trans('messages.permissions.revoked_successfully'),
            session()->get('messages')[0]['message']
        );
    }

    public function test_revoke_multiple_permissions_success()
    {
        // User is authenticated, active, and has permission
        $this->user->givePermissionTo('manage roles');

        $role = Role::create(['name' => 'test-role', 'guard_name' => 'web']);
        // Use existing permissions from seeder
        $permission1 = Permission::where('name', 'view user')->first();
        $permission2 = Permission::where('name', 'add user')->first();
        $permission3 = Permission::where('name', 'update user')->first();

        // Initially assign all permissions
        $role->givePermissionTo([$permission1, $permission2, $permission3]);

        // Revoke permission1 and permission2
        $data = ['permission_ids' => [$permission1->id, $permission2->id]];

        $response = $this->post(route('settings.permissions.revoke', $role), $data);

        // Assert HTTP Response
        $response->assertRedirectBack();

        // Assert Database - Check that permissions were revoked
        $this->assertFalse($role->fresh()->hasPermissionTo($permission1));
        $this->assertFalse($role->fresh()->hasPermissionTo($permission2));
        $this->assertTrue($role->fresh()->hasPermissionTo($permission3));
    }
}
