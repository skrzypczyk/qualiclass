<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCanBeCreated()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('securePassword123');

        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('securePassword123', $user->getPassword());
    }

    public function testUserDefaultValues()
    {
        $user = new User();
        $this->assertFalse($user->isVerified());
    }
}
