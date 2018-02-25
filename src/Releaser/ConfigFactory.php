<?php

namespace Releaser;

/**
 * Creates a new Config object
 *
 * The code here was based on https://github.com/composer/composer/blob/master/src/Composer/Factory.php
 *
 * @package Releaser
 */
class ConfigFactory
{
    /**
     * Locates the configuration file and instantiates a new Config object with it.
     *
     * First, we check the RELEASER_CONFIG environment variable exists and was set to
     * set the path to the config file. If not, then it checks for a `releaser.json`
     * file in one of these places:
     *
     * - The `.releaser` folder in the current user's home folder
     * - The `.config/releaser` folder in the current user's home folder
     * - The `releaser` folder inside the folder set in the XDG_CONFIG_HOME
     *
     * @return Config
     */
    public static function create(): Config
    {
        $configFile = self::getConfigFile();

        if(file_exists($configFile)) {
            return new Config(json_decode(file_get_contents($configFile)));
        }

        return null;
    }

    /**
     * Returns the config file path, according to the rules described in
     * create()
     *
     * @return string
     */
    private static function getConfigFile(): string
    {
        if (getenv('RELEASER_CONFIG')) {
            $file = getenv('RELEASER_CONFIG');
            return $file;
        }

        $configDir = self::getConfigDir();

        return $configDir . '/releaser.json';
    }

    /**
     * Decides in which directory we should look for the configuration file
     *
     * @return string
     */
    private static function getConfigDir(): string
    {
        $home = self::getHomeDir();
        if (is_dir($home . '/.releaser')) {
            return $home . '/.releaser';
        }

        if (self::useXdg()) {
            // XDG Base Directory Specifications
            $xdgConfig = getenv('XDG_CONFIG_HOME') ?: $home . '/.config';
            return $xdgConfig . '/releaser';
        }

        return null;
    }

    /**
     * Returns the current user's home directory
     *
     * @throws \RuntimeException
     * @return string
     */
    private static function getHomeDir(): string
    {
        $home = getenv('HOME');
        if (!$home) {
            throw new \RuntimeException('The HOME environment variable must be set for releaser to run correctly');
        }

        return rtrim(strtr($home, '\\', '/'), '/');
    }

    /**
     * Returns if the XDG is being used in the current environment so that we
     * can use the XDG base directory Specification to look for a configuration
     * file
     *
     * @see https://specifications.freedesktop.org/basedir-spec/latest/
     *
     * @return bool
     */
    private static function useXdg(): bool
    {
        foreach (array_keys($_SERVER) as $key) {
            if (substr($key, 0, 4) === 'XDG_') {
                return true;
            }
        }

        return false;
    }
}
