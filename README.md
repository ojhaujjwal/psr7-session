PSR-7 Session
===================
[![Build Status][travis-image]][travis-url]
[![Coverage Status][coverage-image]][coverage-url]
[![Latest Stable Version](https://poser.pugx.org/ujjwal/psr7-http-session/v/stable)](https://packagist.org/packages/ujjwal/psr7-http-session)
[![Total Downloads](https://poser.pugx.org/ujjwal/psr7-http-session/downloads)](https://packagist.org/packages/ujjwal/psr7-http-session)
[![Latest Unstable Version](https://poser.pugx.org/ujjwal/psr7-http-session/v/unstable)](https://packagist.org/packages/ujjwal/psr7-http-session)
[![License](https://poser.pugx.org/ujjwal/psr7-http-session/license)](https://packagist.org/packages/ujjwal/psr7-http-session)

Alternative to PHP's native session handler. It does not depend on PHP's session capability. It can be used with non-typical php based applications like with [react/http](https://github.com/reactphp/http).

### But, why?
![But, why?](http://vignette2.wikia.nocookie.net/vampirediaries/images/c/ca/But-why-meme-generator-but-why-84103d.jpg/revision/latest?cb=20130811194815])
- You don't have to depend on `session_` functions which means you can write testable code.
- You don't have to depend on `$_SESSION` superglobal allowing you to write more testable code. 
- You can even use this for non-typical php based applications like with [react/http](https://github.com/reactphp/http).
- You can create a framework agnostic library/module depending on [psr-7](http://www.php-fig.org/psr/psr-7/) HTTP message interfaces and this session library.

## Getting started

```php
$sessionOptions = [
    'name' => 'session_id',
    'sid_length' => 40,
    'cookie' => [
        'domain' => 'your-app.com',
    ]
];

$sessionHandler = new Ojhaujjwal\Session\Handler\FileHandler('path/to/session-data');
$sessionManager = new Ojhaujjwal\Session\SessionManager(
    $sessionHandler,
    $request,
    $sessionOptions
);
$storage = $sessionManager->getStorage();

$sessionManager->start();

// you can manipulate $storage just like $_SESSION   
$storage['some_key'] = 'some_value';
$someKey = $storage['some_key'];

$response = $sessionManager->close($response);
//return the response the the client
```

### Installation
`composer require ujjwal/psr7-http-session`

### Session Options

##### name
Type: string
Required: true

Name of the session which is used as cookie name. It should only contain alphanumeric characters.


#### sid_length
Type: integer
Default: 40

the length of session ID string. Session ID length can be between 22 to 256.

#### cookie
Type: array

Used to pass cookie options. See cookie options section.

### Cookie Options

#### domain
Type: string
Default: derived from the `Host` header of request

domain to be set in the session cookie. 

#### path
Type: string
Default: `/`

path to be set in the session cookie.

#### http_only
Type: boolean
Default: `true`

Marks the cookie as accessible only through the HTTP protocol. This means that the cookie won't be accessible by scripting languages, such as JavaScript.

#### secure_only
Type: boolean
Default: True if the original request is https

It indicates whether cookies should only be sent over secure connections.

#### lifetime
Type: integer
Default: `0` for session cookie

It specifies the lifetime of the cookie in seconds which is sent to the browser. The value 0 means "until the browser is closed." Defaults to 0

#### same_site
Type: string
Default: `Lax`
Specifies `SameSite` cookie attribute. Very useful to mitigate CSRF by preventing the browser from sending this cookie along with cross-site requests.
Allowed values:
* empty string for not setting the attribute
* `ParagonIE\Cookie\Cookie::SAME_SITE_RESTRICTION_LAX`(fairly strict)
* `ParagonIE\Cookie\Cookie::SAME_SITE_RESTRICTION_STRICT`(very strict) 

### Basic operations
#### Initializing SessionManager
```php
$sessionManager = new Ojhaujjwal\Session\SessionManager(
    $sessionHandler,
    $request,
    $sessionOptions
);
```

#### Starting session
```php
$sessionManager->start();

$sessionManager->isStarted(); // returns true
```

#### Retrieve session id
```php
$sessionManager->getId(); //returns alphanumeric string
```

#### Regenerate session id
```php
$sessionManager->regenerate();

$sessionManager->regenerate(false); // does not destroy old session
```

#### Close session and write to response header as cookie 
```php
$response = $sessionManager->close($response);
```

#### Retrieving session storage 
```php
$storage = $sessionManager->getStorage();
```
It implements `IteratorAggregate`, `ArrayAccess`, `Countable`
So, it will look very much like `$_SESSION`. 
Just replace the `$_SESSION` occurrences in your app with instance of the object.

#### Write to session 
```php
$storage->abcd = 'efgh';
//or
$storage['abcd'] = 'efgh';
//or
$storage->set('abcd', 'efgh');
```

#### Read from session 
```php
$abcd =  $storage->abc;
//or
$abcd = $storage['abcd'];
//or
$abcd = $storage->get('abcd');
```

#### Remove from session 
```php
unset($storage->abc);
//or
unset($storage['abcd']);
//or
$storage->remove('abcd');
```

#### Flush session data
```php
$storage->flush();
```

### Session Middleware
It also comes with a http middleware which you can use to automatically initialize session and write cookie to response.
The middleware is compatible with `http-interop/http-middleware` based single pass approach or express-like double pass approach.  

```php
 $middleware = new Ojhaujjwal\Session\SessionMiddleware($handler, $sessionOptions);
 $middleware->process($request, $delegate);
 // or
 $middleware($request, $response, $next);
 
 //using with zend-expressive
 //after errorhandler and before the routing middleware
 $app->pipe(\Ojhaujjwal\Session\SessionMiddleware::class);
```
 
### TODO 
- [ ] Fix build in php7.2
- [ ] Garbage collection
- [ ] Cookie Based session handler
- [ ] Encryption Session Handler
 
 
## License

[MIT](LICENSE)

[travis-image]: https://travis-ci.org/ojhaujjwal/psr7-session.svg?branch=master
[travis-url]: https://travis-ci.org/ojhaujjwal/psr7-session
[coverage-image]: https://coveralls.io/repos/github/ojhaujjwal/psr7-session/badge.svg?branch=master
[coverage-url]: https://coveralls.io/github/ojhaujjwal/psr7-session?branch=master
