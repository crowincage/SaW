<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class ATest
 *
 * @package App\Tests
 * @author Christian Ruppel < post@christianruppel.de >
 */
abstract class ATest extends TestCase
{

    /**
     * Invoke a protected or private method
     *
     * @param $object
     * @param $methodName
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    public function getEntityManager ()
    {
        return $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getLogger ()
    {
        return $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
