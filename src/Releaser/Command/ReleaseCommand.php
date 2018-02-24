<?php

namespace Releaser\Command;

use Releaser\Github\PullRequest;
use Releaser\Github\Repository;
use Releaser\View\PullRequestDescription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReleaseCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Creates a new release')
            ->addArgument(
                'repo',
                InputArgument::REQUIRED,
                'The repo to which the release will be created'
            )
            ->addArgument(
                'base',
                InputArgument::REQUIRED,
                'The branch to which you want to merge (release) things to'
            )
            ->addArgument(
                'head',
                InputArgument::REQUIRED,
                'The branch from which you want to merge (release) things from'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mergedPullRequests = $this->getMergedPullRequests(
            $input->getArgument('repo'),
            $input->getArgument('base'),
            $input->getArgument('head')
        );

        $pullRequestDescription = new PullRequestDescription($mergedPullRequests);

        $output->writeln($pullRequestDescription->render());
    }

    /**
     * @param string $repo
     * @param string $base
     * @param string $head
     *
     * @return PullRequest[]
     */
    private function getMergedPullRequests(string $repo, string $base, string $head): array
    {
        $repository = new Repository($this->getGithubClient(), $repo);

        $baseBranch = $repository->getBranch($base);
        $headBranch = $repository->getBranch($head);

        $commits = $repository->compareBranches($baseBranch, $headBranch);

        $mergedPullRequests = [];
        foreach ($commits as $commit) {
            $pullRequestID = $commit->getMergedPullRequestID();

            if (!$pullRequestID) {
                continue;
            }

            $pullRequest = $repository->getPullRequestById($pullRequestID);
            $mergedPullRequests[] = $pullRequest;
        }

        return $mergedPullRequests;
    }

    private function getGithubClient()
    {
        $client = new \Github\Client();

        //$client->authenticate(
        //    $token,
        //    null,
        //    \Github\Client::AUTH_HTTP_TOKEN
        //);

        return $client;
    }
}
