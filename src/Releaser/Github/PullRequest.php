<?php

namespace Releaser\Github;

class PullRequest
{
    private $pullRequestData;

    public function __construct($pullRequestData)
    {
        $this->pullRequestData = $pullRequestData;
    }

    public function getAuthor(): string
    {
        return $this->pullRequestData['user']['login'];
    }

    public function getTitle(): string
    {
        return $this->pullRequestData['title'];
    }

    public function getUrl(): string
    {
        return $this->pullRequestData['html_url'];
    }

}
