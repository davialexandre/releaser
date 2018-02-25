# Releaser

Releaser is a small utility to create Release's descriptions where a Release is basically the merge from branch B to branch A. It will check the commits between the two branches, verify which ones are for Pull Requests merges and list all the Pull Requests, grouped by their author's usernames.

## Installation

Just download the .phar file from the latest release at https://github.com/davialexandre/releaser/releases and place it somewhere in your system (preferably a folder in your PATH). Don't forget to give it +x permission.

## Configuration

The Github API has a limit of 60 requests per hour for anonymous users. This limit can be reached quickly by using the tool. To avoid that, you can give it a Personal Access Token for authentication. Such tokens can be created at https://github.com/settings/tokens. 

The tool will read the token from a configuration file, which can be loaded from any of these places (in this order):

- A file path set in the `RELEASER_CONFIG` environment variable
- A `releaser.json` file in any of these folders:
  - The `.releaser` folder in the current user's home folder
  - The `.config/releaser` folder in the current user's home folder
  - The `releaser` folder inside the folder set in the XDG_CONFIG_HOME  

The configuration file a simple json object. Right now, the only supported option is the one for the access token. Here is an example of a configuration file:

```json
{
  "github_auth_token": "YOUR TOKEN HERE"
}
```

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
