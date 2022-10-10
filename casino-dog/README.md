## Installation


##

game messenger;

function GameMessenger( event_callbacks, origin ) {
    /**
     * if origin === undefined, listens messages from all domains
     * if origin !== undefined, listens messages only origin(s) domain(s), additional origin you can added from method .addOrigin(origin)
     */
    if ( origin !== undefined ) {
        this.origins = [];
        this.origins.push( origin );
    }
    this.events = event_callbacks;
    this.listenPostMessages();
}

GameMessenger.prototype.listenPostMessages = function () {
    var self = this;
    if ( typeof window.addEventListener !== 'undefined' ) {
        window.addEventListener( 'message', function ( e ) {
            if ( self.origins && self.origins.indexOf( e.origin ) === -1 ) {
                return;
            }
            try {
                var data = JSON.parse( e.data );
            } catch ( e ) {
                return false;
            }
            if ( self.events[data.event] ) {
                self.events[data.event](data.data || '', data.viewid || 'empty');
            }
        }, false );
    } else if ( typeof window.attachEvent !== 'undefined' ) {
        window.attachEvent( 'onmessage', function ( e ) {
            if ( self.origins && self.origins.indexOf( e.origin ) === -1 ) {
                return;
            }
            try {
                var data = JSON.parse( e.data );
            } catch ( e ) {
                return false;
            }
            if ( self.events[data.event] ) {
                self.events[data.event](data.data || '', data.viewid || 'empty');
            }
        } );
    }
};

GameMessenger.prototype.addOrigin = function ( origin ) {
    if ( this.origins === undefined ) {
        this.origins = [];
    }
    this.origins.push( origin );
};

## 


You can install the package via composer:
```bash
composer require wainwright/casino-dog
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="casino-dog-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="casino-dog-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="casino-dog-views"
```

## Usage

```php
$casinoDog = new Wainwright\CasinoDog();
echo $casinoDog->echoPhrase('Hello, Wainwright!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/ryandro/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ryanwest](https://github.com/ryandro)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
