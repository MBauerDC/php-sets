<?php

namespace MBauer\PhpSets\test\acceptance;

use MBauer\PhpSets\implementations\GenericElement;
use MBauer\PhpSets\implementations\GenericSet;
use PHPUnit\Framework\TestCase;

class LargeSetOperationsTest extends TestCase
{
    protected int $cores;

    public function setUp(): void
    {
        $this->cores = $this->getNumberOfCPUs();
    }

    public function testIntersection1000With1000Match100(): void
    {
        $arr1 = [];
        $arr3 = [];
        for ($i = 0; $i < 900; $i++) {
            $arr1[] = new GenericElement((string)$i, (string)$i);
        }
        for ($i = 900; $i < 1000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData, $idData);
            $arr1[] = $el;
            $arr3[] = $el;
        }
        for ($i = 1000; $i < 1900; $i++) {
            $arr3[] = new GenericElement($idData, $idData);
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr3);
        $intersection = $set1->intersectWith($set2);
        $this->assertCount(100, $intersection, 'Intersection must work for two sets of 1000 with 100 overlapping.');
    }

    public function testUnion1000With1000(): void
    {
        $arr1 = [];
        $arr2 = [];
        for ($i = 0; $i < 2000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData,$idData);
            if ($i < 1000) {
                $arr1[] = $el;
            } else {
                $arr2[] = $el;
            }
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr2);
        $intersection = $set1->unionWith($set2);
        $this->assertCount(2000, $intersection, 'Union must work for two sets of 1000.');
    }

    public function testSymmetricDifference1000With1000Match100(): void
    {
        $arr1 = [];
        $arr3 = [];
        for ($i = 0; $i < 900; $i++) {
            $arr1[] = new GenericElement((string)$i, (string)$i);
        }
        for ($i = 900; $i < 1000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData, $idData);
            $arr1[] = $el;
            $arr3[] = $el;
        }
        for ($i = 1000; $i < 1900; $i++) {
            $idData = (string)$i;
            $arr3[] = new GenericElement($idData, $idData);
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr3);
        $intersection = $set1->symmetricDifferenceWith($set2);
        $this->assertCount(1800, $intersection, 'Symmetric difference must work for two sets of 1000 with 100 overlapping.');
    }

    public function testIntersection10000With10000Match1000(): void
    {
        $arr1 = [];
        $arr3 = [];
        for ($i = 0; $i < 9000; $i++) {
            $arr1[] = new GenericElement((string)$i, (string)$i);
        }
        for ($i = 9000; $i < 10000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData, $idData);
            $arr1[] = $el;
            $arr3[] = $el;
        }
        for ($i = 10000; $i < 19000; $i++) {
            $arr3[] = new GenericElement($idData, $idData);
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr3);
        $intersection = $set1->intersectWith($set2);
        $this->assertCount(1000, $intersection, 'Intersection must work for two sets of 10000 with 1000 overlapping.');
    }

    public function testUnion10000With10000(): void
    {
        $arr1 = [];
        $arr2 = [];
        for ($i = 0; $i < 20000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData,$idData);
            if ($i < 10000) {
                $arr1[] = $el;
            } else {
                $arr2[] = $el;
            }
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr2);
        $intersection = $set1->unionWith($set2);
        $this->assertCount(20000, $intersection, 'Union must work for two sets of 10000.');
    }

    public function testSymmetricDifference10000With10000Match1000(): void
    {
        $arr1 = [];
        $arr3 = [];
        for ($i = 0; $i < 9000; $i++) {
            $arr1[] = new GenericElement((string)$i, (string)$i);
        }
        for ($i = 9000; $i < 10000; $i++) {
            $idData = (string)$i;
            $el = new GenericElement($idData, $idData);
            $arr1[] = $el;
            $arr3[] = $el;
        }
        for ($i = 10000; $i < 19000; $i++) {
            $idData = (string)$i;
            $arr3[] = new GenericElement($idData, $idData);
        }
        $set1 = new GenericSet(...$arr1);
        $set2 = new GenericSet(...$arr3);
        $intersection = $set1->symmetricDifferenceWith($set2);
        $this->assertCount(18000, $intersection, 'Symmetric difference must work for two sets of 10000 with 1000 overlapping.');
        unset($arr1, $arr3, $set1, $set2, $intersection);
    }

    public function testTimeIntersect100SetsOf1000With2MatchingIsBelow8msDividedByCores(): void
    {
        $limitMS = 8 / $this->cores;
        $sets = [];
        $commonEl1 = new GenericElement('x', 'x');
        $commonEl2 = new GenericElement('y', 'y');
        for ($i = 0; $i < 100; $i++) {
            $currEls = [];
            for ($j = 0; $j < 998; $j++) {
                $idData = $i . '|' . $j;
                $currEls[] = new GenericElement($idData, $idData);
            }
            $sets[] = new GenericSet($commonEl1, ...[...$currEls, $commonEl2]);
        }
        /**
         * @var GenericSet $firstSet
         */
        $firstSet = array_shift($sets);
        $start = hrtime(true);
        $intersection = $firstSet->intersectWith(...$sets);
        $durationNS = hrtime(true) - $start;
        $durationMS = $durationNS / 1000 / 1000;
        echo PHP_EOL . " Intersection 100 sets with 1000 Elements matching 2 took [$durationMS] ms (limit $limitMS)." . PHP_EOL;
        $this->assertLessThan($limitMS, $durationMS, 'Intersection of 100 sets with 1000 Elements matchin 2 must take less than ' . $limitMS . ' ms.');
        unset($firstSet, $intersection, $sets);
    }

    public function testTimeIntersect1000SetsOf1000With20MatchingIsBelow80msDividedByCores(): void
    {
        $limitMS = 80 / $this->cores;
        $sets = [];
        $commonEls = [];
        for ($k = 0; $k < 20; $k++) {
            $commonEls[] = new GenericElement('c_' . $k, 'c_' . $k);
        }
        for ($i = 0; $i < 1000; $i++) {
            $currEls = [];
            for ($j = 0; $j < 998; $j++) {
                $idData = $i . '|' . $j;
                $currEls[] = new GenericElement($idData, $idData);
            }
            $sets[] = new GenericSet(...$commonEls, ...$currEls);
        }
        /**
         * @var GenericSet $firstSet
         */
        $firstSet = array_shift($sets);
        $start = hrtime(true);
        $intersection = $firstSet->intersectWith(...$sets);
        $durationNS = hrtime(true) - $start;
        $durationMS = $durationNS / 1000 / 1000;
        echo PHP_EOL . " Intersection 1000 sets with 1000 Elements matching 20 took [$durationMS] ms (limit $limitMS)." . PHP_EOL;
        $this->assertLessThan($limitMS, $durationMS, 'Intersection of 1000 sets with 1000 Elements matchin 20 must take less than ' . $limitMS . 'ms.');
    }


    function getNumberOfCPUs(): int {
        $ans = 1;
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $ans = count($matches[0]);
        } else if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            $process = @popen('wmic cpu get NumberOfCores', 'rb');
            if (false !== $process) {
                fgets($process);
                $ans = intval(fgets($process));
                pclose($process);
            }
        } else {
            $ps = @popen('sysctl -a', 'rb');
            if (false !== $ps) {
                $output = stream_get_contents($ps);
                preg_match('/hw.ncpu: (\d+)/', $output, $matches);
                if ($matches) {
                    $ans = intval($matches[1][0]);
                }
                pclose($ps);
            }
        }
        return $ans;
    }

}
