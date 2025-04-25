# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] - 2025-04-25

### Changed

- **Breaking:** Don't save `php://temp` or `php://memory` content ([#2](https://github.com/SyberIsle/mock-php-stream/pull/2)) (@pavlyuts)
- Added types based on [PHP: streamWrapper](https://www.php.net/manual/en/class.streamwrapper.php)

### Removed

- **Breaking:** Support for PHP < 8.0

### Fixed

- Fixed creation of dynamic property for PHP 8.2+ ([#3](https://github.com/SyberIsle/mock-php-stream/issues/3))

## [1.1.0] - 2020-08-24

### Added

- `fseek` support ([#1](https://github.com/SyberIsle/mock-php-stream/pull/1)) (@lebedevsergey)

## 1.0.1 - 2018-06-12

## 1.0.0 - 2018-06-09

_Initial release_