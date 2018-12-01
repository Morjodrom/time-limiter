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
        $this->assertInternalType('int', $secondsLeft);
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
        $this->assertLessThanOrEqual(0, $lateCheck, 'After timeout there must be only time exceeded');
		$this->assertFalse($timeLimiter->valid(), 'Must be invalid after time spent');
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
		$this->assertSame(INF, $timeLimiter->current(), '');
	}

}