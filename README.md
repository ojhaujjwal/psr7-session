PSR-7 Session
===================

Alternative to PHP's native session handler. It does not depend on PHP's session capability.

##Docs

```php   
    $sessionHandler = new Ojhaujjwal\Session\FileSessionHandler('path/to/session-data');
    $sessionManager = new Ojhaujjwal\Session\SessionManager(
        $this->sessionHandler,
        $request,
        [
            'name' => 'session_id',
        ]
    );
    $storage = $sessionManager->getStorage();
    
    $sessionManager->start();
       
    // you can manipulate $storage just like $_SESSION   
    $storage['some_key'] = 'some_value';
    $someKey = $storage['some_key'];
    
    $response = $sessionManager->close($response);
    //return the response the the client
```
