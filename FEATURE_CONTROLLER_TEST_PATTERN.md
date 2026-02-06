# Feature Controller Test Pattern Guide

This document outlines the standard patterns for writing feature controller tests in this Laravel application. Feature tests focus on HTTP requests, authentication, authorization, middleware, and validation.

## Table of Contents
1. [Test Class Structure](#test-class-structure)
2. [Setup and Teardown](#setup-and-teardown)
3. [Authentication and Authorization](#authentication-and-authorization)
4. [Validation Testing](#validation-testing)
5. [Success and Failure Scenarios](#success-and-failure-scenarios)
6. [HTTP Assertions](#http-assertions)
7. [Database Assertions](#database-assertions)
8. [Common Test Scenarios](#common-test-scenarios)
9. [Rules and Guidelines](#rules-and-guidelines)

---

## Test Class Structure

### Class Declaration
```php
<?php

namespace Tests\Feature\Controllers\{ControllerNamespace};

use Tests\TestCase;
use App\Models\{Model};
use App\Models\Admin\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class {Controller}Test extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Admin */
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set application locale (usually 'en', 'ar', or 'fr')
        $this->refreshApplicationWithLocale('en');
        
        // Create test data that will be used across multiple tests
        $this->admin = Admin::factory()->create();
        
        // Seed roles if needed
        $this->seed(RoleSeeder::class);
        
        // Authenticate as admin for tests that require authentication
        $this->actingAs($this->admin, 'admin');

        // Fake external services
        Bus::fake();
        Mail::fake();
        Notification::fake();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
```

### Key Points
- Always extend `TestCase`
- Use `RefreshDatabase` trait - Real database for testing
- Use `WithFaker` trait - For generating fake data
- **Call `refreshApplicationWithLocale()` in setUp()** - Set application locale
- Declare authenticated user as protected property
- Fake Bus, Mail, Notification in setUp()

---

## Setup and Teardown

### setUp() Method Pattern
```php
protected function setUp(): void
{
    parent::setUp();
    
    // Set application locale (usually 'en', 'ar', or 'fr')
    $this->refreshApplicationWithLocale('en');
    
    // Create authenticated user
    $this->admin = Admin::factory()->create();
    
    // Seed roles/permissions if needed
    $this->seed(RoleSeeder::class);
    // Or seed system settings
    $this->seed(SystemSettingSeeder::class);
    
    // Authenticate user
    $this->actingAs($this->admin, 'admin');

    // Fake external services
    Bus::fake();
    Mail::fake();
    Notification::fake();
}
```

### tearDown() Method Pattern
```php
protected function tearDown(): void
{
    parent::tearDown();
}
```

---

## Authentication and Authorization

### ⚠️ CRITICAL RULE: Test All Routes in One Method for Authentication/Authorization

**Rule**: For authentication and authorization checks (like user active status), **ALWAYS test ALL routes in ONE method** instead of creating separate methods for each route.

### Test Unauthenticated Access (All Routes)
```php
public function test_all_routes_fail_without_authentication()
{
    // Don't authenticate
    Auth::logout();
    
    $resource = Resource::factory()->create();
    $data = $this->createTestData();
    
    // Test all routes that require authentication
    $storeResponse = $this->post(route('resource.store'), $data);
    $updateResponse = $this->patch(route('resource.update', $resource), $data);
    $deleteResponse = $this->post(route('resource.delete'), ['id' => $resource->id]);
    $indexResponse = $this->get(route('resource.index'));
    $showResponse = $this->get(route('resource.show', $resource));
    
    // Assert all redirect to login or return 401/403
    $storeResponse->assertRedirect(route('login'));
    $updateResponse->assertRedirect(route('login'));
    $deleteResponse->assertRedirect(route('login'));
    $indexResponse->assertRedirect(route('login'));
    $showResponse->assertRedirect(route('login'));
}
```

### Test Inactive User (All Routes)
```php
public function test_all_routes_fail_with_inactive_user()
{
    // Create inactive user
    $this->admin->update(['active' => false]);
    
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    $data = $this->createTestData();
    
    // Test all routes that require active user
    $storeResponse = $this->post(route('resource.store'), $data);
    $updateResponse = $this->patch(route('resource.update', $resource), $data);
    $deleteResponse = $this->post(route('resource.delete'), ['id' => $resource->id]);
    $indexResponse = $this->get(route('resource.index'));
    
    // Assert all fail with inactive user message
    $storeResponse->assertStatus(403);
    $updateResponse->assertStatus(403);
    $deleteResponse->assertStatus(403);
    $indexResponse->assertStatus(403);
}
```

### ⚠️ Permission Testing Rule: One Scenario Per Method

**Rule**: For permission checks, create **ONE additional scenario per method** when user doesn't have permission. All other scenarios assume user has permission and is authenticated and active.

### Test Permission Failure (Per Method)
```php
// For store method
public function test_store_fails_without_permission()
{
    // Don't give permission - user is authenticated and active
    // $this->admin->givePermissionTo('add resource'); // Skip this
    
    $data = $this->createTestData();
    $response = $this->post(route('resource.store'), $data);
    
    $response->assertStatus(403);
    $this->assertStringContainsString(
        trans('messages.validation.not_allow.unauthorized'),
        session()->get('messages')[0]['message']
    );
}

// For update method
public function test_update_fails_without_permission()
{
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    // Don't give permission
    // $this->admin->givePermissionTo('edit resource'); // Skip this
    
    $data = ['field' => 'value'];
    $response = $this->patch(route('resource.update', $resource), $data);
    
    $response->assertStatus(403);
}

// For delete method
public function test_delete_fails_without_permission()
{
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    // Don't give permission
    // $this->admin->givePermissionTo('delete resource'); // Skip this
    
    $response = $this->post(route('resource.delete'), ['id' => $resource->id]);
    
    $response->assertStatus(403);
}
```

### Test Ownership/Authorization Rules
```php
public function test_update_fails_with_unauthorized_owner()
{
    // User has permission but doesn't own the resource
    $this->admin->givePermissionTo('edit resource');
    
    $otherAdmin = Admin::factory()->create();
    $resource = Resource::factory()->create(['admin_id' => $otherAdmin->id]);
    
    $data = ['field' => 'value'];
    $response = $this->patch(route('resource.update', $resource), $data);
    
    $response->assertRedirectBack();
    $this->assertStringContainsString(
        trans('messages.validation.not_allow.resource_update'),
        session()->get('messages')[0]['message']
    );
}
```

---

## Validation Testing

### ⚠️ CRITICAL RULE: One Comprehensive Validation Test Per Method

**Rule**: Create ONE validation test per method that violates ALL validation rules at once. Each field should violate at least one rule.

### ⚠️ IMPORTANT: Always Use errorBag When FormRequest is Named

**Rule**: When asserting session errors, **ALWAYS use `errorBag` parameter if your FormRequest class is named**. The errorBag should match the FormRequest name in camelCase.

**Examples:**
- FormRequest: `StoreResourceRequest` → `errorBag: 'storeResource'`
- FormRequest: `UpdateResourceRequest` → `errorBag: 'updateResource'`
- FormRequest: `CreateCompetitionRequest` → `errorBag: 'createCompetition'`
- FormRequest: `UpdateCompetitionRequest` → `errorBag: 'updateCompetition'`

**If FormRequest is NOT named** (uses default), omit the errorBag parameter.

### Validation Test Pattern
```php
public function test_store_fails_with_invalid_data()
{
    $this->admin->givePermissionTo('add resource');
    
    // Create data that violates ALL validation rules
    // Field A: required -> missing
    // Field B: string, max:60 -> send 70 characters
    // Field C: in:1,2 -> send value 3 (out of range)
    // Field D: integer -> send string
    // Field E: date, after:today -> send past date
    
    $data = [
        // 'field_a' => missing (required violation)
        'field_b' => str_repeat('a', 70), // max:60 violation
        'field_c' => 3, // in:1,2 violation
        'field_d' => 'not_integer', // integer violation
        'field_e' => now()->subDay()->format('Y-m-d'), // after:today violation
        'field_f' => 0, // min:1 violation
    ];
    
    $response = $this->post(route('resource.store'), $data);
    
    // Assert all validation errors
    // ⚠️ IMPORTANT: Always use errorBag if the FormRequest is named
    // If your FormRequest class is named StoreResourceRequest, use errorBag: 'storeResource'
    // If your FormRequest class is named CreateResourceRequest, use errorBag: 'createResource'
    $response->assertSessionHasErrors([
        'field_a', // required
        'field_b', // max:60
        'field_c', // in:1,2
        'field_d', // integer
        'field_e', // after:today
        'field_f', // min:1
    ], errorBag: 'storeResource'); // Use the FormRequest name in camelCase
    
    // Assert nothing was dispatched/created
    Bus::assertNothingDispatched();
    $this->assertDatabaseMissing('resources', ['field_b' => str_repeat('a', 70)]);
}
```

### Common Validation Violations

#### Required Fields
```php
// Omit the field entirely
$data = [
    // 'required_field' => missing
    'other_field' => 'value',
];
```

#### String Length
```php
// Max violation
'field' => str_repeat('a', 61), // max:60

// Min violation
'field' => 'ab', // min:3
```

#### Numeric Rules
```php
// Integer violation
'field' => 'not_integer',

// Min violation
'field' => 0, // min:1

// Max violation
'field' => 1001, // max:1000
```

#### Date Rules
```php
// After violation
'start_date' => now()->subDay()->format('Y-m-d'), // after:today

// Before violation
'end_date' => now()->addDay()->format('Y-m-d'), // before:start_date
```

#### Enum/In Rules
```php
// In violation
'status' => 5, // in:1,2,3

// Not in violation
'type' => 'invalid', // not_in:deleted,archived
```

#### Custom Validation Rules
```php
// Age range violation
'age_start' => 30,
'age_end' => 20, // age_end must be greater than age_start
```

---

## Success and Failure Scenarios

### ⚠️ Focus on High Priority Scenarios

**Rule**: Only test success/failure scenarios that are HIGH PRIORITY and may NOT be covered in service unit tests.

### High Priority Scenarios to Test

1. **Authentication/Authorization** - Always test (not in service tests)
2. **Middleware Triggers** - Always test (not in service tests)
3. **HTTP Response Codes** - Always test (not in service tests)
4. **Route Binding** - Test if complex (not in service tests)
5. **Request Transformation** - Test if complex (not in service tests)
6. **Session/Flash Messages** - Test if important (not in service tests)

### Success Scenario Pattern
```php
public function test_store_success()
{
    // Arrange
    $this->admin->givePermissionTo('add resource');
    $this->admin->coinBalance->update(['balance' => 500]);
    
    // Seed system settings if needed
    $this->seed(SystemSettingSeeder::class);
    SystemSetting::where('setting_key', 'min_value')->update(['setting_value' => 50]);
    
    // Create prerequisite data
    $relatedModel = RelatedModel::factory()->create();
    
    $data = $this->createTestData([
        'related_id' => $relatedModel->id,
    ]);
    
    // Act
    $response = $this->post(route('resource.store'), $data);
    
    // Assert HTTP Response
    $response->assertRedirectBack();
    
    // Assert Database
    $this->assertDatabaseHas('resources', [
        'field' => $data['field'],
        'admin_id' => $this->admin->id,
    ]);
    
    // Assert Side Effects
    $this->assertEquals(400, $this->admin->coinBalance->fresh()->balance);
    $this->assertDatabaseHas('transactions', [
        'amount' => 100,
        'type' => 'spend',
    ]);
    
    // Assert Jobs/Notifications
    Bus::assertDispatched(ProcessJob::class);
    Bus::assertDispatched(BatchNotificationJob::class);
}
```

### Failure Scenario Pattern
```php
public function test_store_fails_insufficient_balance()
{
    // Arrange
    $this->admin->givePermissionTo('add resource');
    $this->admin->coinBalance->update(['balance' => 0]); // Insufficient
    
    $data = $this->createTestData();
    
    // Act
    $response = $this->post(route('resource.store'), $data);
    
    // Assert
    $response->assertRedirectBack();
    $this->assertStringContainsString(
        trans('messages.validation.not_allow.insufficient_balance'),
        session()->get('messages')[0]['message']
    );
    
    $this->assertDatabaseMissing('resources', ['field' => $data['field']]);
    Bus::assertNothingDispatched();
}
```

---

## HTTP Assertions

### Response Status Codes
```php
// Success redirect
$response->assertRedirectBack();
$response->assertRedirect(route('resource.index'));

// Error codes
$response->assertStatus(403); // Forbidden
$response->assertStatus(404); // Not Found
$response->assertStatus(422); // Validation Error
$response->assertStatus(500); // Server Error
```

### Session Errors
```php
// Single error (without error bag - if FormRequest is not named)
$response->assertSessionHasErrors('field');

// Multiple errors (without error bag)
$response->assertSessionHasErrors([
    'field_a',
    'field_b',
    'field_c',
]);

// ⚠️ IMPORTANT: Always use errorBag when FormRequest is named
// If FormRequest class is StoreResourceRequest -> errorBag: 'storeResource'
// If FormRequest class is UpdateResourceRequest -> errorBag: 'updateResource'
// If FormRequest class is CreateCompetitionRequest -> errorBag: 'createCompetition'
$response->assertSessionHasErrors([
    'field_a',
    'field_b',
    'field_c',
], errorBag: 'storeResource'); // Use FormRequest name in camelCase

// No errors
$response->assertSessionHasNoErrors();
```

### Flash Messages
```php
// Check flash message content
$this->assertStringContainsString(
    trans('messages.validation.success.saved'),
    session()->get('messages')[0]['message']
);

// Check error message
$this->assertStringContainsString(
    trans('messages.validation.not_allow.unauthorized'),
    session()->get('messages')[0]['message']
);
```

### JSON Responses (if API)
```php
$response->assertJson([
    'success' => true,
    'data' => ['id' => 1],
]);

$response->assertJsonValidationErrors(['field']);
```

---

## Database Assertions

### Assert Record Created
```php
$this->assertDatabaseHas('table_name', [
    'column' => 'value',
    'admin_id' => $this->admin->id,
]);
```

### Assert Record Updated
```php
$this->assertDatabaseHas('table_name', [
    'id' => $resource->id,
    'field' => 'new_value',
]);
```

### Assert Record Deleted
```php
$this->assertDatabaseMissing('table_name', [
    'id' => $resource->id,
]);
```

### Assert Relationship Created
```php
$this->assertDatabaseHas('pivot_table', [
    'resource_id' => $resource->id,
    'related_id' => $related->id,
]);
```

### Assert Count
```php
$this->assertDatabaseCount('table_name', 5);
```

---

## Common Test Scenarios

### 1. Create Success
```php
public function test_store_success()
{
    $this->admin->givePermissionTo('add resource');
    $data = $this->createTestData();
    
    $response = $this->post(route('resource.store'), $data);
    
    $response->assertRedirectBack();
    $this->assertDatabaseHas('resources', [
        'field' => $data['field'],
        'admin_id' => $this->admin->id,
    ]);
    Bus::assertDispatched(ProcessJob::class);
}
```

### 2. Create Validation Failure
```php
public function test_store_fails_with_invalid_data()
{
    $this->admin->givePermissionTo('add resource');
    
    $data = [
        // Violate all rules
        // 'required_field' => missing,
        'string_field' => str_repeat('a', 70), // max:60
        'enum_field' => 5, // in:1,2,3
        'date_field' => now()->subDay()->format('Y-m-d'), // after:today
    ];
    
    $response = $this->post(route('resource.store'), $data);
    
    // ⚠️ Always use errorBag if FormRequest is named
    // FormRequest: StoreResourceRequest -> errorBag: 'storeResource'
    $response->assertSessionHasErrors([
        'required_field',
        'string_field',
        'enum_field',
        'date_field',
    ], errorBag: 'storeResource');
    
    Bus::assertNothingDispatched();
}
```

### 3. Create Unauthorized
```php
public function test_store_fails_without_permission()
{
    // Don't give permission
    $data = $this->createTestData();
    
    $response = $this->post(route('resource.store'), $data);
    
    $response->assertStatus(403);
}
```

### 4. Update Success
```php
public function test_update_success()
{
    // User is authenticated, active, and has permission
    $this->admin->givePermissionTo('edit resource');
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    $data = ['field' => 'new_value'];
    
    $response = $this->patch(route('resource.update', $resource), $data);
    
    $response->assertRedirectBack();
    $this->assertDatabaseHas('resources', [
        'id' => $resource->id,
        'field' => 'new_value',
    ]);
    Bus::assertDispatched(SyncJob::class);
}
```

### 5. Update Permission Failure
```php
public function test_update_fails_without_permission()
{
    // User is authenticated and active, but NO permission
    // $this->admin->givePermissionTo('edit resource'); // Skip this
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    $data = ['field' => 'new_value'];
    
    $response = $this->patch(route('resource.update', $resource), $data);
    
    $response->assertStatus(403);
}
```

### 6. Update Unauthorized Owner
```php
public function test_update_fails_with_unauthorized_owner()
{
    // User has permission but doesn't own the resource
    $this->admin->givePermissionTo('edit resource');
    $otherAdmin = Admin::factory()->create();
    $resource = Resource::factory()->create(['admin_id' => $otherAdmin->id]);
    $data = ['field' => 'new_value'];
    
    $response = $this->patch(route('resource.update', $resource), $data);
    
    $response->assertRedirectBack();
    $this->assertStringContainsString(
        trans('messages.validation.not_allow.resource_update'),
        session()->get('messages')[0]['message']
    );
}
```

### 7. Delete Success
```php
public function test_delete_success()
{
    // User is authenticated, active, and has permission
    $this->admin->givePermissionTo('delete resource');
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    
    $response = $this->post(route('resource.delete'), ['id' => $resource->id]);
    
    $response->assertRedirectBack();
    $this->assertDatabaseMissing('resources', ['id' => $resource->id]);
}
```

### 8. Delete Permission Failure
```php
public function test_delete_fails_without_permission()
{
    // User is authenticated and active, but NO permission
    // $this->admin->givePermissionTo('delete resource'); // Skip this
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    
    $response = $this->post(route('resource.delete'), ['id' => $resource->id]);
    
    $response->assertStatus(403);
}
```

### 7. Add Relationship Success
```php
public function test_add_relationship_success()
{
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    $related = RelatedModel::factory()->count(3)->create();
    $ids = $related->pluck('id')->toArray();
    
    $response = $this->post(route('resource.relationship.store', $resource), [
        'related_ids' => implode(',', $ids)
    ]);
    
    $response->assertRedirectBack();
    foreach ($ids as $id) {
        $this->assertDatabaseHas('resource_related', [
            'resource_id' => $resource->id,
            'related_id' => $id,
        ]);
    }
    Bus::assertDispatched(BatchNotificationJob::class);
}
```

### 8. Remove Relationship Success
```php
public function test_remove_relationship_success()
{
    $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
    $related = RelatedModel::factory()->create();
    $resource->related()->attach($related->id);
    
    $response = $this->post(route('resource.relationship.delete'), [
        'resource_id' => $resource->id,
        'related_id' => $related->id,
    ]);
    
    $response->assertRedirectBack();
    $this->assertDatabaseMissing('resource_related', [
        'resource_id' => $resource->id,
        'related_id' => $related->id,
    ]);
}
```

---

## Rules and Guidelines

### ✅ DO Test

1. **Authentication** - Test ALL routes in ONE method for unauthenticated access
2. **Authorization (User Status)** - Test ALL routes in ONE method for inactive user or other auth issues
3. **Permissions** - ONE permission failure scenario per method (user authenticated, active, but no permission)
4. **Ownership Rules** - Test ownership/authorization rules per method
5. **Middleware** - Test middleware triggers and responses
6. **Validation** - ONE comprehensive test per method violating ALL rules
7. **Success Scenarios** - Test success per method (user authenticated, active, has permission)
8. **HTTP Responses** - Test redirects, status codes, session errors
9. **High Priority Business Logic** - Only if not covered in service tests
10. **Job Dispatching** - Test if jobs are dispatched correctly
11. **Database Side Effects** - Test balance updates, transaction creation, etc.

### ❌ DON'T Test

1. **Pure Business Logic** - Covered in service unit tests
2. **Multiple Validation Scenarios** - ONE comprehensive test is enough
3. **Simple CRUD** - Focus on auth, validation, middleware
4. **Low Priority Edge Cases** - Only high priority scenarios
5. **Internal Service Methods** - Test through HTTP requests only

### Test Coverage Priorities

1. **High Priority** (Always test):
   - Authentication (ALL routes in ONE method - unauthenticated access)
   - Authorization - User Status (ALL routes in ONE method - inactive user, etc.)
   - Permissions (ONE scenario per method - no permission)
   - Ownership (per method - unauthorized owner)
   - Middleware triggers
   - Validation (ONE comprehensive test per method)
   - Success scenarios (per method - authenticated, active, has permission)
   - HTTP response codes
   - Job dispatching
   - Critical side effects (balance updates, etc.)

2. **Medium Priority** (Test if complex):
   - Request transformation
   - Route model binding edge cases
   - Complex middleware logic

3. **Low Priority** (Skip):
   - Simple redirects
   - Basic CRUD operations
   - Business logic (covered in service tests)

---

## Helper Methods Pattern

### Create Test Data Helper
```php
/**
 * Create test data for requests
 * 
 * @param array $override Override default values
 * @return array
 */
private function createTestData(array $override = []): array
{
    return array_merge([
        'field_a' => 'default_value',
        'field_b' => 'default_string',
        'field_c' => 1,
        'field_d' => now()->addDay()->format('Y-m-d'),
        'field_e' => 100,
    ], $override);
}
```

---

## Example: Complete Test File Structure

```php
<?php

namespace Tests\Feature\Controllers\Resource;

use Tests\TestCase;
use App\Models\Resource;
use App\Models\Admin\Admin;
use App\Models\SystemSetting;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SystemSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessResourceJob;
use App\Jobs\Notifications\BatchNotificationJob;

class ResourceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Admin */
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set application locale
        $this->refreshApplicationWithLocale('en');
        
        $this->admin = Admin::factory()->create();
        $this->seed(RoleSeeder::class);
        $this->actingAs($this->admin, 'admin');

        Bus::fake();
    }

    public function test_store_success()
    {
        $this->admin->givePermissionTo('add resource');
        $this->admin->coinBalance->update(['balance' => 500]);
        $this->seed(SystemSettingSeeder::class);
        
        $data = $this->createTestData();
        $response = $this->post(route('resource.store'), $data);
        
        $response->assertRedirectBack();
        $this->assertDatabaseHas('resources', [
            'field' => $data['field'],
            'admin_id' => $this->admin->id,
        ]);
        Bus::assertDispatched(ProcessResourceJob::class);
    }

    public function test_store_fails_with_invalid_data()
    {
        $this->admin->givePermissionTo('add resource');
        
        $data = [
            // 'required_field' => missing,
            'string_field' => str_repeat('a', 70), // max:60
            'enum_field' => 5, // in:1,2,3
            'date_field' => now()->subDay()->format('Y-m-d'), // after:today
        ];
        
        $response = $this->post(route('resource.store'), $data);
        
        // ⚠️ Always use errorBag when FormRequest is named
        // FormRequest: StoreResourceRequest -> errorBag: 'storeResource'
        $response->assertSessionHasErrors([
            'required_field',
            'string_field',
            'enum_field',
            'date_field',
        ], errorBag: 'storeResource');
        
        Bus::assertNothingDispatched();
    }

    public function test_all_routes_fail_without_authentication()
    {
        Auth::logout();
        
        $resource = Resource::factory()->create();
        $data = $this->createTestData();
        
        $storeResponse = $this->post(route('resource.store'), $data);
        $updateResponse = $this->patch(route('resource.update', $resource), $data);
        $deleteResponse = $this->post(route('resource.delete'), ['id' => $resource->id]);
        
        $storeResponse->assertRedirect(route('login'));
        $updateResponse->assertRedirect(route('login'));
        $deleteResponse->assertRedirect(route('login'));
    }

    public function test_store_fails_without_permission()
    {
        // User authenticated and active, but NO permission
        // $this->admin->givePermissionTo('add resource'); // Skip this
        $data = $this->createTestData();
        $response = $this->post(route('resource.store'), $data);
        
        $response->assertStatus(403);
    }

    public function test_update_fails_without_permission()
    {
        // User authenticated and active, but NO permission
        // $this->admin->givePermissionTo('edit resource'); // Skip this
        $resource = Resource::factory()->create(['admin_id' => $this->admin->id]);
        $data = ['field' => 'value'];
        
        $response = $this->patch(route('resource.update', $resource), $data);
        
        $response->assertStatus(403);
    }

    public function test_update_fails_with_unauthorized_owner()
    {
        // User has permission but doesn't own the resource
        $this->admin->givePermissionTo('edit resource');
        $otherAdmin = Admin::factory()->create();
        $resource = Resource::factory()->create(['admin_id' => $otherAdmin->id]);
        $data = ['field' => 'value'];
        
        $response = $this->patch(route('resource.update', $resource), $data);
        
        $response->assertRedirectBack();
        $this->assertStringContainsString(
            trans('messages.validation.not_allow.resource_update'),
            session()->get('messages')[0]['message']
        );
    }

    private function createTestData(array $override = []): array
    {
        return array_merge([
            'required_field' => 'value',
            'string_field' => 'valid_string',
            'enum_field' => 1,
            'date_field' => now()->addDay()->format('Y-m-d'),
        ], $override);
    }
}
```

---

## Notes

- Always follow the AAA pattern (Arrange, Act, Assert)
- Focus on HTTP layer: authentication, authorization, validation, middleware
- **Test ALL routes in ONE method** for authentication and authorization (user active status)
- **ONE permission failure scenario per method** - user authenticated, active, but no permission
- **All other scenarios** assume user is authenticated, active, and has permission
- **ONE comprehensive validation test per method** - violate ALL rules at once
- Use real database with `factory()->create()`
- Test high priority scenarios not covered in service unit tests
- Don't duplicate service test coverage
- Test middleware triggers and HTTP responses
- Use helper methods for creating test data
- Fake Bus, Mail, Notification in setUp()
- Always use `refreshApplicationWithLocale()` in setUp()

