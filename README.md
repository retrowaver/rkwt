# rkwt

Symfony 4 web app that automatically checks Allegro.pl new auctions, looking for items that match users' search criteria (and sending notifications afterwards). Users may use it to either find good deals (before anyone else finds them) or to get a notice whenever some rare item shows up.

### How it works?

User can define one or more searches. A search consists of a set of filters, that will be used to query Allegro WebAPI for new auctions. Each time the `bin/console app:update-searches` command is called, app checks for new auctions matching search criteria. If it finds at least one, sends a notification to user (as of now only email notifications are supported).

`bin/console app:update-searches` command should be added to crontab for continuons checking.

### Note

App was intended to be used by a close group of friends. It uses a single Allegro WebAPI key and doesn't limit users when it comes to the amount of sent requests or maximum number of searches (something to keep in mind in case of making it public).

## Getting started

Alter `.env` (db & mailer configuration) and `config/services.yaml` (Allegro WebAPI key) files.

Run `bin/console app:update-categories`.

Add `bin/console app:update-searches` to crontab.


### Prerequisites

64-bit PHP 7.1.3+

## Built With

* [Symfony 4](https://symfony.com/4)
* [Bootstrap 4](https://getbootstrap.com/)
* some js libraries (including [Handlebars](https://handlebarsjs.com/), [jQuery](https://jquery.com/), [jQuery.i18n](https://github.com/wikimedia/jquery.i18n) and more)

## To do

* better preview of values of added filters
* when adding filters with type `int` or `float`, input should be of type `number` (but it's not that simple due to comma / period handling, I should create an issue with this I guess)
* automatic categories update (whenever needed)
* some way of dealing with searches that were valid in the past, but are not valid anymore (due to category or filter changes on Allegro itself)