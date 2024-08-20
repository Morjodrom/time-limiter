# Time Limiter
A small utility class to help to limit script execution time.

# Basic usage
The class is usually used within loops with heavy time consumption.

``` injectablephp
// maximum time to execute one request
$REQUEST_TIMEOUT_SEC = 5; 

$curlClient = new SomeCurlClient(); // just an example.

// shared hosting often has a limit. E.g. 30 seconds
$maxExecutionTime = ini_get('max_execution_time'); 
$timeLimiter = new \timelimiter\TimeLimiter($maxExecutionTime, $REQUEST_TIMEOUT_SEC);

// check if there is time left to prevent 504 timeout
// recommended 
foreach($timeLimiter => $timeLeft){
  $result = $curlClient->doSomeHeavyJob([
      'timeout' => $REQUEST_TIMEOUT_SEC
  ]);
  // handle the result
  // ...
}

// or alternatively while loop might be used with respective Iterator calls.
while($timeLimiter->valid()){ 
  $result = $curlClient->doSomeHeavyJob([
      'timeout' => $REQUEST_TIMEOUT_SEC
  ]);
  $timeLimiter->next(); // must be called to adapt to long iterations
}
```

# Installation
As a production dependency using [Composer](https://getcomposer.org/):

    composer require morjodrom/time-limiter

If you only need this class during development, you should add it as a dev-dependency:

    composer require --dev morjodrom/time-limiter

# Constructor options
``int $limitSeconds`` - seconds to process. 0 equals to no limit.
Value from ``ini_get('max_execution_time')`` might be the desired option.

``[int $preliminaryTimeout] = DEFAULT_TIME_UP_SECONDS = 3`` seconds to stop execution preliminary
before reaching the timeout.
``$preliminaryTimeout`` must be a bit greater that the longest *theoretical* operation you perform in a loop. 
Therefore, the last risky operation that might exceed the execution time is omitted.
The class tracks the time spend on each iteration to update $preliminaryTimeout to equal the longest operation


* ``[int|null $startTimestamp = $_SERVER['REQUEST_TIME']`` is used by default. Must be a timestamp
since Unix Epoch (January 1 1970 00:00:00 GMT), e.g. ``time()`` call.

It is highly encouraged to use ``foreach`` construction, though raw Iteration via while also is 
possible with correct Iterator calls
# Methods
``current(): int`` - returns number of seconds left before the timeout 
``valid(): bool`` - returns if there is time left to perform the script safely
``next(): void`` - must be called after a finished iteration to adapt to unexpected long iterations

# Support
Feel free to open an issue: <https://github.com/Morjodrom/time-limiter/issues>

# License - MIT