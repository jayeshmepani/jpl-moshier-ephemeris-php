# JPL Moshier Ephemeris PHP FFI

PHP 8.3+ FFI wrapper for the independent JPL Moshier Ephemeris C library.

This package wraps the project-owned `jme_*` C API from `/home/shreesoftech/projects/test1/astro_packages/jpl-ephemeris-`. It is not a Swiss Ephemeris wrapper and should not treat `swe_*` names or `SE_*` constants as the primary API contract.

## Contract

- Primary public functions: `jme_*`
- Primary public constants: `JME_*`
- Current wrapper target: all 204 public `jme_*` functions tracked by the native API inventory
- Native headers contain 462 `JME_*` tokens including two header guards; the PHP wrapper exposes the semantic native constants and preserves existing compatibility aliases where present
- Native backend: `libjme.so`, `libjme.dylib`, or `jme.dll`
- No hidden rounding, output reshaping, or dropped status/error buffers
- Incomplete native behavior should return `JME_ERR` from the C library rather than pretending to provide production ephemeris output

Swiss Ephemeris names may only belong in a separate migration adapter if one is deliberately added. The main wrapper, examples, and tests should use JME naming.

## Engine Selection

The low-level `JmeEphFFI` class exposes every native function directly. The optional Laravel-style `JmeService::calc()` convenience method sets `ENGINE=...` through `jme_set_astro_models()` and then calls `jme_calc()`:

- `AUTO`: let the native C core choose its standard JPL/fallback policy
- `JPL`: require JPL kernel-backed behavior
- `MOSHIER`: force Moshier analytical behavior
- `VSOP_ELP_MEEUS`: force VSOP87/ELP2000/Meeus analytical behavior
- `ANALYTICAL`: use the native analytical engine mode

Backward-compatible aliases accepted by `JmeService`:

- `native` -> `AUTO`
- `vsop87` -> `VSOP_ELP_MEEUS`

Use direct `jme_jpl_*` calls for raw JPL/CALCEPH kernel access. The PHP layer does not normalize or reinterpret native outputs.

## Requirements

- PHP `^8.3`
- PHP FFI extension (`ext-ffi`)
- A compiled JME shared library

By default, the wrapper loads the bundled platform library from this package, for example:

```text
/home/shreesoftech/projects/test1/astro_packages/user-ffi-wrappers/jpl-moshier-ephemeris-php/libs/linux-x64/libjme.so
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
composer verify:surface
composer test
```

The test suite verifies the PHP wrapper against the JME-native contract, including function inventory coverage, constant inventory coverage, key `JME_*` values, and convenience calculation paths. Set `JME_SOURCE_PATH` if the native source tree is not at the default local path.

`composer verify:surface` performs an exact low-level surface audit of the generated wrapper:

- `204` tracked `jme_*` declarations compared against the native header prototypes
- `462` `JME_*` constants compared against native header values
- numeric constants cross-checked through a compiled C probe instead of trusting PHP generation alone
