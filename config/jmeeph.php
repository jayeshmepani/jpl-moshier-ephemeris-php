<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | JME Library Path
    |--------------------------------------------------------------------------
    | Path to the compiled libjme.so / libjme.dylib / jme.dll file.
    */
    'library_path' => env('JME_LIBRARY_PATH', dirname(__DIR__) . '/libs/linux-x64/libjme.so'),

    /*
    |--------------------------------------------------------------------------
    | Native Engine Selection
    |--------------------------------------------------------------------------
    | This controls only the optional PHP convenience JmeService::calc() method.
    | The low-level FFI wrapper always exposes every native jme_* function.
    |
    | 'AUTO'           -> Let the native C core choose its normal engine policy.
    | 'JPL'            -> Require JPL kernel-backed behavior.
    | 'MOSHIER'        -> Force Moshier analytical behavior.
    | 'VSOP_ELP_MEEUS' -> Force VSOP87/ELP2000/Meeus analytical behavior.
    | 'ANALYTICAL'     -> Native analytical engine mode.
    |
    | Backward-compatible aliases accepted by JmeService:
    | 'native' -> AUTO
    | 'vsop87' -> VSOP_ELP_MEEUS
    */
    'engine' => env('JME_ENGINE', env('JME_CALCULATION_PATH', 'AUTO')),

    /*
    |--------------------------------------------------------------------------
    | Ephemeris Path (for JPL Kernels)
    |--------------------------------------------------------------------------
    */
    'ephemeris_path' => env('JME_EPHEMERIS_PATH', storage_path('app/ephemeris')),
];
