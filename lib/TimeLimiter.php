<?php

/**
 * @noinspection AccessModifierPresentedInspection
 */

namespace timelimiter;

use ReturnTypeWillChange;

/**
 * Class TimeLimiter
 *
 * Helps to limit script execution time for time-heavy operations.
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
    protected $lastExecutionTime = 0;

    /**
     * @var int
     */
    protected $maxExecutionTimeSec;


    /**
     * TimeLimiter constructor.
     *
     * @param int $maxExecutionTimeSec - maximum seconds to execute. 0 defaults INF. Can be used with ``ini_get('max_execution_time')``.
     * @param float $preliminaryTimeoutSec - longest possible iteration length to prevent a shutdown during the last iteration. The value is rounded up
     * @param int $startTimestamp - timestamp of the iteration start. Defaults to $_SERVER['REQUEST_TIME']
     *
     * @see INF
     */
    public function __construct(int $maxExecutionTimeSec = 0, float $preliminaryTimeoutSec = self::DEFAULT_TIME_UP_SECONDS, int $startTimestamp = 0)
    {
        $this->startTimestamp = $startTimestamp ?: $_SERVER['REQUEST_TIME'];
        $this->maxExecutionTimeSec = $maxExecutionTimeSec > 0 ? $maxExecutionTimeSec : INF;
        $this->setPreliminaryTimeoutSec($preliminaryTimeoutSec);
    }

    /**
     * Return seconds left
     *
     * @return float
     */
    #[ReturnTypeWillChange]
    public function current(): float
    {
        return $this->startTimestamp + $this->maxExecutionTimeSec - time();
    }

    /**
     * Checks if there is time to process left
     * @return boolean
     */
    public function valid(): bool
    {
        return $this->current() - $this->preliminaryTimeoutSec > 0;
    }

    #[ReturnTypeWillChange]
    public function next()
    {
        $iterationDuration = time() - $this->lastExecutionTime;
        $isRiskyIteration = $iterationDuration > $this->preliminaryTimeoutSec;
        if ($isRiskyIteration) {
            $this->setPreliminaryTimeoutSec($iterationDuration);
        }

        $this->lastExecutionTime = time();
    }

    public function key(): int
    {
        return time();
    }

    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->lastExecutionTime = time();
    }
}