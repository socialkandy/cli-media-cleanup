socialkandy/wp-cli-media-cleanup
====================



[![Build Status](https://travis-ci.org/socialkandy/wp-cli-media-cleanup.svg?branch=master)](https://travis-ci.org/socialkandy/wp-cli-media-cleanup)

Quick links: [Using](#using) | [Installing](#installing) | [Contributing](#contributing) | [Support](#support)

## Using

This package implements the following commands:

### wp media cleanup

Clean all invalid attachments and files from your site.

~~~
wp media cleanup [--dry-run] [--yes] [--files-only] [--attachments-only]
~~~

Analyzes all files in your `wp-uploads` folder looking for references in attachments metadata. And it also checks if all attachments files exists.

**OPTIONS**

	[--dry-run]
		Only search and analyze, but won't delete anything.
	[--yes]
		Answer yes to confirmation messages.
	[--files-only]
		Search and delete only invalid files.
	[--attachments-only]
		Search and delete only invalid attachments.

**EXAMPLES**

    $ wp media cleanup
    Scanning attachments...
	You have 12 attachments with no file associated.
	Are you sure you want to delete 12 invalid attachments? [y/n] y
	Success: Deleted 12 invalid attachments.
	Scanning uploads folder: /srv/www/cb-int.com/current/web/app/uploads/
	You have 230 files in total.
	There are 158 files with valid attachments.
	There are 72 files with no attachment associated.
	Are you sure you want to delete 72 invalid files? [y/n] y
	Success: Deleted 72 invalid files.

	$ wp media cleanup --attachments-only --dry-run
	Scanning attachments...
	You have 12 attachments with no file associated.

	$ wp media cleanup --attachments-only --yes
	Scanning attachments...
	You have 12 attachments with no file associated.
	Success: Deleted 12 invalid attachments.

## Installing

Installing this package requires WP-CLI v1.1.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with:

	wp package install git@github.com:socialkandy/wp-cli-media-cleanup.git

## Contributing

We appreciate you taking the initiative to contribute to this project.

Contributing isn’t limited to just code. We encourage you to contribute in the way that best fits your abilities, by writing tutorials, giving a demo at your local meetup, helping other users with their support questions, or revising our documentation.

For a more thorough introduction, [check out WP-CLI's guide to contributing](https://make.wordpress.org/cli/handbook/contributing/). This package follows those policy and guidelines.

### Reporting a bug

Think you’ve found a bug? We’d love for you to help us get it fixed.

Before you create a new issue, you should [search existing issues](https://github.com/socialkandy/wp-cli-media-cleanup/issues?q=label%3Abug%20) to see if there’s an existing resolution to it, or if it’s already been fixed in a newer version.

Once you’ve done a bit of searching and discovered there isn’t an open or fixed issue for your bug, please [create a new issue](https://github.com/socialkandy/wp-cli-media-cleanup/issues/new). Include as much detail as you can, and clear steps to reproduce if possible. For more guidance, [review our bug report documentation](https://make.wordpress.org/cli/handbook/bug-reports/).

### Creating a pull request

Want to contribute a new feature? Please first [open a new issue](https://github.com/socialkandy/wp-cli-media-cleanup/issues/new) to discuss whether the feature is a good fit for the project.

Once you've decided to commit the time to seeing your pull request through, [please follow our guidelines for creating a pull request](https://make.wordpress.org/cli/handbook/pull-requests/) to make sure it's a pleasant experience. See "[Setting up](https://make.wordpress.org/cli/handbook/pull-requests/#setting-up)" for details specific to working on this package locally.

## Support

Github issues aren't for general support questions, but there are other venues you can try: http://wp-cli.org/#support


*This README.md is generated dynamically from the project's codebase using `wp scaffold package-readme` ([doc](https://github.com/wp-cli/scaffold-package-command#wp-scaffold-package-readme)). To suggest changes, please submit a pull request against the corresponding part of the codebase.*
