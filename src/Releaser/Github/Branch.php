<?php

namespace Releaser\Github;

/**
 * This class Represents a branch in a Repository, and provides some
 * methods to access information about it
 *
 * @package Releaser\Github
 */
class Branch
{
    /**
     * @var array
     *  The branch data returned by the Github API
     * @see https://developer.github.com/v3/repos/branches/#get-branch
     */
    private $branchData;

    public function __construct(array $branchData)
    {
        $this->branchData = $branchData;
    }

    /**
     * Returns a Commit object representing the commit this branch is
     * pointing to
     *
     * @return Commit
     */
    public function getCommit(): Commit
    {
        return new Commit($this->branchData['commit']);
    }
}
