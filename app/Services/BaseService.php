<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Relations\Relation;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class BaseService
{
    protected $data;

    protected function getMorphClass(object $data = null)
    {
        $data = $data ?? $this->data;

        $morphMap = Relation::morphMap();
        return array_search(get_class($data), $morphMap, true);
    }

    protected function getErrorException(\Throwable $e): array
    {
        $message = match (get_class($e)) {
            ValidatorException::class => $e->getMessageBag(),
            default => $e->getMessage(),
        };

        return [
            'success' => false,
            'message' => $message,
        ];
    }
}
