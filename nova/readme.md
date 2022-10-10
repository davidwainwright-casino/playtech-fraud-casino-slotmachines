# wut
cause you may have a legit license and want to wrap it inside your own packages. 
not sure if it's safe to use this package cause retrieved from dns poisioned machine, so use at your own risk. 

set some random license characters in your .env or config/nova.php

To change yourself, simply install nova.laravel.com through composer (license needed to download) and change within **src/Nova.php**:

```php
    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return bool
     */
    public static function checkLicenseValidity()
    {
        return Cache::remember('nova_valid_license_key', 3600, function () {
            return rescue(function () {
                return static::checkLicense()->status() == 204;
            }, false);
        });
    }

    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public static function checkLicense()
    {
        return Http::post('https://nova.laravel.com/api/license-check', [
            'url' => request()->getHost(),
            'key' => config('nova.license_key', ''),
        ]);
    }
    
```

to:


```php

    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return bool
     */
    public static function checkLicenseValidity()
    {
        return Cache::remember('nova_valid_license_key', 3600, function () {
            return rescue(function () {
                return static::checkLicense()->status() != 204;
            }, false);
        });
    }

    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public static function checkLicense()
    {
        return Http::post('https://nova.laravel.com', [
            'url' => request()->getHost(),
            'key' => config('nova.license_key', ''),
        ]);
    }
 ```

## Laravel Nova

- [Website](https://nova.laravel.com)
- [Releases](https://nova.laravel.com/releases)
- [Documentation](https://nova.laravel.com/docs)
  - [Installation](https://nova.laravel.com/docs/3.0/installation.html)
  - [Updating Nova](https://nova.laravel.com/docs/3.0/installation.html#updating-nova)
- [Nova Packages](https://novapackages.com)

## Upgrade Guide

- Copy the `Main` dashboard to your codebase
- Delete the `cards` method from your `NovaServiceProvider`
- Action `fields()` method changed to `fields(NovaRequest $request)`

### Modals

If you have a custom modal, make sure to add the `<teleport to="#modals">` component as the root level.
