<?php

namespace Releaser;

use Github\Client;
use Releaser\Github\PullRequest;
use Releaser\Github\Repository;

class Release
{
    private $repository;
    private $headBranch;
    private $baseBranch;

    public function __construct(Client $githubClient, $repo, $base, $head)
    {
        $this->repository = new Repository($githubClient, $repo);
        $this->baseBranch = $this->repository->getBranch($base);
        $this->headBranch = $this->repository->getBranch($head);
    }

    /**
     * @return PullRequest[]
     */
    public function getPullRequests(): array
    {
        $commits = $this->repository->compareBranches($this->baseBranch, $this->headBranch);

        $pullRequests = [];
        foreach ($commits as $commit) {
            $pullRequestID = $commit->getMergedPullRequestID();

            if (!$pullRequestID) {
                continue;
            }

            $pullRequest = $this->repository->getPullRequestById($pullRequestID);
            $pullRequests[] = $pullRequest;
        }

        return $pullRequests;
    }
}
