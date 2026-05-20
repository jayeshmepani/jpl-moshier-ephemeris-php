<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JME Library Path
    |--------------------------------------------------------------------------
    | Path to the compiled libjme.so / libjme.dylib / jme.dll file.
    */
    'library_path' => env('JME_LIBRARY_PATH', '/home/shreesoftech/projects/test1/astro_packages/jpl-ephemeris-/build/libjme.so'),

    /*
    |--------------------------------------------------------------------------
    | Calculation Path
    |--------------------------------------------------------------------------
    | This controls only the PHP convenience JmeService::calc() method.
    | The low-level FFI wrapper always exposes every native jme_* function.
    |
    | 'native'  -> Call jme_calc(); native C decides JPL/fallback behavior.
    | 'moshier' -> Call jme_moshier_planet_state(); currently planets only.
    | 'vsop87'  -> Call jme_vsop87_planet_state(); currently analytical bodies.
    |
    | Use direct jme_jpl_* functions for raw JPL/CALCEPH kernel access.
    */
    'calculation_path' => env('JME_CALCULATION_PATH', env('JME_ENGINE', 'native')),

    /*
    |--------------------------------------------------------------------------
    | Ephemeris Path (for JPL Kernels)
    |--------------------------------------------------------------------------
    */
    'ephemeris_path' => env('JME_EPHEMERIS_PATH', storage_path('app/ephemeris')),
];
