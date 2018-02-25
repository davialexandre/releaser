<?php

namespace Releaser\Github;

/**
 * Represents a comparison between two commits in a Repository
 *
 * @package Releaser\Github
 */
class Comparison
{
    /**
     * @var array
     *   The comparison data as returned by the Github API
     * @see https://developer.github.com/v3/repos/commits/#compare-two-commits
     */
    private $comparisonData;

    public function __construct(array $comparisonData)
    {
        $this->comparisonData = $comparisonData;
    }

    /**
     * Returns all the commits contained between the two commits in the Comparison.
     *
     * For example, consider this repository tree:
     *   A *master
     *    \_B_C_D *staging
     *
     * If the comparison is between D and A, then commits D, C and B will be returned
     *
     * Important: Due to the imposed limits of the Github API, only up to 250 commits
     * will be returned.
     *
     * @return Commit[]
     */
    public function getCommits(): array
    {
        $commits = [];

        foreach ($this->comparisonData['commits'] as $commitData) {
            $commits[] = new Commit($commitData);
        }

        return $commits;
    }

    /**
     * Returns the total number of commits in the Comparison. Note that this
     * is the actual number of commits in the comparison, not the number of
     * commits objects returned. This means this number can be greater than
     * the number of items returned.
     *
     * @return int
     */
    public function getTotalNumberOfCommits(): int
    {
        return $this->comparisonData['total_commits'];
    }

    /**
     * Returns the number of commits objects returned by the API and included in
     * the Comparison. Since the API limits this to up 250 items, this number can
     * be less than the one returned by getTotalNumberOfCommits()
     *
     * @return int
     */
    public function getNumberOfCommits(): int
    {
        return count($this->comparisonData['commits']);
    }
}
