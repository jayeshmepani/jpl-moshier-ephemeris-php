<?php

declare(strict_types=1);

namespace SwissEph\Service;

use Illuminate\Support\Facades\Facade;
use SwissEph\FFI\SwissEphFFI;

/**
 * Laravel Facade for Swiss Ephemeris FFI.
 *
 * Provides static-like access to SwissEphFFI methods through Laravel's facade pattern.
 * All calls delegate directly to the raw SwissEphFFI singleton instance. The facade
 * does not add convenience wrappers, reshape return values, or hide C output buffers.
 *
 * @author Jayesh Patel <jayeshmepani777@gmail.com>
 *
 * @mixin \SwissEph\FFI\SwissEphFFI
 *
 * @see \SwissEph\FFI\SwissEphFFI
 */
final class SwissEphFacade extends Facade
{
    /** Get registered name in service container. */
    protected static function getFacadeAccessor(): string
    {
        return 'swisseph';
    }
}
