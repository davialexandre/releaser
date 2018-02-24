<?php

namespace Releaser;

use Github\Client;
use Releaser\Github\PullRequest;
use Releaser\Github\Repository;
use Releaser\View\PullRequestDescription;

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

    public function getDescription()
    {
        $mergedPullRequests = $this->getMergedPullRequests();

        $pullRequestDescription = new PullRequestDescription($mergedPullRequests);

        return $pullRequestDescription->render();
    }

    /**
     * @return PullRequest[]
     */
    private function getMergedPullRequests(): array
    {
        $commits = $this->repository->compareBranches($this->baseBranch, $this->headBranch);

        $mergedPullRequests = [];
        foreach ($commits as $commit) {
            $pullRequestID = $commit->getMergedPullRequestID();

            if (!$pullRequestID) {
                continue;
            }

            $pullRequest = $this->repository->getPullRequestById($pullRequestID);
            $mergedPullRequests[] = $pullRequest;
        }

        return $mergedPullRequests;
    }
}
