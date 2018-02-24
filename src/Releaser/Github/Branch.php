<?php

namespace Releaser\Github;

class Branch
{
    private $branchData;

    public function __construct(array $branchData)
    {
        $this->branchData = $branchData;
    }

    public function getCommit(): Commit
    {
        return new Commit($this->branchData['commit']);
    }
}
