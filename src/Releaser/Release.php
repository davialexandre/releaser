<?php

namespace Releaser;

use Github\Client;
use Releaser\Github\Commit;
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
     * @var Commit[]
     *  An array of all the commits include in this Release
     */
    private $commits;

    /**
     * @var string
     *  The head branch of this Release. That is, the branch
     *  with the changes we want to release
     */
    private $head;

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
     */
    public function __construct(Client $githubClient, $repo, $base, $head)
    {
        $this->repository = new Repository($githubClient, $repo);
        $this->head = $head;
        $this->commits = $this->compareBranches($base, $head);
    }

    /**
     * Returns the Pull Requests included in this Release. That is, the Pull Requests
     * for merged to the Release's head branch and that are not yet in the base branch.
     *
     * @return PullRequest[]
     */
    public function getPullRequests(): array
    {
        $pullRequests = [];
        foreach ($this->commits as $commit) {
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
     * Returns the head branch of this Release. That is, the branch with the changes
     * we want to merge
     *
     * @return string
     */
    public function getHeadBranch(): string
    {
        return $this->head;
    }

    /**
     * Compares the two given branches
     *
     * @param string $base
     * @param string $head
     * @return Commit[]
     */
    private function compareBranches($base, $head): array
    {
        $baseBranch = $this->repository->getBranch($base);
        $headBranch = $this->repository->getBranch($head);

        return $this->repository->compareBranches($baseBranch, $headBranch);
    }
}
