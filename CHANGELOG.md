# Change Log

All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## [1.1.1] - 2022-02-27

### Fixed

- The Symfony response class can return `false` for the response content. This caused a fatal error when the content was
  passed to JSON:API assertions - as the assertion methods type-hint the content as a `string` in version 4 of that
  dependency. This has been fixed by adding a `TestResponse::getContent()` method that returns an empty string if the
  Symfony method returns `false`.

## [1.1.0] - 2022-02-08

### Added

- Package now supports Laravel 9.
- Added support for `cloudcreativity/json-api-testing` version 4.0.
- Values passed to the test builder `filter()` and `page` methods can now include `UrlRoutable` objects (i.e. models).
  Routable objects are converted to their route key value. This also applies when a filter or page value is set via the
  `query()` method.

### Deprecated

- The following page assertions will be removed in the next major release. You should use the fluent methods instead to
  assert the resources fetched, meta and links. Deprecated methods are:
    - `assertFetchedPage()` - use `assertFetchedMany()`, `assertMeta()` and `assertLinks()`.
    - `assertFetchedPageInOrder()` - use `assertFetchedManyInOrder()`, `assertMeta()` and `assertLinks()`.
    - `assertFetchedEmptyPage()` - use `assertFetchedNone()`, `assertMeta()` and `assertLinks()`.

## [1.0.0] - 2021-07-31

### Changed

- Updated `cloudcreativity/json-api-testing` dependency to `3.3.0`.

## [1.0.0-beta.1] - 2021-03-30

Initial beta release. No changes since `alpha.1`.

## [1.0.0-alpha.1] - 2021-01-25

Initial release.
