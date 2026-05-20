<?php

declare(strict_types=1);

namespace JmeEph\Service;

use JmeEph\FFI\JmeEphFFI;

class JmeService
{
    private JmeEphFFI $ffi;
    private string $calculationPath;

    public function __construct(JmeEphFFI $ffi, string $calculationPath = 'native')
    {
        $this->ffi = $ffi;
        $this->calculationPath = $calculationPath;
    }

    public function calc(float $jd_et, int $body, int $flags = JmeEphFFI::JME_CALC_NONE, &$results = null, &$error = null)
    {
        $results = $results ?? $this->ffi->getFFI()->new("double[6]");
        $error = $error ?? $this->ffi->getFFI()->new("char[256]");

        if ($this->calculationPath === 'moshier') {
            return $this->ffi->getFFI()->jme_moshier_planet_state($jd_et, $body, $results);
        }

        if ($this->calculationPath === 'vsop87') {
            return $this->ffi->getFFI()->jme_vsop87_planet_state($jd_et, $body, $results);
        }

        // The native C core owns JPL/fallback policy for jme_calc().
        return $this->ffi->getFFI()->jme_calc($jd_et, $body, $flags, $results, $error);
    }
}
