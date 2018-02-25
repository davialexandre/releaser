<?php

namespace Releaser;

/**
 * Represents the application configuration
 *
 * @package Releaser
 */
class Config
{
    /**
     * @var \stdClass
     *  The config json object, as parsed from the configuration file
     */
    private $config;

    public function __construct(object $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the value of the `github_auth_token` property from the
     * configuration file, or an empty string if the property doesn't exist.
     *
     * @return string
     */
    public function getGithubAuthToken(): string
    {
        return $this->getConfigValue('github_auth_token', '');
    }

    /**
     * If $property exists in the config object, returns its value. Otherwise, returns
     * $defaultValue
     *
     * @param string $property
     * @param null $defaultValue
     * @return mixed|null
     */
    private function getConfigValue(string $property, $defaultValue = null)
    {
        if(property_exists($this->config, $property)) {
            return $this->config->{$property};
        }

        return $defaultValue;
    }
}
