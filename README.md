# Time Limiter
A small utility class to help to limit script execution time.

# Basic usage
The class is usually used within loops with heavy time consumption.

```php
<?php
// maximum time to execute one request
$REQUEST_TIMEOUT_SEC = 5; 

$curlClient = new SomeCurlClient(); // just an example.

// shared hosting often has a limit. E.g. 30 seconds
$maxExecutionTime = ini_get('max_execution_time'); 
$timeLimiter = new \timelimiter\TimeLimiter($maxExecutionTime, $REQUEST_TIMEOUT_SEC);

// check if there is time left to prevent
// the script fail with 504 timeout
while($timeLimiter->valid()){ 
    $result = $curlClient->doSomeHeavyJob([
        'timeout' => $REQUEST_TIMEOUT_SEC
    ]);
    // handle the result
    // ...
}
```

# Installation
As a production dependency using [Composer](https://getcomposer.org/):

    composer require morjodrom/time-limiter

If you only need this class during development, you should add it as a dev-dependency:

    composer require --dev morjodrom/time-limiter

# Constructor options
``int $limitSeconds`` - seconds to process. 0 equals to INF.
Can be used with ``ini_get('max_execution_time')`` for the first parameter.
<b>Note!</b> You can freely pass any time just to limit the script execution
e.g. to send progress data to the client.

``[int $preliminaryTimeout] = DEFAULT_TIME_UP_SECONDS = 3`` - seconds to stop preliminary before the timeout.
``$preliminaryTimeout`` must be a bit greater that the longest operation you perform in a loop
to prevent script fatal error with a timeout. E.g, a typical share hosting limits:
- ``max_execution_time`` is 30 seconds
- a heavy operation is a curl request with 5 seconds timeout
- script beginning and ending may take some time less than 1 second

**So, the total time (in seconds) you have for execution is roughly:**

30 > 0.5 + 5n + 0.5

There is only 5.5 seconds left on some N iteration of curl and you risk to have a fatal error
if a next operation starts. So, we have to stop heavy code preliminary before the max execution
timeout:

30 > 0.5 + 5n + preliminaryTimeout(5) + 0.5

Having around 5.5 seconds reserved the script ends safely.

* ``[int|null $startTimestamp = $_SERVER['REQUEST_TIME']`` is used by default. Must be a timestamp
since Unix Epoch (January 1 1970 00:00:00 GMT).

# Methods
``current(): int`` - returns number of seconds left before the timeout 
``valid(): bool`` - returns if there is time left to perform the script safely

# Support
Feel free to open an issue: <https://github.com/Morjodrom/time-limiter/issues>

# License - MIT