# Service Test Pattern Guide

This document outlines the standard patterns for writing service tests in this Laravel application. Use this guide when generating test files for service classes.

## Table of Contents
1. [Test Class Structure](#test-class-structure)
2. [Setup and Teardown](#setup-and-teardown)
3. [Mocking Patterns](#mocking-patterns)
4. [Test Method Patterns](#test-method-patterns)
5. [Factory Usage](#factory-usage)
6. [Assertion Patterns](#assertion-patterns)
7. [Common Test Scenarios](#common-test-scenarios)
8. [Rules and Guidelines](#rules-and-guidelines)

---

## Test Class Structure

### Class Declaration
```php
<?php

namespace Tests\Unit\Services\{ServiceNamespace};

use Bus;
use Mockery;
use Tests\TestCase;
use App\Models\{Model};
use App\Contracts\{Interface};
use Illuminate\Support\Facades\Auth;
use App\Services\{ServiceClass};
use Illuminate\Foundation\Testing\RefreshDatabase;

class {ServiceClass}Test extends TestCase
{
    use RefreshDatabase;

    /** @var {ServiceClass} */
    protected $service;
    protected $repository;
    protected $transactionManager;
    protected $flasher;
    // ... other dependencies
}
```

### Key Points
- Always extend `TestCase`
- **Use RefreshDatabase trait** - We use real database for Eloquent operations
- Declare service instance and dependencies as protected properties
- Use PHPDoc `@var` annotation for the service property
- **Tests go in Unit folder** - Use `Tests\Unit\Services` namespace

---

## Setup and Teardown

### setUp() Method Pattern
```php
protected function setUp(): void
{
    parent::setUp();
    
    // Mock all external dependencies
    $this->repository = Mockery::mock(RepositoryInterface::class);
    $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
    $this->flasher = Mockery::mock(FlasherInterface::class);
    // ... mock all other dependencies
    
    // Instantiate service with mocked dependencies
    $this->service = new ServiceClass(
        $this->repository,
        $this->transactionManager,
        $this->flasher,
        // ... other dependencies
    );
    
    // Mock static/alias classes if needed
    $this->userNotify = Mockery::mock('alias:'.UserNotifyEmail::class);
    
    // Setup authenticated user if needed (use real factory)
    $this->mainAdmin = Admin::factory()->create(['name' => 'main admin']);
    Auth::shouldReceive('id')->andReturn($this->mainAdmin->id);
    Auth::shouldReceive('user')->andReturn($this->mainAdmin);
    
    // Fake bus for job testing
    Bus::fake();
    
    // Fake mail if needed
    Mail::fake();
    
    // Fake notifications if needed
    Notification::fake();
}
```

### tearDown() Method Pattern
```php
protected function tearDown(): void
{
    Mockery::close();
    parent::tearDown();
}
```

---

## Mocking Patterns

### Basic Mock Setup
```php
// Mock an interface
$this->repository = Mockery::mock(RepositoryInterface::class);

// Mock a concrete class
$this->service = Mockery::mock(ServiceClass::class);

// Mock an alias/static class
$this->helper = Mockery::mock('alias:'.HelperClass::class);
```

### Mock Expectations

#### Simple Return Value
```php
$this->repository
    ->shouldReceive('findById')
    ->with($id)
    ->andReturn($model);
```

#### Return Using Callback
```php
$this->transactionManager
    ->shouldReceive('run')
    ->andReturnUsing(fn($callback) => $callback());
```

#### Throw Exception
```php
$this->transactionManager
    ->shouldReceive('run')
    ->andThrow(new \Exception('DB error'));
```

#### Multiple Calls with Different Returns
```php
$this->flasher
    ->shouldReceive('info')
    ->once()
    ->with(__('messages.success.message'));
    
$this->flasher
    ->shouldReceive('error')
    ->once()
    ->with(__('messages.error.message'));
```

#### Return Array/Complex Data
```php
$this->repository
    ->shouldReceive('update')
    ->with($model, $data)
    ->andReturn([
        'model' => $model,
        'resync' => true
    ]);
```

### Partial Mocking (for Models)
```php
$model = Model::factory()->make(['id' => 123]);
$model = \Mockery::mock($model)->makePartial();
$model->shouldReceive('methodName')->andReturn($value);
```

---

## Test Method Patterns

### Test Method Naming Convention
Use snake_case with descriptive names:
- `test_{method_name}_{scenario}_{expected_result}`
- Examples:
  - `test_create_competition_success`
  - `test_create_competition_fail_insufficient_balance`
  - `test_update_competition_handles_exception`
  - `test_delete_competition_fail_unauthorized`

### Basic Test Structure (AAA Pattern)
```php
public function test_method_name_scenario()
{
    // Arrange: Setup mocks and data
    $data = Model::factory()->make()->toArray();
    $this->repository->shouldReceive('method')->andReturn($value);
    
    // Act: Execute the method
    $result = $this->service->methodName($data);
    
    // Assert: Verify results
    $this->assertTrue($result);
    // Note: No database assertions - we mock everything
}
```

---

## Factory Usage

### ✅ Use Real Database with `create()`

**Rule**: When the service uses Eloquent, use `factory()->create()` to persist models to the database.

### Factory Patterns
```php
// Single model
$model = Model::factory()->create();

// Multiple models
$models = Model::factory()->count(3)->create();

// With attributes
$model = Model::factory()->create(['status' => 'active']);

// Relationships
$parent = ParentModel::factory()->create();
$child = ChildModel::factory()->create(['parent_id' => $parent->id]);

// Using relationships
$parent->children()->saveMany(ChildModel::factory()->count(2)->make());
```

### When to Use `make()` vs `create()`

**Use `create()`** when:
- Service uses Eloquent operations
- You need to test database persistence
- You need relationships to work
- You need to test database queries

**Use `make()`** only when:
- You're testing data transformation that doesn't touch the database
- You're creating test data that won't be persisted

---

## Assertion Patterns

### Boolean Results
```php
$this->assertTrue($result);
$this->assertFalse($result);
```

### Value Comparisons
```php
$this->assertEquals($expected, $actual);
$this->assertEquals('active', $model->status);
```

### Database Assertions
```php
// ⚠️ DO NOT USE DATABASE ASSERTIONS
// We avoid database operations entirely. Instead, verify through mocks:
// - Verify repository methods were called
// - Verify service methods returned expected values
// - Verify flasher messages were called
// Example:
$this->repository->shouldReceive('create')->once();
$this->flasher->shouldReceive('crudSuccess')->with('saved')->once();
```

### Job Assertions
```php
// Assert job was dispatched
Bus::assertDispatched(JobClass::class);

// Assert job was not dispatched
Bus::assertNotDispatched(JobClass::class);
```

### Instance Assertions
```php
$this->assertInstanceOf(ExpectedClass::class, $result);
```

### Reflection for Private Methods
```php
$method = (new \ReflectionClass($this->service))->getMethod('privateMethod');
$method->setAccessible(true);
$result = $method->invoke($this->service, $arg1, $arg2);
```

---

## Common Test Scenarios

### 1. Success Scenario
```php
public function test_create_entity_success()
{
    // Arrange
    $data = Model::factory()->make()->toArray();
    $this->transactionManager->shouldReceive('run')->andReturnUsing(fn($cb) => $cb());
    $this->flasher->shouldReceive('crudSuccess')->with('saved')->once();
    $this->repository->shouldReceive('create')->once();
    
    // Act
    $result = $this->service->createEntity($data);
    
    // Assert
    $this->assertTrue($result);
    $this->repository->shouldHaveReceived('create')->once();
    Bus::assertDispatched(RelatedJob::class);
}
```

### 2. Validation Failure Scenario
```php
public function test_create_entity_fail_validation_error()
{
    // Arrange
    $data = Model::factory()->make()->toArray();
    $this->systemSetting->shouldReceive('getValue')->andReturn($minValue);
    $this->flasher->shouldReceive('error')
        ->with(__('messages.validation.error.message'))
        ->once();
    
    // Act
    $result = $this->service->createEntity($data);
    
    // Assert
    $this->assertFalse($result);
    $this->repository->shouldNotHaveReceived('create');
    Bus::assertNotDispatched(RelatedJob::class);
}
```

### 3. Exception Handling Scenario
```php
public function test_create_entity_handles_exception()
{
    // Arrange
    $data = Model::factory()->make()->toArray();
    $this->transactionManager->shouldReceive('run')->andThrow(new \Exception('DB error'));
    $this->flasher->shouldReceive('crudFailure')->with('saved')->once();
    
    // Act
    $result = $this->service->createEntity($data);
    
    // Assert
    $this->assertFalse($result);
    Bus::assertNotDispatched(RelatedJob::class);
}
```

### 4. Unauthorized Access Scenario
```php
public function test_delete_entity_fail_unauthorized()
{
    // Arrange
    $otherAdmin = Admin::factory()->make(['id' => 999]);
    $entity = Entity::factory()->make(['id' => 1, 'admin_id' => $otherAdmin->id]);
    $this->repository->shouldReceive('findById')->andReturn($entity);
    $this->flasher->shouldReceive('error')
        ->with(__('messages.validation.not_allow.unauthorized'))
        ->once();
    
    // Act
    $result = $this->service->deleteEntity($entity->id);
    
    // Assert
    $this->assertFalse($result);
}
```

### 5. Not Found Scenario
```php
public function test_find_entity_handles_not_found()
{
    // Arrange
    $this->repository->shouldReceive('findById')->andReturn(null);
    $this->flasher->shouldReceive('error')->once();
    
    // Act
    $result = $this->service->findEntity(999);
    
    // Assert
    $this->assertFalse($result);
}
```

### 6. Update with Conditional Logic
```php
public function test_update_entity_success_with_condition()
{
    // Arrange
    $entity = Entity::factory()->make(['id' => 1]);
    $data = ['field' => 'value'];
    $this->transactionManager->shouldReceive('run')->andReturnUsing(fn($cb) => $cb());
    $this->flasher->shouldReceive('crudSuccess')->with('updated')->once();
    $this->repository
        ->shouldReceive('update')
        ->with($entity, $data)
        ->andReturn([
            'entity' => $entity,
            'shouldResync' => true
        ]);
    
    // Act
    $result = $this->service->updateEntity($entity, $data);
    
    // Assert
    $this->assertTrue($result);
    Bus::assertDispatched(SyncJob::class);
}
```

### 7. Relationship Testing
```php
public function test_add_relationship_success()
{
    // Arrange
    $parent = ParentModel::factory()->make(['id' => 1]);
    $children = ChildModel::factory()->count(2)->make(['parent_id' => 1]);
    $this->transactionManager->shouldReceive('run')->andReturnUsing(fn($cb) => $cb());
    $this->flasher->shouldReceive('crudSuccess')->with('saved')->once();
    $this->repository->shouldReceive('addRelationship')->once();
    
    // Act
    $result = $this->service->addRelationship($parent, $children->pluck('id')->toArray());
    
    // Assert
    $this->assertTrue($result);
}
```

### 8. Complex Validation with Multiple Conditions
```php
public function test_activate_entity_success()
{
    // Arrange
    $entity = Entity::factory()->make([
        'id' => 1,
        'start_date' => now()->subDay(),
        'status' => 'pending',
    ]);
    $entity = \Mockery::mock($entity)->makePartial();
    $entity->shouldReceive('validationMethod')->andReturn(true);
    $entity->shouldReceive('getRelated')->andReturn(Related::factory()->count(2)->make());
    
    $this->transactionManager->shouldReceive('run')->andReturnUsing(fn($cb) => $cb());
    $this->flasher->shouldReceive('crudSuccess')->with('activated')->once();
    
    // Act
    $result = $this->service->activateEntity($entity);
    
    // Assert
    $this->assertTrue($result);
    $this->assertEquals('active', $entity->status);
}
```

---

## Rules and Guidelines

### ✅ DO Test

1. **Recalculations** - Test balance calculations, totals, etc.
2. **Conditional Branching** - Test if/else logic, validation rules
3. **Invoking External Dependencies** - Test job dispatch, mail sending, notifications
4. **Multi-Step Operations** - Test complex workflows
5. **Domain Rules** - Test business logic and rules
6. **Errors and Exception Flows** - Test error handling

### ❌ DON'T Test

1. **Pure CRUD Services** - Don't test services that only do create/read/update/delete
2. **Methods that Only Wrap Eloquent** - Don't test simple Eloquent wrappers
3. **Trivial Logic** - Don't test `if ($x) return true` type methods
4. **Methods with No Business Rule** - Don't test methods without business logic

### ✅ DO Mock

1. **Flasher** - Always mock FlasherInterface
2. **Queue/Job Dispatcher** - Use Bus::fake()
3. **Mail** - Use Mail::fake()
4. **External APIs** - Mock HTTP calls
5. **Notifications** - Use Notification::fake()
6. **Transaction Manager** - Optional, can be mocked for rollback scenarios

### ❌ NEVER Mock

1. **Eloquent Models (Statically)** - Use real models with factories
2. **Factories** - Use real factory()->create()
3. **Model Methods** - Use real model instances
4. **Relationships** - Use real Eloquent relationships

### Database Usage

- **Use Real Database** - When service uses Eloquent, use RefreshDatabase and factory()->create()
- **Test Database State** - Use assertDatabaseHas, assertDatabaseMissing, etc.
- **Use Relationships** - Create real relationships with attach(), saveMany(), etc.

### Test Coverage Priorities

1. **High Priority** (Always test):
   - Recalculation methods
   - Conditional branching logic
   - Multi-step operations
   - Domain rules and business logic
   - Exception handling

2. **Medium Priority** (Test when complex):
   - Data transformation methods
   - Complex calculations
   - External dependency invocations

3. **Low Priority** (Skip):
   - Pure CRUD operations
   - Simple Eloquent wrappers
   - Trivial helper methods
   - Methods without business rules

---

## Common Mock Patterns Reference

### Transaction Manager
```php
// Success
$this->transactionManager->shouldReceive('run')->andReturnUsing(fn($cb) => $cb());

// Failure
$this->transactionManager->shouldReceive('run')->andThrow(new \Exception('DB error'));
```

### Flasher
```php
// Success messages
$this->flasher->shouldReceive('crudSuccess')->with('saved')->once();
$this->flasher->shouldReceive('crudSuccess')->with('updated')->once();
$this->flasher->shouldReceive('crudSuccess')->with('deleted')->once();
$this->flasher->shouldReceive('crudSuccess')->with('activated')->once();

// Failure messages
$this->flasher->shouldReceive('crudFailure')->with('saved')->once();

// Error messages
$this->flasher->shouldReceive('error')->with(__('messages.error'))->once();

// Info messages
$this->flasher->shouldReceive('info')->with(__('messages.info'))->once();
```

### Auth Facade
```php
Auth::shouldReceive('id')->andReturn($admin->id);
Auth::shouldReceive('user')->andReturn($admin);
```

### Bus Assertions
```php
Bus::fake(); // In setUp()

Bus::assertDispatched(JobClass::class);
Bus::assertNotDispatched(JobClass::class);
```

---

## Example: Complete Test File Structure

```php
<?php

namespace Tests\Unit\Services\Example;

use Bus;
use Mail;
use Mockery;
use Tests\TestCase;
use App\Models\Example;
use App\Contracts\FlasherInterface;
use Illuminate\Support\Facades\Auth;
use App\Services\Example\ExampleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var ExampleService */
    protected $service;
    protected $repository;
    protected $transactionManager;
    protected $flasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ExampleRepositoryInterface::class);
        $this->transactionManager = Mockery::mock(TransactionManagerInterface::class);
        $this->flasher = Mockery::mock(FlasherInterface::class);

        $this->service = new ExampleService(
            $this->repository,
            $this->transactionManager,
            $this->flasher
        );

        $this->mainAdmin = Admin::factory()->create();
        Auth::shouldReceive('id')->andReturn($this->mainAdmin->id);
        Auth::shouldReceive('user')->andReturn($this->mainAdmin);
        Bus::fake();
        Mail::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_balance_success()
    {
        // Arrange
        $model = Example::factory()->create(['balance' => 100]);
        $this->flasher->shouldReceive('crudSuccess')->with('updated')->once();
        
        // Act
        $result = $this->service->recalculateBalance($model);
        
        // Assert
        $this->assertTrue($result);
        $this->assertEquals(150, $model->fresh()->balance);
    }

    // ... more tests
}
```

---

## Notes

- Always follow the AAA pattern (Arrange, Act, Assert)
- Keep tests focused on one scenario per test method
- Use descriptive names that explain what is being tested
- **Use real database** when service uses Eloquent
- **Never mock Eloquent models, factories, or relationships**
- **Only mock**: flasher, queue, mail, external APIs, notifications
- Test business logic, not framework code
- Test both success and failure paths
- Include edge cases and boundary conditions
- **Don't test pure CRUD services** - focus on business logic
- **Unit tests go in `Tests\Unit\Services` namespace**

