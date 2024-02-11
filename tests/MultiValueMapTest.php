<?php

namespace RestClient\Tests;

use PHPUnit\Framework\TestCase;
use RestClient\Util\MultiValueMap;

class MultiValueMapTest extends TestCase
{
    public function testCaseInsensitiveAdd(): void
    {
        $map = new MultiValueMap(true);

        $map->add('AbA', 'A')->add('bBb', 'B');

        $this->assertTrue($map->contains('aba'), 'Map does not contain key "AbA"');
        $this->assertTrue($map->contains('bbb'), 'Map does not contain key "bbb"');
        $this->assertFalse($map->contains('bb'), 'Map contains key "bb"');
    }

    public function testAdd(): void
    {
        $map = new MultiValueMap();

        $map->add('AbA', 'A')->add('bBb', ['B']);

        $this->assertTrue($map->contains('AbA'), 'Map does not contain key "AbA"');
        $this->assertTrue($map->contains('bBb'), 'Map does not contain key "bbb"');
        $this->assertFalse($map->contains('aba'), 'Map contains key "aba"');
    }

    public function testMergeValues(): void
    {
        $map = new MultiValueMap();

        $map->add('A', 'a')->add('A', 'b')->add('A', ['c']);

        $this->assertEquals(['a', 'b', 'c'], $map->get('A'));
    }

    public function testRemoveValues(): void
    {
        $map = new MultiValueMap();

        $map->add('A', ['a'])->add('B', ['b']);

        $map->remove('A');

        $this->assertEquals(['B'], $map->keys());
        $this->assertFalse($map->contains('A'));
    }

    public function testArrayAccess(): void
    {
        $map = (new MultiValueMap())->setAll([
            'A' => ['a', 'A', '1'],
            'B' => ['b', 'B', '2']
        ]);

        $this->assertEquals(['a', 'A', '1'], $map['A']);
    }

    public function testGetFirst(): void
    {
        $map = (new MultiValueMap(true))->setAll([
            'A' => ['a', 'A', '1'],
            'B' => ['2', 'b', 'B'],
        ]);

        $this->assertEquals('2', $map->getFirst('b'));
    }

    public function testAddIfAbsent(): void
    {
        $map = new MultiValueMap();

        $map->add('A', '1')
            ->add('B', '2')
            ->putIfAbsent('A', '500');

        $this->assertEquals('1', $map->getFirst('A'));
    }

    public function testGetValues(): void
    {
        $map = (new MultiValueMap())->setAll([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c'
        ]);

        $map->add('A', 'aa');

        $this->assertEquals(['a', 'aa'], $map->get('A'));
    }

    public function testGetAll(): void
    {
        $map = (new MultiValueMap())->setAll([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c'
        ]);

        $all = $map->getAll();

        $this->assertEquals([
            'A' => ['a'],
            'B' => ['b'],
            'C' => ['c']
        ], $all);
    }

    public function testIterator(): void
    {
        $map = (new MultiValueMap(true))->setAll([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c'
        ]);

        $all = [];

        foreach ($map as $k => $v) {
            $all[$k] = $v;
        }

        $this->assertEquals([
            'A' => ['a'],
            'B' => ['b'],
            'C' => ['c']
        ], $all);
    }

    public function testAddAll(): void
    {
        $map = (new MultiValueMap())->setAll([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c'
        ]);

        $map2 = (new MultiValueMap())->setAll([
            'C' => 'CcC'
        ]);

        // Add array
        $map->addAll(['C' => 'CCC']);
        // Add self
        $map->addAll($map2);

        $all = $map->getAll();

        $this->assertEquals([
            'A' => ['a'],
            'B' => ['b'],
            'C' => ['c', 'CCC', 'CcC']
        ], $all);
    }

    public function testPutAll(): void
    {
        $map = (new MultiValueMap())->setAll([
            'A' => 'a',
            'B' => 'b',
            'C' => 'c'
        ]);

        $map->putAll(['A' => 'aa', 'D' => 'd']);

        $all = $map->getAll();

        $this->assertEquals([
            'A' => ['aa'],
            'B' => ['b'],
            'C' => ['c'],
            'D' => ['d']
        ], $all);
    }

    public function testMerge(): void
    {
        $map = (new MultiValueMap())->setAll(['A' => 'a']);
        $map2 = (new MultiValueMap())->setAll(['B' => 'b']);
        $map3 = $map->merge($map2);

        $this->assertEquals([
            'A' => ['a'],
            'B' => ['b']
        ], $map3->getAll());

        $this->assertEquals([
            'A' => ['a']
        ], $map->getAll());

        $this->assertEquals([
            'B' => ['b']
        ], $map2->getAll());
    }
}
