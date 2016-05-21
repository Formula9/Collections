<?php namespace Nine\Collections;

use function Nine\Library\tuples;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ScopeTest extends \PHPUnit_Framework_TestCase
{
    protected $scope;

    public function doPlugin($test)
    {
        return strtoupper($test);
    }

    public function setUp()
    {
        $this->scope = new Scope;
    }

    public function test_access()
    {
        $scope = new Scope(tuples('a:1, b:2, c:3'));
        // append
        $scope->append('d', 'extra');
        static::assertTrue($scope->has('d'));
        static::assertEquals(tuples('a:1, b:2, c:3, d:extra'), $scope->toArray());
        // get
        static::assertEquals('extra', $scope->get('d'));
        // count
        static::assertEquals(4, $scope->count());
        // set
        $scope->set('d', 'other');
        static::assertEquals('other', $scope->get('d'));
        // search and replace
        $scope->search_and_replace(['d' => 'fork']);
        static::assertEquals('fork', $scope->get('d'));
    }

    public function test_instantiation()
    {
        static::assertInstanceOf(Scope::class, $this->scope);
        static::assertNotSame(new Scope, $this->scope, 'Scope should create a new instance.');
    }

    public function test_plugins()
    {
        $scope = new Scope;
        static::assertFalse($scope->hasPlugin('test@me'));

        $scope->plugin('test.me', [$this, 'doPlugin']);
        static::assertTrue($scope->hasPlugin('test.me'));
        static::assertEquals('THIS IS A TEST', $scope->{'test.me'}('This is a test'));

        $scope->forgetPlugin('test.me');
        static::assertFalse($scope->hasPlugin('test.me'));

    }

    public function test_storage()
    {
        $scope = new Scope(new Collection(tuples('a:1, b:2, c:3')));
        static::assertEquals(tuples('a:1, b:2, c:3'), $scope->toArray());

        $scope->forget('b');
        static::assertEquals(tuples('a:1, c:3'), $scope->toArray());

        $scope->merge(tuples('b:two'));
        static::assertEquals(tuples('a:1, b:two, c:3'), $scope->toArray());

        $scope->sort();
        static::assertEquals('{"a":1,"b":"two","c":3}', $scope->toJson());
    }

}
