<?php

namespace Releaser;

use Github\Client;
use Releaser\Github\Comparison;
use Releaser\Github\PullRequest;
use Releaser\Github\Repository;

class Release
{
    private $repository;
    private $comparison;

    public function __construct(Client $githubClient, $repo, $base, $head)
    {
        $this->repository = new Repository($githubClient, $repo);
        $this->comparison = $this->compareBranches($base, $head);
    }

    /**
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

    private function compareBranches($base, $head): Comparison
    {
        $baseBranch = $this->repository->getBranch($base);
        $headBranch = $this->repository->getBranch($head);

        return $this->repository->compareBranches($baseBranch, $headBranch);
    }
}
