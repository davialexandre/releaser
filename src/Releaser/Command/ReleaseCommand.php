<?php

namespace Releaser\Command;

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

        $releaseDescription = new ReleaseDescription($release);
        $output->writeln($releaseDescription->render());
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
