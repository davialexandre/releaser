<?php

namespace Releaser\View;

class PullRequestDescription
{
    private $pullRequests;

    public function __construct(array $pullRequests)
    {
        $this->pullRequests = $pullRequests;
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
        foreach ($this->pullRequests as $pullRequest) {
            $pullRequestsGroupByAuthor[$pullRequest->getAuthor()][] = $pullRequest;
        }

        ksort($pullRequestsGroupByAuthor);

        return $pullRequestsGroupByAuthor;
    }
}
