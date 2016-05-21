<?php namespace Nine\Collections;

use Nine\Library\Lib;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{
    /** @var Attributes */
    private $attributes;

    public function setUp()
    {
        $this->attributes = new Attributes(
            Config::createFromYaml(__DIR__ . '/config/config.yml')->copy()
        );
    }

    public function tearDown()
    {
        $this->attributes = NULL;
    }

    public function test00_Instantiation()
    {
        //echo "\n+attributes";

        // validate the instance of Attributes
        static::assertInstanceOf(Attributes::class, $this->attributes);
    }

    public function test01_ContentTests()
    {
        // validate attributes
        static::assertArrayHasKey('db', $this->attributes->getAttributes(),
            'key `db` should exist in attributes.');

        // find expected
        static::assertSame(Lib::array_query($this->attributes->getAttributes(), 'db.models.home'), 'HomeModel', '`db.models.home` should return `HomeModel`.');

        //validate toJson()
        static::assertEquals(file_get_contents(__DIR__ . '/config/json_pretty_test.json'), $this->attributes->toJson(JSON_PRETTY_PRINT),
            'toJson should match stored json sample.');
    }

    public function test02_Assignments()
    {
        $attributes = new Attributes(['test' => 'test array assignment']);
        static::assertTrue(isset($this->attributes['test']));
        static::assertEquals('test array assignment', $attributes['test']);

        $this->expectException(Exceptions\ImmutableViolationException::class);
        unset($attributes['test']);
    }

    public function test03_AttributesFromArrayable()
    {
        $this->attributes = new Attributes(
            $arrayable = Config::createFromYaml(__DIR__ . '/config/config.yml')
        );

        static::assertEquals($this->attributes->toArray(), $arrayable->toArray());
    }

}
