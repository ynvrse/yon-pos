<?php

namespace App\Services;

use FeatureNinja\Cva\ClassVarianceAuthority;

class AlertCvaService
{
    public static function new(): ClassVarianceAuthority
    {
        return ClassVarianceAuthority::new(
            [
                'relative w-full rounded-lg border px-4 py-3 text-sm [&>svg+div]:translate-y-[-3px] [&>svg]:absolute [&>svg]:left-4 [&>svg]:top-4 [&>svg]:text-foreground [&>svg~*]:pl-7',
            ],
            [
                'variants' => [
                    'variant' => [
                        'default' => 'bg-green-100 text-green-700',
                        'warning' => 'bg-yellow-100 text-yellow-700',
                        'info' => 'bg-blue-100 text-blue-700',
                        'destructive' => 'border-red-500 bg-red-100 text-red-700 dark:border-red-700 [&>svg]:text-red-700',
                    ],
                ],
                'defaultVariants' => [
                    'variant' => 'default',
                ],
            ],
        );
    }
}
