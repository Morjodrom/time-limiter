<?php

namespace timelimiter;

/**
 * Class TimeLimiterTest
 *
 * @covers TimeLimiter
 */
class TimeLimiterTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @covers TimeLimiter::valid()
     * @covers \timelimiter\TimeLimiter::current
	 */
	public function testCurrent() {
		$limit = 6;
		$safeTime = 3;
        $preliminaryStopTime = $limit - $safeTime;
		$timeLimiter = new TimeLimiter($limit, $safeTime, time());
		$secondsLeft = $timeLimiter->current();
		$this->assertGreaterThanOrEqual($preliminaryStopTime, $secondsLeft, 'There must be time left before the actual timeout');
	}

	/**
	 * @covers TimeLimiter::valid()
	 */
	public function testValid() {
		$limit = 2;
		$safeTime = 1;
		$timeLimiter = new TimeLimiter($limit, $safeTime, time());
        $initialCheck = $timeLimiter->current();
        $this->assertGreaterThan(0, $initialCheck, 'Before timeout there must be time to process');
		$this->assertTrue($timeLimiter->valid(), 'Must be valid right the moment after creation');
		sleep($limit - $safeTime);
        $lateCheck = $timeLimiter->current();
        $this->assertGreaterThan(0, $lateCheck, 'After timeout there must be time left');
		$this->assertFalse($timeLimiter->valid(), 'But any further operation must be invalid');
	}

    /**
     * @covers \timelimiter\TimeLimiter::current
     * @covers \timelimiter\TimeLimiter::valid
     * @covers \timelimiter\TimeLimiter::__construct
     */
	public function testValidInfinity() {
	    $limitlessExecutionTimeParam = 0;
		$timeLimiter = new TimeLimiter($limitlessExecutionTimeParam);
		$this->assertTrue($timeLimiter->valid());
		$this->assertSame(INF, $timeLimiter->current(), 'The execution must be infinite');
	}

    public function testIteration() {
        $limit = 2;
        $safeTime = 1;
        $limiter = new TimeLimiter($limit, $safeTime, time());
        $count = 0;
        foreach ($limiter as $time => $left){
            usleep(1);
            $count++;
        }

        $this->assertGreaterThan(0, $count, 'Limiter must iterate at least once');
        $this->assertFalse($limiter->valid(), 'Limiter must be invalid after iteration');
        $limiter->rewind();
        $this->assertFalse($limiter->valid(), 'Limiter invalid state must persist');
    }

    public function testIterationOnBoundaryValues()
    {
        $maxExecutionTimeSec = 2;
        $longestIteration = 1;
        $limiter = new TimeLimiter($maxExecutionTimeSec, $longestIteration, time());
        $count = 0;
        foreach ($limiter as $left){
            sleep($longestIteration);
            $count++;
        }

        $this->assertGreaterThan(0, $count, 'Limiter must iterate at least once');
        $this->assertGreaterThan(0, $limiter->current(), 'Limiter must have a safety gap');
    }


    public function testAdaptiveLimit()
    {
        $longIteration = 3;
        $shortIteration = 1;
        $riskyIteration = $longIteration;
        $iterations = [$shortIteration, $longIteration, $shortIteration, $riskyIteration];
        $maxExecutionTimeSec = array_sum($iterations);

        $iterationMaxDurationS = $shortIteration;

        $limiter = new TimeLimiter($maxExecutionTimeSec, $iterationMaxDurationS, time());
        $iterationIndex = 0;
        foreach ($limiter as $left){
            sleep($iterations[$iterationIndex] ?: $shortIteration);
            $iterationIndex++;
        }
        $this->assertEquals(count($iterations) - 1, $iterationIndex, 'The last risky operation must be skipped');
        $this->assertGreaterThan(0, $limiter->current(), 'Must not exceed on variable duration');
    }

}