<?php

namespace Releaser\Github;

/**
 * Represents a Pull Request in a Repository
 *
 * @package Releaser\Github
 */
class PullRequest
{
    /**
     * @var var
     *   The Pull Request data as returned by the Github API
     * @see https://developer.github.com/v3/pulls/#get-a-single-pull-request
     */
    private $pullRequestData;

    public function __construct($pullRequestData)
    {
        $this->pullRequestData = $pullRequestData;
    }

    /**
     * Returns the Github handle of the Author of the Pull Request
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->pullRequestData['user']['login'];
    }

    /**
     * Returns the title of the Pull Request
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->pullRequestData['title'];
    }

    /**
     * Returns the URL of the Pull Request
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->pullRequestData['html_url'];
    }

}
