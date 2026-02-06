# Entity Generation Template

This document outlines the structure and pattern used to generate complete CRUD modules for entities in the management system.

## Structure Overview

When given a migration file for any entity, the following files need to be generated:

### 1. Model (e.g., `app/Models/Entity.php`)
```php
protected $fillable = [
    // All fillable fields from migration
];
```

### 2. Controller (e.g., `app/Http/Controllers/EntityController.php`)
- `list()`: Returns view for listing page
- `store(StoreEntityRequest $request)`: Handles entity creation
- `edit(Entity $entity)`: Returns view for edit page
- `update(UpdateEntityRequest $request, Entity $entity)`: Handles entity update
- `delete(Request $request)`: Handles entity deletion

### 3. Service (e.g., `app/Services/Entity/EntityService.php`)
- `create(array $data)`: Creates new entity with transaction
- `update(Entity $entity, array $data)`: Updates entity with transaction
- `delete(Entity $entity, string $reason)`: Deletes entity

### 4. Request Classes
- `StoreEntityRequest.php`: Validation rules for creation
- `UpdateEntityRequest.php`: Validation rules for update

### 5. Routes (`routes/web.php`)
```php
Route::middleware('can:view entity')->get('/entities', [EntityController::class, "list"])->name("entities");
Route::middleware('can:add entity')->post('/entities/store', [EntityController::class, "store"])->name("entities.store");
Route::middleware('can:update entity')->get('/entities/edit/{entity}', [EntityController::class, "edit"])->name("entities.edit");
Route::patch('/entities/update/{entity}', [EntityController::class, "update"])->name("entities.update");
Route::middleware("can:delete entity")->post('/entities/delete', [EntityController::class, "delete"])->name("entities.delete");
```

### 6. Blade Views
- `resources/views/pages/entity/list.blade.php`: List view with modal for creation
- `resources/views/pages/entity/edit.blade.php`: Edit form
- `resources/views/components/tables/entity-table-action-buttons.blade.php`: Action buttons

### 7. Livewire Component
- `app/Livewire/EntityTable.php`: Data table for listing entities

### 8. Translation Keys
- `lang/en/user.php`: Add `'entity' => ['field1' => 'Field1', ...]`
- `lang/ar/user.php`: Add `'entity' => ['field1' => 'Field1', ...]`
- `lang/fr/user.php`: Add `'entity' => ['field1' => 'Field1', ...]`
- `lang/en/form.php`: Add `'entity' => ['add' => "add new entity", 'delete' => "delete entity"]`
- `lang/ar/form.php`: Add `'entity' => ['add' => "إضافة entity جديد", 'delete' => "حذف entity"]`
- `lang/fr/form.php`: Add `'entity' => ['add' => "Ajouter un nouveau entity", 'delete' => "Supprimer entity"]`
- `lang/en/links.php`: Add `'entity' => ['list' => 'Entities']`
- `lang/ar/links.php`: Add `'entity' => ['list' => 'Entities']`
- `lang/fr/links.php`: Add `'entity' => ['list' => 'Entities']`

## Key Patterns

### File Naming
- Model: PascalCase singular (Entity.php)
- Controller: EntityController.php
- Service: Services/Entity/EntityService.php
- Requests: Http/Requests/Entity/StoreEntityRequest.php
- Views: pages/entity/list.blade.php, pages/entity/edit.blade.php
- Livewire: EntityTable.php
- Table actions: components/tables/entity-table-action-buttons.blade.php

### Translation Key Format
- Labels: `__('user.entity.field_name')`
- Form actions: `__('form.entity.add')`, `__('form.entity.delete')`
- Links: `__('links.entity.list')`

### Permission Format
- Can permissions: `can:view entity`, `can:add entity`, `can:update entity`, `can:delete entity`

## Generation Steps

1. Read migration file to extract table name and columns
2. Generate Model with fillable fields
3. Generate Controller with CRUD methods
4. Generate Service with transaction handling
5. Generate Request validation classes
6. Generate Livewire table component
7. Generate Blade views (list and edit)
8. Generate table action buttons
9. Add routes to web.php
10. Add translation keys to user.php, form.php, and links.php (en, ar, fr)
11. Add permissions to RoleSeeder.php
12. Add permission translations (en, ar, fr)

## Example Usage

For an "employee" entity with fields: name, email, position, salary, department_id
- Generate all files following the Partie pattern
- Use "employee" as the entity name
- Pluralize for routes: "employees"
- Follow the exact structure documented here

