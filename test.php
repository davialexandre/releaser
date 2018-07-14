<?php

use Releaser\ConfigFactory;
use Releaser\Github\Branch;
use Releaser\Github\Repository;

require_once "vendor/autoload.php";

$client = new \Github\Client();
$config = ConfigFactory::create();

if($config->getGithubAuthToken()) {
    $client->authenticate(
        $config->getGithubAuthToken(),
        null,
        \Github\Client::AUTH_HTTP_TOKEN
    );
}

$repository = new Repository($client, 'davialexandre/civihr');
$master = $repository->getBranch('master');
var_dump($master);
die();
$staging = $repository->getBranch('staging');

//var_dump('master is at ' . $master->getCommit()->getHash());
//var_dump('staging is at ' . $staging->getCommit()->getHash());
foreach ($repository->compareBranches($master, $staging) as $commit) {
//    echo $commit->getHash() . PHP_EOL;
}
