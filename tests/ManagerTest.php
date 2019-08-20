<?php declare(strict_types=1);

namespace Tests;

use Phalcon\Events\EventsAwareInterface;
use Phlexus\Libraries\Auth\Manager;
use PHPUnit\Framework\TestCase;

final class ManagerTest extends TestCase
{
    public function testMockConstructor(): void
    {
        $managerMock = $this->createMock(Manager::class);

        $this->assertInstanceOf(EventsAwareInterface::class, $managerMock);
    }
}
