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
     *  The base branch of this Release. That is, the branch
     *  to which we want to merge the changes
     */
    private $base;

    /**
     * @var string
     *  The head branch of this Release. That is, the branch
     *  with the changes we want to release
     */
    private $head;

    /**
     * @var string
     *  This Release's title
     */
    private $title;

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
        $this->base = $base;
        $this->head = $head;
        $this->title = "Sync $base with $head";
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
     * Sets the title of this Release. Used when creating a Pull Request.
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Creates a Pull Request for this Release
     *
     * @param string $description
     * @return PullRequest
     *
     * @throws \Github\Exception\MissingArgumentException
     */
    public function createPullRequest(string $description): PullRequest
    {
        return $this->repository->createPullRequest(
            $this->title,
            $description,
            $this->base,
            $this->head
        );
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
