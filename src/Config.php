<?php namespace Nine\Collections;

/**
 * @package Nine Collections
 * @version 0.4.2
 * @author  Greg Truesdell <odd.greg@gmail.com>
 */

use Nine\Traits\WithItemArrayAccess;
use Nine\Traits\WithItemImportExport;

/**
 * **Config provides a central, standardised method of handling
 * configuration files and settings in the F9 framework.**
 *
 * A general purpose configuration class with import/export methods
 * and \ArrayAccess with `dot` notation access methods.
 */
class Config extends Collection implements ConfigInterface
{
    // for YAML and JSON import and export methods
    use WithItemImportExport;

    // for \ArrayAccess methods that support `dot` indexes
    use WithItemArrayAccess;

    /**
     * @param array $import
     */
    public function importArray(Array $import)
    {
        array_map(
            function ($key, $value) { $this->set($key, $value); },
            array_keys($import), array_values($import)
        );
    }

    /**
     * @param string $file
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    public function importFile($file)
    {
        $this->import_files([$file], '.php');
    }

    /**
     * Imports (merges) config files found in the specified directory.
     *
     * @param string $base_path
     * @param string $mask
     *
     * @return Config
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    public function importFolder($base_path, $mask = '*.php')
    {
        // extract the extension from the mask
        $extension = str_replace('*', '', $mask);

        // import the files
        $this->import_files($this->parse_folder($base_path, $mask), $extension);

        return $this;
    }

    /**
     *
     * @param string $folder
     *
     * @return Config|static
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    static public function createFromFolder($folder)
    {
        return (new static)->importFolder($folder);
    }

    /**
     * @param string $json - filename or JSON string
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    static public function createFromJson($json)
    {
        $config = new static;
        $config->importJSON($json);

        return $config;
    }

    /**
     * @param $yaml
     *
     * @return static
     *
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @throws \InvalidArgumentException
     */
    static public function createFromYaml($yaml)
    {
        $config = new static;
        $config->importYAML($yaml);

        return $config;
    }

    /**
     * @param $abstract
     *
     * @return mixed
     */
    public static function setting($abstract)
    {
        return parent::all()[$abstract];
    }

    /**
     * Register a configuration using the base name of the file.
     *
     * @param        $extension
     * @param        $file_path
     * @param string $key
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    private function import_by_extension($extension, $file_path, $key = '')
    {
        $extension = strtolower(str_replace('*', '', $extension));

        if ( ! in_array($extension, ['.json', '.php', '.yaml', '.yml'], TRUE)) {
            throw new \InvalidArgumentException("Invalid import extension: `$extension`");
        }

        # include only if the root key does not exist
        if ( ! $this->offsetExists($key)) {
            switch ($extension) {
                case '.php':
                    /** @noinspection UntrustedInclusionInspection */
                    $import = include "$file_path";
                    break;
                case '.yaml':
                case '.yml':
                    $import = $this->importYAML($file_path);
                    break;
                case '.json':
                    $import = $this->importJSON($file_path);
                    break;
                default :
                    $import = NULL;
                    break;
            }

            # only import if the config file returns an array
            if (is_array($import)) {
                $this->set($key, $import);
            }
        }
    }

    /**
     * Import configuration data from a set of files.
     *
     * @param array  $files
     * @param string $file_extension
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    private function import_files(array $files, $file_extension = '.php')
    {
        foreach ($files as $config_file) {
            # use the base name as the config key.
            # i.e.: `config/happy.php` -> `happy`
            $config_key = basename($config_file, $file_extension);

            # load
            $this->import_by_extension($file_extension, $config_file, $config_key);
        }
    }

    /**
     * Glob a set of file names from a normalized path.
     *
     * @param string $base_path
     * @param string $file_extension
     *
     * @return array
     */
    private function parse_folder($base_path, $file_extension = '.php')
    {
        $base_path = rtrim(realpath($base_path), '/') . '/';

        return glob($base_path . $file_extension);
    }
}
