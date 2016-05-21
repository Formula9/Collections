<?php namespace Nine\Collections;

/**
 * @package Nine
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use Nine\Library\Lib;
use Symfony\Component\Yaml\Yaml;

/**
 * Test the Collection Class
 *
 * @backupGlobals          disabled
 * @backupStaticAttributes disabled
 */
class PathsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Paths */
    private $paths;

    public function setUp()
    {
        $this->paths = new Paths;
    }

    public function tearDown()
    {
        $this->paths = NULL;
    }

    public function test00_Instantiation()
    {
        //echo "\n+paths";

        // validate instance
        $this->assertInstanceOf(Paths::class, $this->paths, 'paths should be an instance of Paths.');
    }

    public function test01_Paths()
    {
        $paths = new Paths(
            (new Yaml)->parse(file_get_contents(__DIR__ . '/config/paths.yml'))
        );

        $this->assertArrayHasKey('support', $paths, 'paths should include `support`.');
        $this->assertEquals(Lib::normalize_path('src/support'), $paths['support']);

        $paths->add('cows', 'src/contracts');
        $this->assertArrayHasKey('cows', $paths, 'paths should have added `cows`.');
        $this->assertEquals(Lib::normalize_path('src/contracts'), $paths['cows']);

        $paths->merge([
            'apple'  => 'src/traits',
            'orange' => 'tests/config',
        ]);
        $this->assertArrayHasKey('orange', $paths, 'paths should have merged `orange`.');
        $this->assertEquals(Lib::normalize_path('tests/config'), $paths['orange']);

    }

}
