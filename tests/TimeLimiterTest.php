<?php

namespace LoopLimiter;

/**
 * Class TimeLimiterTest
 * @covers TimeLimiter
 */
class TimeLimiterTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @covers TimeLimiter::valid()
	 */
	public function testCurrent() {
		$limit = 6;
		$safeTime = TimeLimiter::DEFAULT_TIME_UP_SECONDS;
		$loopManager = new TimeLimiter($limit, $safeTime, time());
		$left = $loopManager->current();
		$this->assertGreaterThanOrEqual($limit - $safeTime, $left);
	}

	/**
	 * @covers TimeLimiter::valid()
	 */
	public function testValid() {
		$limit = 2;
		$safeTime = 1;
		$loopManager = new TimeLimiter($limit, $safeTime, time());
		$this->assertTrue($loopManager->valid(), 'Must be valid right the moment after creation');
		sleep($limit - $safeTime);
		$this->assertFalse($loopManager->valid(), 'Must invalidate after time spent');
	}

	public function testValidInfinity() {
		$loopManager = new TimeLimiter(0);
		$this->assertTrue($loopManager->valid());
		$this->assertSame(INF, $loopManager->current());
	}

}