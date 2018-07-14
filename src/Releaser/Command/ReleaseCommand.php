<?php

namespace Releaser\Command;

use Releaser\ConfigFactory;
use Releaser\Release;
use Releaser\View\ReleaseDescription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates a new Release
 *
 * @package Releaser\Command
 */
class ReleaseCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('release')
            ->setDescription('Creates a new release')
            ->setHelp('Example usage: releaser release davialexandre/releaser master develop')
            ->addArgument(
                'repo',
                InputArgument::REQUIRED,
                'The repo to which the release will be created, in a format like "davialexandre/release" (username/repository)'
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
            )
            ->addOption(
                'exclude-sub-pull-requests',
                'x',
                InputOption::VALUE_NONE,
                'When passed, only Pull Requests sent to the head branch will be included in the description'
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

        $releaseDescription = new ReleaseDescription(
            $release,
            $input->getOption('exclude-sub-pull-requests')
        );
        $output->writeln($releaseDescription->render());
    }

    /**
     * @return \Github\Client
     */
    private function getGithubClient(): \Github\Client
    {
        $client = new \Github\Client();
        $config = ConfigFactory::create();

        if($config->getGithubAuthToken()) {
            $client->authenticate(
                $config->getGithubAuthToken(),
                null,
                \Github\Client::AUTH_HTTP_TOKEN
            );
        }

        return $client;
    }
}
