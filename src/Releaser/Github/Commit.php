<?php

namespace Releaser\Github;

/**
 * Represents a single Commit in a Repository
 *
 * @package Releaser\Github
 */
class Commit
{
    /**
     * @var array
     *   The commit data as returned by the Github API
     * @see https://developer.github.com/v3/repos/commits/#get-a-single-commit
     */
    private $commitData;

    public function __construct($commitData)
    {
        $this->commitData = $commitData;
    }

    /**
     * Returns the message for this commit
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->commitData['commit']['message'];
    }

    /**
     * Returns the hash for this commit
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->commitData['sha'];
    }

    /**
     * If this commit was created as the result of a Pull Request merge,
     * this merge will return the ID of the Pull Request. Otherwise, null
     * will be returned.
     *
     * @return int|null
     */
    public function getMergedPullRequestID(): ?int
    {
        $matches = [];
        if (!preg_match($this->getMergePullRequestRegex(), $this->getMessage(), $matches)) {
            return null;
        }

        return (int)$matches[1];
    }

    /**
     * Returns the REGEX to check the commit message to see if it was
     * created by a Pull Request merge
     *
     * @return string
     */
    private function getMergePullRequestRegex(): string
    {
        return '/Merge pull request #(\d+?) from/';
    }

    /**
     * Returns if this Commit is equal to $commit.
     *
     * Two commits are equal if they have the same hash
     *
     * @param Commit $commit
     * @return bool
     */
    public function equalsTo(Commit $commit): bool
    {
        return $this->getHash() === $commit->getHash();
    }
}
