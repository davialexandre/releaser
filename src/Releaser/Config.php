<?php

namespace Releaser;

class Config
{
    private $config;

    public function __construct(string $config)
    {
        $this->config = $config;
    }

    public function getGithubAuthToken(): string
    {
        return $this->getConfigValue('github_auth_token');
    }

    private function getConfigValue(string $config, $defaultValue = null)
    {
        if(property_exists($this->config, $config)) {
            return $this->config->{$config};
        }

        return $defaultValue;
    }
}
