<?php

declare(strict_types=1);

namespace App\Tests\Unit\Identity;

use PHPUnit\Framework\TestCase;
use StrictlyPHP\Value\Implementation\Identity\IdFactory;

class IdFactoryTest extends TestCase
{
    private IdFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new IdFactory();
    }

    public function testGenerateNewUuId(): void
    {
        $id1 = $this->factory->generateNewUuId();
        $id2 = $this->factory->generateNewUuId();
        self::assertNotEquals($id1, $id2);
    }
}
