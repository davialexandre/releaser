<?php

namespace Releaser;

class ConfigFactory
{
    public static function create(): Config
    {
        $configFile = self::getConfigFile();

        if(file_exists($configFile)) {
            return new Config(json_decode(file_get_contents($configFile)));
        }

        return null;
    }

    private static function getConfigFile(): string
    {
        if (getenv('RELEASER_CONFIG')) {
            $file = getenv('RELEASER_CONFIG');
            return $file;
        }

        $configDir = self::getConfigDir();

        return $configDir . '/releaser.json';
    }

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
    }

    /**
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
