PSR-7 Session
===================

Alternative to PHP's native session handler. It does not depend on PHP's session capability.

## Getting started

```php
    $sessionOptions = [
        'name' => 'session_id',
        'sid_length' => 40,
        'cookie' => [
            'domain' => 'your-app.com',
        ]
    ];

    $sessionHandler = new Ojhaujjwal\Session\FileSessionHandler('path/to/session-data');
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
    * empty string
    * `ParagonIE\Cookie\Cookie::SAME_SITE_RESTRICTION_LAX`
    * `ParagonIE\Cookie\Cookie::SAME_SITE_RESTRICTION_STRICT` 

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
$storage->set('abcd', 'efgh';
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
 
### TODO 
- [ ] Unit tests
- [ ] Garbage collection
 