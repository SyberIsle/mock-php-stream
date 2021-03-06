# php:// Mock Library

[![Travis CI](https://secure.travis-ci.org/SyberIsle/mock-php-stream.png)](https://travis-ci.org/SyberIsle/mock-php-stream) [![Code Climate](https://codeclimate.com/github/SyberIsle/mock-php-stream/badges/gpa.svg)](https://codeclimate.com/github/SyberIsle/mock-php-stream)

This library is intended to be used to mock the `php://` stream wrapper.

It's highly recommended that you register/unregister this ONLY when you need it in a test. Otherwise it could interfere
with other built-in php streams during the course of it being registered.

## Installation

`composer install syberisle/mock-php-stream`

## Usage

Using this to test a Slim 3 app's ability to do direct file uploads, where the body IS the content, is
now easier. 

```php
MockPhpStream::register();
file_put_contents('php://input', 'you test data');

$app = new Slim\App();
$app->post('', function ($request) {
    // direct file uploads
    $request->getBody()->detach();
    $from = fopen("php://input", 'r');
    $to   = fopen('/my/path/file', 'w');
    stream_copy_to_stream($from, $to);
    fclose($from);
    fclose($to);
});

MockPhpStream::unregister();
```

## Security

Using this in production could potentially cause problems as it overrides the built-in php stream functions.

## Credits

The idea was based off on this blog article [Mocking php://input](http://news-from-the-basement.blogspot.com/2011/07/mocking-phpinput.html).

- [David Lundgren](https://github.com/dlundgren)
- [All Contributors](https://github.com/syberisle/pipeline/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.