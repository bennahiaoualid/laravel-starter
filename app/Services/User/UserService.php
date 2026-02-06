<?php

namespace App\Services\User;

use App\Contracts\FlasherInterface;
use App\Contracts\TransactionManagerInterface;
use App\Models\User;
use App\Traits\RegisterLogs;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService
{
    use RegisterLogs;

    /**
     * The service coordinates business logic, validation, filtering, transformation, and notifications.
     * The repository is used for data access only.
     */
    public function __construct(
        protected FlasherInterface $flasher,
        protected TransactionManagerInterface $transaction_manager_interface
    ) {}

    /**
     * Create a new user with transaction and notification.
     */
    public function create(array $data)
    {
        try {
            return $this->transaction_manager_interface->run(function () use ($data) {
                $user = User::create($data);
                $user->roles()->sync($data['role']);
                $this->flasher->crudSuccess('saved');
                
                return true;
            });

        } catch (Exception $exception) {
            $this->registerLogs('UserService::create', $exception);
            $this->flasher->crudFailure('saved');

            return false;
        }
    }

    /**
     * Update a user with transaction and notification.
     *
     * @return bool
     */
    public function update(User $user, array $data)
    {
        try {
            return $this->transaction_manager_interface->run(function () use ($user, $data) {
                $user->name = $data['name'];
                $user->email = $data['email'];

                $user->save();

                $user->roles()->sync($data['role']);

                $this->flasher->crudSuccess('updated');

                return true;
            });
        } catch (Exception $exception) {
            $this->registerLogs('UserService::update', $exception);
            $this->flasher->crudFailure('updated');

            return false;
        }
    }

    /**
     * Toggle user active status (activate/deactivate)
     *
     * @return bool
     */
    public function toggleStatus(User $user)
    {
        try {
            return $this->transaction_manager_interface->run(function () use ($user) {
                if ($user->id === Auth::id()) {
                    $this->flasher->error('You cannot deactivate yourself');

                    return false;
                }
                $user->is_active = ! $user->is_active;
                $user->save();

                $this->flasher->crudSuccess('updated');

                return true;
            });
        } catch (Exception $exception) {
            $this->registerLogs('UserService::toggleStatus', $exception);
            $this->flasher->crudFailure('updated');

            return false;
        }
    }
}
