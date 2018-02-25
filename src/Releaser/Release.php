<?php

namespace Releaser;

use Github\Client;
use Releaser\Github\Comparison;
use Releaser\Github\PullRequest;
use Releaser\Github\Repository;

/**
 * Represents a new Release in a Repository.
 *
 * A Release is basically a Pull Request from the $head branch to a $base branch.
 *
 * @package Releaser
 */
class Release
{
    /**
     * @var Repository
     *  The Repository to which this Release belongs to
     */
    private $repository;

    /**
     * @var Comparison
     *  A comparison between the two branches involved in this Release
     */
    private $comparison;

    /**
     * Creates a new Release
     *
     * @param Client $githubClient
     *   A $client object to call the Github API
     * @param string $repo
     *   The repository where the Release will happen. A string like
     *   "davialexandre/releaser", for example
     * @param string $base
     *   The branch to which we want to release things to
     * @param string $head
     *   The branch from which we want to release things from
     *
     * @throws Github\InvalidArgumentException
     */
    public function __construct(Client $githubClient, $repo, $base, $head)
    {
        $this->repository = new Repository($githubClient, $repo);
        $this->comparison = $this->compareBranches($base, $head);
    }

    /**
     * Returns the Pull Requests included in this Release. That is, the Pull Requests
     * for merged to the Release's head branch and that are not yet in the base branch.
     *
     * Note that, due to the fact the Github API limits the information returns in a
     * comparison between two branches, not all commits might have been included here.
     *
     * @return PullRequest[]
     */
    public function getPullRequests(): array
    {
        $pullRequests = [];
        foreach ($this->comparison->getCommits() as $commit) {
            $pullRequestID = $commit->getMergedPullRequestID();

            if (!$pullRequestID) {
                continue;
            }

            $pullRequest = $this->repository->getPullRequestById($pullRequestID);
            $pullRequests[] = $pullRequest;
        }

        return $pullRequests;
    }

    /**
     * Returns the total number of Commits in the Release. This counts even the commits
     * objects not returned by the Github API
     *
     * @return int
     */
    public function getTotalNumberOfCommits(): int
    {
        return $this->comparison->getTotalNumberOfCommits();
    }

    /**
     * Returns the total number of commits objects included in the Release objected and returned
     * by the Github API. This number can be less than the one returned by getTotalNumberOfCommits()
     *
     * @return int
     */
    public function getNumberOfCommits(): int
    {
        return $this->comparison->getNumberOfCommits();
    }

    /**
     * Compares the two given branches
     *
     * @param string $base
     * @param string $head
     * @return Comparison
     */
    private function compareBranches($base, $head): Comparison
    {
        $baseBranch = $this->repository->getBranch($base);
        $headBranch = $this->repository->getBranch($head);

        return $this->repository->compareBranches($baseBranch, $headBranch);
    }
}
