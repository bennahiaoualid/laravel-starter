<?php

namespace App\Livewire;

use App\Models\User;
use App\PowerGridThemes\TailwindStriped;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UserTable extends PowerGridComponent
{
    public string $tableName = 'user-table-6v7sxp-table';

    public bool $canDelete = false;

    public bool $canUpdate = false;

    public function setUp(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $this->canDelete = $user->can('delete user');
        $this->canUpdate = $user->can('update user');

        return [
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::responsive(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->whereNot('id', Auth::id())->with('roles');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('email')
            ->add('role', function ($user) {
                return optional($user->roles->first())->name ?? 'No role';
            });

    }

    public function columns(): array
    {
        return [
            Column::make(__('user.profile.name'), 'name'),
            Column::make(__('user.profile.email'), 'email'),
            Column::make(__('roles.role'), 'role'),
            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function actions(User $user): array
    {
        $buttons = [];

        // Activate/Deactivate button
        if ($this->canDelete) {
            if ($user->is_active) {
                $buttons[] = Button::add('deactivate-user-'.$user->id)
                    ->slot('<i class="fa-solid fa-ban fa-fw text-base"></i>')
                    ->class('block inline-flex items-center border rounded-md font-semibold uppercase cursor-pointer tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 px-2 py-1 text-xs bg-transparent text-warning border-warning hover:bg-warning hover:text-white focus:bg-warning focus:text-white active:bg-warning active:text-white focus:ring-warning')
                    ->dispatch('open-modal', [
                        'detail' => 'deactivate-user',
                        'value' => $user->id,
                        'input_detail' => ['userName' => $user->name],
                    ]);
            } else {
                $buttons[] = Button::add('activate-user-'.$user->id)
                    ->slot('<i class="fa-solid fa-check fa-fw text-base"></i>')
                    ->class('block inline-flex items-center border rounded-md font-semibold uppercase cursor-pointer tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 px-2 py-1 text-xs bg-transparent text-success border-success hover:bg-success hover:text-white focus:bg-success focus:text-white active:bg-success active:text-white focus:ring-success')
                    ->dispatch('open-modal', [
                        'detail' => 'activate-user',
                        'value' => $user->id,
                        'input_detail' => ['userName' => $user->name],
                    ]);
            }
        }

        // Edit button
        if ($this->canUpdate) {
            $buttons[] = Button::add('edit-user-'.$user->id)
                ->slot('<i class="fa-solid fa-edit text-base"></i>')
                ->class('block inline-flex items-center border rounded-md font-semibold uppercase cursor-pointer tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 px-2 py-1 text-xs bg-transparent text-info border-info hover:bg-info hover:text-white focus:bg-info focus:text-white active:bg-info active:text-white focus:ring-info')
                ->route('settings.users.edit', ['user' => $user], '_blank');
        }

        return $buttons;
    }

    public function customThemeClass(): ?string
    {
        return TailwindStriped::class;
    }
}
