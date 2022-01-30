# Change Log

All notable changes to this project will be documented in this file. This project adheres to
[Semantic Versioning](http://semver.org/) and [this changelog format](http://keepachangelog.com/).

## Unreleased

### Added

- Package now supports Laravel 9.
- Added support for `cloudcreativity/json-api-testing` version 4.0.
- The test builder `filter()` method can now be called multiple times, with filters merged into existing filters.
- New test builder `filterIds()` method. This sets the value of an id filter, converting any `UrlRoutable` objects (i.e
  models) to their route key.

## [1.0.0] - 2021-07-31

### Changed

- Updated `cloudcreativity/json-api-testing` dependency to `3.3.0`.

## [1.0.0-beta.1] - 2021-03-30

Initial beta release. No changes since `alpha.1`.

## [1.0.0-alpha.1] - 2021-01-25

Initial release.
