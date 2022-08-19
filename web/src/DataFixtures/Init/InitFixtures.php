<?php

namespace App\DataFixtures\Init;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

abstract class InitFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['init'];
    }
}
