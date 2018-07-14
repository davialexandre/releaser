<?php

namespace Releaser\Github;

use Github\Client;

/**
 * Represents a Repository in Github
 *
 * @package Releaser\Github
 */
class Repository
{
    /**
     * @var Client
     *  A Github client object, used to make API calls
     */
    private $client;

    /**
     * @var array
     *  An array containing the repository name and its user:
     *  [
     *    'username' => 'foo',
     *    'repository' => 'bar'
     *  ]
     */
    private $repositoryInfo;

    /**
     * Creates a new Repository
     *
     * @param Client $client
     * @param string $repository
     *   A string like "davialexandre/releaser"
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Client $client, string $repository)
    {
        $this->client = $client;
        $this->repositoryInfo = $this->parseRepository($repository);
    }

    /**
     * Returns a Branch object representing a branch in this Repository with
     * the given $branchName
     *
     * @param string $branchName
     * @return Branch
     */
    public function getBranch(string $branchName): Branch
    {
        $branchData = $this->getBranchData($branchName);

        return new Branch($branchData);
    }

    /**
     * Compares two branches in the Repository
     *
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

    /**
     * Returns a Pull Request object representing the Pull Request with the given
     * $id in this Repository
     *
     * @param int $id
     * @return PullRequest
     */
    public function getPullRequestById(int $id): PullRequest
    {
        $pullRequestData = $this->pullRequestApi()->show(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $id
        );

        return new PullRequest($pullRequestData);
    }

    /**
     * Parses a string like "<username|organization>/<repository" into an array like:
     *
     * [
     *   'username' => <username|organization>,
     *   'repository' => <repository>
     * ]
     *
     * @param string $repository
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function parseRepository(string $repository): array
    {
        $matches = [];
        if (!preg_match('/^(\w+?)\/([\w-]+?)$/', $repository, $matches)) {
            throw new \InvalidArgumentException(
                'Invalid repository. It must be a string in the "username/repository" format'
            );
        }

        return [
            'username' => $matches[1],
            'repository' => $matches[2],
        ];
    }

    /**
     * Returns an API endpoint for Repositories
     *
     * @return \Github\Api\Repo
     */
    private function repoApi(): \Github\Api\Repo
    {
        return $this->client->repo();
    }

    /**
     * Calls the Github API and returns the branch data for the branch with
     * the given $branchName
     *
     * @see https://developer.github.com/v3/repos/branches/#get-branch
     *
     * @param string $branchName
     * @return array
     */
    private function getBranchData(string $branchName): array
    {
        return $this->repoApi()->branches(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $branchName
        );
    }

    /**
     * Calls the Github API to compare two commits
     *
     * @see https://developer.github.com/v3/repos/commits/#compare-two-commits
     *
     * @param Commit $baseCommit
     * @param Commit $headCommit
     * @return array
     */
    private function compareApi(Commit $baseCommit, Commit $headCommit): array
    {
        return $this->repoApi()->commits()->compare(
            $this->repositoryInfo['username'],
            $this->repositoryInfo['repository'],
            $baseCommit->getHash(),
            $headCommit->getHash()
        );
    }

    /**
     * Returns an API endpoint for Pull Requests
     *
     * @return \Github\Api\PullRequest
     */
    private function pullRequestApi()
    {
        return $this->client->pullRequest();
    }
}
