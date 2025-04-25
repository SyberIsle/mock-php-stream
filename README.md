# php:// Mock Library

![Tests](https://github.com/syberisle/mock-php-stream/actions/workflows/tests.yml/badge.svg?event=push)

This library is intended to be used to mock the `php://` stream wrapper.

It's highly recommended that you register/unregister this ONLY when you need it in a test. Otherwise, it could interfere
with other built-in php streams during the course of it being registered.

**NOTE** Since version 2.0 the `php://temp[/maxmemory:NN]` and `php://memory` paths will behave as PHP does. The content
written to them will not be available after the `stream_close` call. This means that `file_put_contents` and  
`file_get_contents` cannot be used to read and write these two paths.

## Installation

`composer install syberisle/mock-php-stream`

## Usage

Using this to test a Slim 3 app's ability to do direct file uploads, where the body IS the content, is now easier. 

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