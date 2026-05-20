# JPL Moshier Ephemeris PHP FFI

PHP 8.3+ FFI wrapper for the independent JPL Moshier Ephemeris C library.

This package wraps the project-owned `jme_*` C API from `/home/shreesoftech/projects/test1/astro_packages/jpl-ephemeris-`. It is not a Swiss Ephemeris wrapper and should not treat `swe_*` names or `SE_*` constants as the primary API contract.

## Contract

- Primary public functions: `jme_*`
- Primary public constants: `JME_*`
- Current wrapper coverage: 191 native `jme_*` functions and all 460 constants tracked by the native API inventory
- Native backend: `libjme.so`, `libjme.dylib`, or `jme.dll`
- No hidden rounding, output reshaping, or dropped status/error buffers
- Incomplete native behavior should return `JME_ERR` from the C library rather than pretending to provide production ephemeris output

Swiss Ephemeris names may only belong in a separate migration adapter if one is deliberately added. The main wrapper, examples, and tests should use JME naming.

## Calculation Paths

The low-level `JmeEphFFI` class exposes every native function directly. The optional Laravel-style `JmeService::calc()` convenience method supports these paths:

- `native`: calls `jme_calc()` and lets the C core decide JPL/fallback behavior
- `moshier`: calls `jme_moshier_planet_state()` directly; currently a partial planetary analytical path
- `vsop87`: calls `jme_vsop87_planet_state()` directly; currently a partial analytical path

Use direct `jme_jpl_*` calls for raw JPL/CALCEPH kernel access. Do not treat the PHP convenience path as proof that every high-level algorithm is complete; native status is governed by the C library’s return codes and error buffers.

## Requirements

- PHP `^8.3`
- PHP FFI extension (`ext-ffi`)
- A compiled JME shared library

By default, local development uses:

```text
/home/shreesoftech/projects/test1/astro_packages/jpl-ephemeris-/build/libjme.so
```

Override it with:

```bash
export JME_LIBRARY_PATH=/path/to/libjme.so
```

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
composer test
```

The test suite verifies the PHP wrapper against the JME-native contract, including function inventory coverage, constant inventory coverage, key `JME_*` values, and convenience calculation paths. Set `JME_SOURCE_PATH` if the native source tree is not at the default local path.
