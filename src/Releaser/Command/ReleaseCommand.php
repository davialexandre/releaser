<?php

namespace Releaser\Command;

use Releaser\ConfigFactory;
use Releaser\Release;
use Releaser\View\ReleaseDescription;
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
        $client = $this->getGithubClient();
        $release = new Release(
            $client,
            $input->getArgument('repo'),
            $input->getArgument('base'),
            $input->getArgument('head')
        );

        if($release->getTotalNumberOfCommits() > $release->getNumberOfCommits()) {
            $output->writeln($this->getApiLimitWarningBlock());
        }

        $releaseDescription = new ReleaseDescription($release);
        $output->writeln($releaseDescription->render());
    }

    /**
     * @return \Github\Client
     */
    private function getGithubClient()
    {
        $client = new \Github\Client();
        $config = ConfigFactory::create();

        if($config->getGithubAuthToken()) {
            var_dump('Authenticating');
            $client->authenticate(
                $config->getGithubAuthToken(),
                null,
                \Github\Client::AUTH_HTTP_TOKEN
            );
        }

        return $client;
    }

    private function getApiLimitWarningBlock()
    {
        $formatter = $this->getHelper('formatter');
        $message = [
            'Warning',
            'Due to the Github API limits, not all commits have been processed for this release. Some Pull Requests might not have been included'
        ];

        return $formatter->formatBlock($message, 'info');
    }
}
