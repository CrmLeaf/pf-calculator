# Changelog

Notable changes to `crmleaf/pf-calculator`.

Format per [Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versioning
per [Semantic Versioning](https://semver.org/spec/v2.0.0.html) - with one extra
rule this package observes, because it computes statutory figures:

> **Any change that alters a published result is at minimum a minor release**,
> and is listed under `Changed` with the notification, circular or Act section
> that prompted it.

## [Unreleased]

## [1.0.0] - 2026-08-12

### Added

- Initial release. Splits the 12% employer share into EPS and EPF the way EPFO does it, applies the wage ceiling only where the statute applies it, and adds EDLI and administration charges.

### Statutory basis

- Employees' Provident Funds and Miscellaneous Provisions Act 1952, with the EPS share capped at the ₹15,000 wage ceiling under paragraph 11(3) of the Employees' Pension Scheme 1995 even where EPF is contributed on the full basic.

[Unreleased]: https://github.com/crmleaf/pf-calculator/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/crmleaf/pf-calculator/releases/tag/v1.0.0
