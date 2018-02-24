<?php

namespace Releaser\Github;

class Commit
{
    private $commitData;

    public function __construct($commitData)
    {
        $this->commitData = $commitData;
    }

    public function getMessage(): string
    {
        return $this->commitData['commit']['message'];
    }

    public function getHash(): string
    {
        return $this->commitData['sha'];
    }

    public function getMergedPullRequestID(): ?int
    {
        $matches = [];
        if (!preg_match($this->getMergePullRequestRegex(), $this->getMessage(), $matches)) {
            return null;
        }

        return (int)$matches[1];
    }

    private function getMergePullRequestRegex(): string
    {
        return '/Merge pull request #(\d+?) from/';
    }
}
