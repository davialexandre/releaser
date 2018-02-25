# Releaser

Releaser is a small utility to create Release's descriptions where a Release is basically the merge from branch B to branch A. It will check the commits between the two branches, verify which ones are for Pull Requests merges and list all the Pull Requests, grouped by their author's usernames.

## Installation

Just download the .phar file from the latest release at https://github.com/davialexandre/releaser/releases and place it somewhere in your system (preferably a folder in your PATH). Don't forget to give it +x permission.

## Usage

Let's say you want to release the work from the `develop` branch of the `company/project` repository to its `master` branch. This is what the command will look like:

```
releaser.phar release "company/project" master develop
```

The output will be something like this

```
Pull requests included:

@author1
- Some pull request title here: https://github.com/company/project/pull/1
- Another pull request title here: https://github.com/company/project/pull/2

@author2
- My first pull request: https://github.com/company/project/pull/3
- My second pull request: https://github.com/company/project/pull/4
```

## Known limitations

- To get a list of the commits included in a release, we use the Github's [compare API](https://developer.github.com/v3/repos/commits/#compare-two-commits), which only returns up to 250 commits. If there more commits than that between the two branches, the release description might not include all the Pull Requests
- It's not possible two release across forks. The two given branches must exist in the given repository
