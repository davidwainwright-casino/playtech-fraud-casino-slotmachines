<?php

namespace Laravel\Nova;

use BackedEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Util
{
    /**
     * Determine if the given request is intended for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function isNovaRequest(Request $request)
    {
        $domain = config('nova.domain');
        $path = trim(Nova::path(), '/') ?: '/';

        if (! is_null($domain) && $domain !== config('app.url') && $path === '/') {
            if (! Str::startsWith($domain, ['http://', 'https://', '://'])) {
                $domain = $request->getScheme().'://'.$domain;
            }

            if (! in_array($port = $request->getPort(), [443, 80]) && ! Str::endsWith($domain, ":{$port}")) {
                $domain = $domain.':'.$port;
            }

            $uri = parse_url($domain);

            return isset($uri['port'])
                        ? rtrim($request->getHttpHost(), '/') === $uri['host'].':'.$uri['port']
                        : rtrim($request->getHttpHost(), '/') === $uri['host'];
        }

        return $request->is($path) ||
               $request->is(trim($path.'/*', '/')) ||
               $request->is('nova-api/*') ||
               $request->is('nova-vendor/*');
    }

    /**
     * Convert large integer higher than Number.MAX_SAFE_INTEGER to string.
     *
     * https://stackoverflow.com/questions/47188449/json-max-int-number/47188576
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function safeInt($value)
    {
        if (is_int($value) && $value >= 9007199254740991) {
            return (string) $value;
        }

        return $value;
    }

    /**
     * Resolve given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value)
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return value($value);
    }

    /**
     * Get the user model for Laravel Nova.
     *
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static function userModel()
    {
        $guard = config('nova.guard') ?: config('auth.defaults.guard');

        $provider = config("auth.guards.{$guard}.provider");

        return config("auth.providers.{$provider}.model");
    }

    /**
     * Get the session auth guard for the model.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model  $model
     * @return string|null
     */
    public static function sessionAuthGuardForModel($model)
    {
        if (is_object($model)) {
            $model = get_class($model);
        }

        $provider = collect(config('auth.providers'))->reject(function ($provider) use ($model) {
            return ! ($provider['driver'] === 'eloquent' && is_a($model, $provider['model'], true));
        })->keys()->first();

        return collect(config('auth.guards'))->reject(function ($guard) use ($provider) {
            return ! ($guard['driver'] === 'session' && $guard['provider'] === $provider);
        })->keys()->first();
    }

    /**
     * Get the dependent validation rules.
     *
     * @param  string  $attribute
     * @return array<string, string>
     *
     * @see \Illuminate\Validation\Validator::$dependentRules
     */
    public static function dependentRules($attribute)
    {
        return collect([
            'After',
            'AfterOrEqual',
            'Before',
            'BeforeOrEqual',
            'Confirmed',
            'Different',
            'ExcludeIf',
            'ExcludeUnless',
            'ExcludeWith',
            'ExcludeWithout',
            'Gt',
            'Gte',
            'Lt',
            'Lte',
            'AcceptedIf',
            'DeclinedIf',
            'RequiredIf',
            'RequiredUnless',
            'RequiredWith',
            'RequiredWithAll',
            'RequiredWithout',
            'RequiredWithoutAll',
            'Prohibited',
            'ProhibitedIf',
            'ProhibitedUnless',
            'Prohibits',
            'Same',
        ])->mapWithKeys(function ($rule) use ($attribute) {
            $rule = Str::snake($rule);

            return ["{$rule}:" => "{$rule}:{$attribute}."];
        })->all();
    }
}
