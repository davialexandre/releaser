<?php

namespace Releaser\View;

use Releaser\Release;

class ReleaseDescription
{
    private $release;

    public function __construct(Release $release)
    {
        $this->release = $release;
    }

    public function render()
    {
        $output = "Pull requests included: \n\n";

        foreach ($this->groupAndSortPullRequestsByAuthor() as $author => $pullRequests) {
            $output .= "@{$author}: \n";
            foreach ($pullRequests as $pullRequest) {
                $output .= sprintf(
                    "- %s: %s\n",
                    $pullRequest->getTitle(),
                    $pullRequest->getUrl()
                );
            }
            $output .= "\n";
        }

        return $output;
    }

    /**
     * @return array
     */
    private function groupAndSortPullRequestsByAuthor(): array
    {
        $pullRequestsGroupByAuthor = [];
        foreach ($this->release->getPullRequests() as $pullRequest) {
            $pullRequestsGroupByAuthor[$pullRequest->getAuthor()][] = $pullRequest;
        }

        ksort($pullRequestsGroupByAuthor);

        return $pullRequestsGroupByAuthor;
    }
}
