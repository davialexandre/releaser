<?php

namespace Releaser\Github;

use Github\Client;

class Repository
{
    private $client;
    private $repositoryInfo;

    public function __construct(Client $client, string $repository)
    {
        $this->client = $client;
        $this->repositoryInfo = $this->parseRepository($repository);
    }

    public function getBranch($branchName): Branch
    {
        $branchData = $this->getBranchData($branchName);

        return new Branch($branchData);
    }

    /**
     * @param Branch $base
     * @param Branch $head
     *
     * @return Comparison
     */
    public function compareBranches(Branch $base, Branch $head): Comparison
    {
        $comparisonData = $this->compareApi($base->getCommit(), $head->getCommit());

        return new Comparison($comparisonData);
    }

    public function getPullRequestById($id): PullRequest
    {
        $pullRequestData = $this->pullRequestApi()->show(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $id
        );

        return new PullRequest($pullRequestData);
    }

    /**
     * @param $repository
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function parseRepository($repository): array
    {
        $matches = [];
        if (!preg_match('/^(\w+?)\/(\w+?)$/', $repository, $matches)) {
            throw new InvalidArgumentException(
                'Invalid repository. It must be a string in the "username/repository" format'
            );
        }

        return [
            'username' => $matches[1],
            'repository' => $matches[2],
        ];
    }

    private function repoApi()
    {
        return $this->client->repo();
    }

    private function getBranchData($branchName)
    {
        return $this->repoApi()->branches(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $branchName
        );
    }

    private function compareApi(Commit $baseCommit, Commit $headCommit)
    {
        return $this->repoApi()->commits()->compare(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $baseCommit->getHash(),
            $headCommit->getHash()
        );
    }

    private function pullRequestApi()
    {
        return $this->client->pullRequest();
    }
}
