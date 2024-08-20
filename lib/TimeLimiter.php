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
     * @var int
     */
    protected $startTimestamp;

    /**
     * @var int - must be integer as is compared against integer-only time()
     * @see time()
     */
    protected $preliminaryTimeoutSec;

    public function setPreliminaryTimeoutSec(float $preliminaryTimeoutSec)
    {
        $this->preliminaryTimeoutSec = ceil($preliminaryTimeoutSec);
    }

    /**
     * @var int
     */
    protected $limitSeconds;


    /**
     * TimeLimiter constructor.
     *
     * @param int $maxExecutionTimeSec - seconds to process. 0 equals INF. Can be used with ``ini_get('max_execution_time')``.
     * @param float $preliminaryTimeoutSec - longest possible duration of an iteration to prevent risk of a shutdown during the last iteration. Value is rounded up
     * @param int|null $startTimestamp - timestamp of the iteration start. $_SERVER['REQUEST_TIME'] is used by default.
     *
     * @see INF
     */
    public function __construct(int $maxExecutionTimeSec = 0, float $preliminaryTimeoutSec = self::DEFAULT_TIME_UP_SECONDS, int $startTimestamp = null)
    {
        $this->startTimestamp = $startTimestamp ?: $_SERVER['REQUEST_TIME'];
        $this->limitSeconds = $maxExecutionTimeSec > 0 ? $maxExecutionTimeSec : INF;
        $this->setPreliminaryTimeoutSec($preliminaryTimeoutSec);
    }

    /**
     * Return seconds left
     *
     * @return int|float
     */
    public function current()
    {
        return $this->startTimestamp + $this->limitSeconds - time();
    }

    /**
     * Checks if there is time to process left
     * @return boolean
     */
    public function valid(): bool
    {
        return $this->current() - $this->preliminaryTimeoutSec > 0;
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