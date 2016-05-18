<?php namespace Nine\Collections;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class ContextTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    private $collection;

    /** @var Scope */
    private $context;

    private $context_with_collection;

    public function setUp()
    {
        $grocery_list = [
            'groceries' => [
                'apples'  => 10,
                'bananas' => 6,
                'cereal'  => 'Fruit Loops',
            ],
        ];

        $this->collection = new Collection($grocery_list);
        $this->context = new Scope($grocery_list);
    }

    public function tearDown()
    {
        $this->collection = NULL;
        $this->context = NULL;
        $this->context_with_collection = NULL;
    }

    public function test00_context_should_correctly_instantiate_from_a_collection()
    {
        //echo "\n+context";

        $this->context_with_collection = new Scope($this->collection);

        static::assertNotNull($this->context, 'Context must not be NULL.');
        static::assertNotNull($this->context_with_collection, 'Context must not be NULL.');
        static::assertInstanceOf(Scope::class, $this->context, 'Context must exist.');
        static::assertInstanceOf(Scope::class, $this->context_with_collection, 'Context must exist.');
    }

    public function test01_context_should_provide_array_and_property_access_to_data()
    {
        $this->context_with_collection = new Scope($this->collection);

        static::assertEquals('Fruit Loops', $this->context->{'groceries.cereal'}, 'Cereal must be Fruit Loops.');
        static::assertEquals('Fruit Loops', $this->context_with_collection->{'groceries.cereal'}, 'Cereal must be Fruit Loops.');
        static::assertEquals(10, $this->context['groceries.apples'], 'Cereal must be Fruit Loops.');
        static::assertEquals(10, $this->context_with_collection['groceries.apples'], 'Cereal must be Fruit Loops.');

        static::assertEquals([
            'apples'  => 10,
            'bananas' => 6,
            'cereal'  => 'Fruit Loops',
        ], $this->context['groceries'], 'Groceries must return a grocery list.');

        static::assertEquals([
            'apples'  => 10,
            'bananas' => 6,
            'cereal'  => 'Fruit Loops',
        ], $this->context_with_collection['groceries'], 'Groceries must return a grocery list.');
    }

    public function test02_config_should_support_creation_checking_and_execution_of_macros()
    {
        $context = $this->context;

        $context->plugin('test_me', function () { return 'This is a test.'; });
        static::assertTrue($context->hasPlugin('test_me'), 'Should have macro named `test_me`.');
        static::assertEquals('This is a test.', $this->context->{'test_me'}(), 'The macro `test_me()` should return `This is a test.`');

        $context->plugin('test_me_with_args', function ($name) { return "My name is $name."; });
        static::assertTrue($context->hasPlugin('test_me_with_args'), 'Should have macro named `test_me_with_args.`.');
        static::assertEquals('My name is Greg.', $this->context->{'test_me_with_args'}('Greg'), 'The macro `test_me()` should return `My name is Greg.`');
        static::assertEquals('My name is Greg.', $this->context->{'test_me_with_args'}('Greg'), 'The macro `test_me()` should return `My name is Greg.`');
    }
}
