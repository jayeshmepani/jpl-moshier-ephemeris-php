# JPL Moshier Ephemeris PHP FFI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jayeshmepani/jpl-moshier-ephemeris-php.svg?style=flat-square)](https://packagist.org/packages/jayeshmepani/jpl-moshier-ephemeris-php)
[![Total Downloads](https://img.shields.io/packagist/dt/jayeshmepani/jpl-moshier-ephemeris-php.svg?style=flat-square)](https://packagist.org/packages/jayeshmepani/jpl-moshier-ephemeris-php)
[![PHP Version Require](https://img.shields.io/packagist/php-v/jayeshmepani/jpl-moshier-ephemeris-php?style=flat-square)](https://packagist.org/packages/jayeshmepani/jpl-moshier-ephemeris-php)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

PHP 8.3+ FFI wrapper for the independent JPL Moshier Ephemeris C library.

This package wraps the project-owned `jme_*` C API from the JPL Moshier Ephemeris native library.

## Contract

- Primary public functions: `jme_*`
- Primary public constants: `JME_*`
- Current wrapper target: all 204 public `jme_*` functions tracked by the native API inventory
- Native headers contain 462 `JME_*` tokens including two header guards; the PHP wrapper exposes the semantic native constants and preserves existing compatibility aliases where present
- Native backend: `libjme.so`, `libjme.dylib`, or `jme.dll`
- No hidden rounding, output reshaping, or dropped status/error buffers
- Incomplete native behavior should return `JME_ERR` from the C library rather than pretending to provide production ephemeris output

## Engine Selection

The low-level `JmeEphFFI` class exposes every native function directly. The optional Laravel-style `JmeService::calc()` convenience method sets `ENGINE=...` through `jme_set_astro_models()` and then calls `jme_calc()`:

- `AUTO`: let the native C core choose its standard JPL/fallback policy
- `JPL`: require JPL kernel-backed behavior
- `MOSHIER`: force Moshier analytical behavior
- `VSOP_ELP_MEEUS`: force VSOP87/ELP2000/Meeus analytical behavior

Use direct `jme_jpl_*` calls for raw JPL/CALCEPH kernel access. The PHP layer does not normalize or reinterpret native outputs.

## Requirements

- PHP `^8.3`
- PHP FFI extension (`ext-ffi`)
- A compiled JME shared library
- The matching CALCEPH runtime library when using the JPL/CALCEPH backend

By default, the wrapper loads the bundled platform library from this package, for example:

```text
vendor/jayeshmepani/jpl-moshier-ephemeris-php/libs/linux-x64/libjme.so
```

Override it with:

```bash
export JME_LIBRARY_PATH=/path/to/libjme.so
```

## Runtime Libraries and Kernels

Composer installs prebuilt runtime archives from this repository's GitHub releases when a local library is not already present. Each runtime archive contains:

- JME: `jme.dll`, `libjme.so`, or `libjme.dylib`
- CALCEPH: the platform runtime library required by JPL kernel mode

JPL `.bsp` kernels are not shipped in this package. Download them separately from the native JME kernel release:

```text
https://github.com/jayeshmepani/jpl-ephemeris/releases/tag/jpl-kernels
```

Supported kernel choices:

- `de440s.bsp` - small
- `de440.bsp` - medium
- `de441.bsp` - large, published as split release parts because the full file exceeds GitHub's single asset size limit

## Quick Start

```php
use FFI;
use JmeEph\FFI\JmeEphFFI;

$jme = new JmeEphFFI();

$jd = $jme->jme_julian_day(
    2000,
    1,
    1,
    12.0,
    JmeEphFFI::JME_CALENDAR_GREGORIAN
);

$xx = $jme->getFFI()->new('double[6]');
$error = $jme->getFFI()->new('char[256]');

$result = $jme->jme_calc_ut(
    $jd,
    JmeEphFFI::JME_BODY_SUN,
    JmeEphFFI::JME_CALC_NONE,
    $xx,
    $error
);

if ($result === JmeEphFFI::JME_OK) {
    echo "Sun longitude: {$xx[0]}\n";
} else {
    echo 'JME error: ' . FFI::string($error) . "\n";
}
```

## Test

```bash
composer install
composer verify:surface
composer test
```

The test suite verifies the PHP wrapper against the JME-native contract, including function inventory coverage, constant inventory coverage, key `JME_*` values, and convenience calculation paths. Set `JME_SOURCE_PATH` if the native source tree is not at the default local path.

`composer verify:surface` performs an exact low-level surface audit of the generated wrapper:

- `204` tracked `jme_*` declarations compared against the native header prototypes
- `462` `JME_*` constants compared against native header values
- numeric constants cross-checked through a compiled C probe instead of trusting PHP generation alone
