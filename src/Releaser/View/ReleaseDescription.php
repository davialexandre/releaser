<?php

namespace Releaser\View;

use Releaser\Github\PullRequest;
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

    /**
     * @var bool
     *  Whether the description will only include Pull Requests sent to
     *  the Release's head branch or not
     */
    private $excludeSubPullRequests;

    public function __construct(Release $release, bool $excludeSubPullRequests)
    {
        $this->release = $release;
        $this->excludeSubPullRequests = $excludeSubPullRequests;
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
            if ($this->shouldExcludePullRequest($pullRequest)) {
                continue;
            }
            $pullRequestsGroupByAuthor[$pullRequest->getAuthor()][] = $pullRequest;
        }

        ksort($pullRequestsGroupByAuthor);

        return $pullRequestsGroupByAuthor;
    }

    /**
     * Returns whether the given $pullRequest should be included in the description
     * or not.
     *
     * If 'excludeSubPullRequests' is true, then only Pull Requests targeting the
     * Release's head (i.e. Pull Requests which the base branch is the Release's
     * head branch) will be included. Otherwise, all Pull Requests will be
     * included.
     *
     * @param PullRequest $pullRequest
     * @return bool
     */
    private function shouldExcludePullRequest(PullRequest $pullRequest): bool
    {
        if (!$this->excludeSubPullRequests) {
            return false;
        }

        return $pullRequest->getBaseBranchName() !== $this->release->getHeadBranch();
    }
}
