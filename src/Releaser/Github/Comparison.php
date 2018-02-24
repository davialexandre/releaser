<?php

namespace Releaser\Github;

class Comparison
{
    private $comparisonData;

    public function __construct(array $comparisonData)
    {
        $this->comparisonData = $comparisonData;
    }

    /**
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

    public function getTotalNumberOfCommits(): int
    {
        return $this->comparisonData['total_commits'];
    }

    public function getNumberOfCommits(): int
    {
        return count($this->comparisonData['commits']);
    }
}
