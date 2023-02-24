<?php

namespace Statamic\GraphQL\Middleware;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Validation\ValidationException;
use Rebing\GraphQL\Support\Middleware;
use Statamic\Support\Str;

class AllowedFilters extends Middleware
{
    public function handle($root, $args, $context, ResolveInfo $info, Closure $next)
    {
        $allowedFilters = $root->allowedFilters($args);

        $forbidden = collect($args['filter'] ?? [])
            ->keys()
            ->filter(fn ($filter) => ! $allowedFilters->contains($filter));

        if ($forbidden->isNotEmpty()) {
            throw ValidationException::withMessages([
                'filter' => Str::plural('Forbidden filter', $forbidden).': '.$forbidden->join(', '),
            ]);
        }

        return $next($root, $args, $context, $info);
    }
}
