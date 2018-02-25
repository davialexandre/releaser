<?php

namespace Releaser\View;

use Releaser\Release;

/**
 * Renders the textual description of a Release
 *
 * It will contain a list of all the Pull Requests included in the release,
 * group by their authors
 *
 * @package Releaser\View
 */
class ReleaseDescription
{
    /**
     * @var Release
     */
    private $release;

    public function __construct(Release $release)
    {
        $this->release = $release;
    }

    /**
     * Renders the description
     *
     * The output will be something like this:
     *
     *
     *  `@username1_handle`
     *    - Pull Request Title: Pull Request URL
     *    - Pull Request Title: Pull Request URL
     *
     *  `@username2_handle`
     *    - Pull Request Title: Pull Request URL
     *   ...
     *
     * @return string
     */
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
     * Groups the Release's Pull Requests by their authors and sorts the authors alphabetically
     *
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
