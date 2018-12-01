<?php

namespace timelimiter;

/**
 * Class TimeLimiter
 *
 * @package timelimiter
 * @author Morjodrom
 */
class TimeLimiter
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
     * Can be used with ``ini_get('max_execution_time')`` for the first parameter.
     *
     * @param int      $limitSeconds - seconds to process. 0 equals to INF
     * @param int      $timeUpSeconds - seconds to stop preliminary before the timeout
     * @param int|null $startTimestamp - $_SERVER['REQUEST_TIME'] is used by default
     *
     * @see INF
     */
    public function __construct($limitSeconds, $timeUpSeconds = self::DEFAULT_TIME_UP_SECONDS, $startTimestamp = null) {

        if($limitSeconds === 0) {
            $this->timeout = INF;
        }
        else {
            $startTimestamp = $startTimestamp ?: (int) $_SERVER['REQUEST_TIME'];
            $this->timeout = $startTimestamp - $timeUpSeconds + $limitSeconds;
        }
    }


    /**
     * Return seconds left
     *
     * @return int|float
     */
    public function current() {
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
    public function valid() {
        return $this->current() > 0;
    }
}