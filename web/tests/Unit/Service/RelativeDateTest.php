<?php

namespace App\Tests\Unit\Service;

use App\Service\RelativeDate;
use DateTime;
use PHPUnit\Framework\TestCase;

class RelativeDateTest extends TestCase
{
    public function testRelativeDateReturnWaitedResult(): void
    {
        $relativeDate = new RelativeDate();

        $resultYear = $relativeDate->turnDateToStringRelative(new DateTime("+ 2 years"));
        $this->assertSame("1 ans", $resultYear);

        $resultMonth = $relativeDate->turnDateToStringRelative(new DateTime("+ 2 months"));
        $this->assertSame("1 mois", $resultMonth);

        $resultD = $relativeDate->turnDateToStringRelative(new DateTime("+ 2 days"));
        $this->assertSame("1 jours", $resultD);

        $resultH = $relativeDate->turnDateToStringRelative(new DateTime("+ 2 hours"));
        $this->assertSame("1h", $resultH);

        $resultM = $relativeDate->turnDateToStringRelative(new DateTime("+ 2 minutes"));
        $this->assertSame("1min", $resultM);
    }
}
