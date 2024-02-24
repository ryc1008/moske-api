<?php

declare(strict_types=1);

namespace App\Kernel;

use Hyperf\Database\Commands\Ast\ModelUpdateVisitor as Visitor;

class ModelUpdateVisitor extends Visitor{
    /**
     * Used by `casts` attribute.
     */
    protected function formatDatabaseType(string $type): ?string
    {
        switch ($type) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return 'integer';
            case 'decimal':
                // 设置为 decimal，并设置对应精度
                return 'decimal:2';
            case 'float':
            case 'double':
            case 'real':
                return 'float';
            case 'json':
                return 'json';
            case 'bool':
            case 'boolean':
                return 'boolean';
            case 'timestamp':
                return 'datetime';
            default:
                return null;
        }
    }

    /**
     * Used by `@property` docs.
     */
    protected function formatPropertyType(string $type, ?string $cast): ?string
    {
        if (! isset($cast)) {
            $cast = $this->formatDatabaseType($type) ?? 'string';
        }

        switch ($cast) {
            case 'integer':
                return 'int';
            case 'date':
            case 'timestamp':
            case 'datetime':
                return '\Carbon\Carbon';
            case 'json':
                return 'array';
        }

//        if (Str::startsWith($cast, 'decimal')) {
//            // 如果 cast 为 decimal，则 @property 改为 string
//            return 'string';
//        }

        return $cast;
    }
}