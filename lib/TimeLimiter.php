<?php

namespace timelimiter;

/**
 * Class TimeLimiter
 *
 * A small utility class to help to limit script execution time.
 *
 * @package timelimiter
 * @author Morjodrom
 */
class TimeLimiter implements \Iterator
{
    /**
     * An opinionated amount of seconds to stop execution safely
     */
    const DEFAULT_TIME_UP_SECONDS = 3;

    /**
     * @var int - timestamp threshold to stop
     */
    private $timeout;


    /**
     * TimeLimiter constructor.
     *
     *
     * @param int $limitSeconds - seconds to process. 0 equals to INF.
     * Can be used with ``ini_get('max_execution_time')`` for the first parameter.
     * <b>Note!</b> You can freely pass any time just to limit the script execution
     * e.g. to send progress data to the client.
     * @param int $preliminaryTimeout - seconds to stop preliminary before the timeout.
     * ``$preliminaryTimeout`` must be a bit greater that the longest operation you perform in a loop
     * to prevent script fatal error with a timeout. E.g, a typical share hosting limits:
     * - max_execution_time is 30 seconds
     * - a heavy operation is a curl request with 5 seconds timeout
     * - script beginning and ending may take some time less than 1 second
     * So, the total time you have for execution is roughly:
     * 30 > 0.5 + 5n + 0.5
     * There is only 5.5 seconds left on some N iteration of curl and you risk to have a fatal error
     * if a next operation starts. So, we have to stop heavy code preliminary before the max execution
     * timeout:
     * 30 > 0.5 + 5n + preliminaryTimeout(5) + 0.5
     * @param int|null $startTimestamp - $_SERVER['REQUEST_TIME'] is used by default. Must be a timestamp
     * since Unix Epoch (January 1 1970 00:00:00 GMT).
     *
     *
     * @example
     * $REQUEST_TIMEOUT_SEC = 5; // maximum time to execute one request
     *
     * $curlClient = new SomeCurlClient();
     * $maxExecutionTime = ini_get('max_execution_time'); // shared hosting often has a limit. E.g. 30 seconds
     * $timeLimiter = new \timelimiter\TimeLimiter($maxExecutionTime, $REQUEST_TIMEOUT_SEC);
     *
     * while($timeLimiter->valid()){ // check if there is time left to prevent the script fail with 504 timeout
     * $result = $curlClient->doSomeHeavyJob([
     * 'timeout' => $REQUEST_TIMEOUT_SEC
     * ]);
     * // handle the result
     * // ...
     * }
     *
     * @see INF
     */
    public function __construct(int $limitSeconds, int $preliminaryTimeout = self::DEFAULT_TIME_UP_SECONDS, int $startTimestamp = null)
    {
        if($limitSeconds === 0) {
            $this->timeout = INF;
        }
        else {
            $startTimestamp = $startTimestamp ?: $_SERVER['REQUEST_TIME'];
            $this->timeout = $startTimestamp - $preliminaryTimeout + $limitSeconds;
        }
    }


    /**
     * Return seconds left
     *
     * @return int|float
     */
    public function current()
    {
        if($this->timeout === INF){
            return $this->timeout;
        }
        $now = time();
        return $this->timeout - $now;
    }

    /**
     * Checks if there is time to process left
     * @return boolean
     */
    public function valid(): bool
    {
        return $this->current() > 0;
    }

    public function next()
    {
        // not applicable
    }

    public function key()
    {
        return time();
    }

    public function rewind()
    {
        // must not rewind as limiter should work only for one request
    }
}