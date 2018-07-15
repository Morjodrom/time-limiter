<?php

namespace LoopLimiter;


class TimeLimiter implements \Iterator
{
    const DEFAULT_TIME_UP_SECONDS = 3;

    /**
     * @var int - timestamp threshold to stop
     */
    private $timesUp;


    /**
     * LoopManager constructor.
     *
     * @param int      $limitSeconds - seconds to process
     * @param int      $timeUpSeconds - seconds to stop before the end
     * @param int|null $startTimestamp - $_SERVER['REQUEST_TIME'] by default
     */
    public function __construct(int $limitSeconds, int $timeUpSeconds = self::DEFAULT_TIME_UP_SECONDS, int $startTimestamp = null) {
        if($limitSeconds === 0) {
            $this->timesUp = INF;
        }
        else {
            $startTimestamp = $startTimestamp ?? (int) $_SERVER['REQUEST_TIME'];
            $this->timesUp = $startTimestamp - $timeUpSeconds + $limitSeconds;
        }
    }


    /**
     * Return current time left
     * @return float
     */
    public function current(): float {
        if($this->timesUp === INF){
            return $this->timesUp;
        }
        $now = time();
        return $this->timesUp - $now;
    }





    /**
     * Checks if there is time to process left
     * @return boolean
     */
    public function valid(): bool {
        return $this->current() > 0;
    }

    /**
     * Move forward to next element
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * Return the key of the current element
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * Rewind the Iterator to the first element
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}