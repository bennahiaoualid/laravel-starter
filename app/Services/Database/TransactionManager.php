<?php

namespace App\Services\Database;

use App\Contracts\TransactionManagerInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionManager implements TransactionManagerInterface
{
    public function run(callable $callback): mixed
    {
        DB::beginTransaction();
        try {
            $result = $callback();
            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
