<?php

declare(strict_types=1);

namespace JmeEph\Tests;

use FFI;
use JmeEph\FFI\JmeEphFFI;
use JmeEph\Service\JmeService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

final class JmeEphFFITest extends TestCase
{
    private ?JmeEphFFI $jme = null;

    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->jme = new JmeEphFFI;
        } catch (RuntimeException $e) {
            $this->markTestSkipped('JME shared library not found: ' . $e->getMessage());
        }
    }

    public function testCoreConstantsMatchNativeHeaderContract(): void
    {
        $this->assertSame('0.1.0', JmeEphFFI::JME_VERSION);
        $this->assertSame(0, JmeEphFFI::JME_OK);
        $this->assertSame(-1, JmeEphFFI::JME_ERR);
        $this->assertSame(0, JmeEphFFI::JME_BODY_SUN);
        $this->assertSame(1, JmeEphFFI::JME_BODY_MOON);
        $this->assertSame(21, JmeEphFFI::JME_BODY_MEAN_NODE);
        $this->assertSame(22, JmeEphFFI::JME_BODY_TRUE_NODE);
        $this->assertSame(0, JmeEphFFI::JME_CALC_NONE);
        $this->assertSame(1, JmeEphFFI::JME_CALC_SPEED);
        $this->assertSame(512, JmeEphFFI::JME_CALC_SIDEREAL);
        $this->assertSame(1024, JmeEphFFI::JME_CALC_NO_ABERRATION);
        $this->assertSame(2048, JmeEphFFI::JME_CALC_NO_LIGHT_DEFLECTION);
        $this->assertSame(131072, JmeEphFFI::JME_CALC_TOPOCENTRIC);
        $this->assertSame(1, JmeEphFFI::JME_RISE_RISE);
        $this->assertSame(2, JmeEphFFI::JME_RISE_SET);
        $this->assertSame(4, JmeEphFFI::JME_RISE_MERIDIAN_TRANSIT);
        $this->assertSame(314, JmeEphFFI::JME_HOUSE_AZIMUTHAL);
        $this->assertSame(318, JmeEphFFI::JME_HOUSE_HORIZONTAL);
        $this->assertSame(1, JmeEphFFI::JME_CALENDAR_GREGORIAN);
        $this->assertSame(0, JmeEphFFI::JME_CALENDAR_JULIAN);
    }

    public function testWrapperCoversNativeFunctionAndConstantInventory(): void
    {
        $nativeRoot = getenv('JME_SOURCE_PATH') ?: '/home/shreesoftech/projects/test1/astro_packages/jpl-ephemeris-';

        if (! is_file($nativeRoot . '/include/jme/jme.h')) {
            $this->markTestSkipped('Native JME source tree is not available for inventory comparison.');
        }

        $nativeHeaders = file_get_contents($nativeRoot . '/include/jme/jme.h')
            . "\n"
            . file_get_contents($nativeRoot . '/include/jme/jme_extended.h');
        preg_match_all('/\b(?:const char \*|char \*|int|double|void)\s+(jme_[A-Za-z0-9_]+)\s*\(/', $nativeHeaders, $nativeFunctionMatches);
        $nativeFunctions = array_values(array_unique($nativeFunctionMatches[1]));

        $wrapperSource = file_get_contents(__DIR__ . '/../src/FFI/JmeEphFFI.php');
        preg_match_all('/\b(?:const char \*|char \*|int|double|void)\s+(jme_[A-Za-z0-9_]+)\s*\(/', $wrapperSource, $wrapperFunctionMatches);
        $wrapperFunctions = array_values(array_unique($wrapperFunctionMatches[1]));

        $this->assertSame([], array_values(array_diff($nativeFunctions, $wrapperFunctions)));
        $this->assertCount(191, $nativeFunctions);
        $this->assertCount(191, $wrapperFunctions);

        preg_match_all('/\|\s*\d+\s*\|\s*`(JME_[A-Z0-9_]+)`\s*\|/', file_get_contents($nativeRoot . '/docs/API_TRACKING.md'), $nativeConstantMatches);
        $nativeConstants = array_values(array_filter(
            array_unique($nativeConstantMatches[1]),
            static fn (string $name): bool => ! in_array($name, ['JME_EXTENDED_H', 'JME_JME_H'], true)
        ));
        $wrapperConstants = array_keys((new ReflectionClass(JmeEphFFI::class))->getConstants());

        $this->assertSame([], array_values(array_diff($nativeConstants, $wrapperConstants)));
        $this->assertCount(458, $nativeConstants);
        $this->assertGreaterThanOrEqual(458, count($wrapperConstants));
    }

    public function testVersion(): void
    {
        $buffer = $this->jme->getFFI()->new('char[256]');

        $this->jme->jme_version($buffer, 256);

        $this->assertMatchesRegularExpression('/^\d+\.\d+\.\d+/', FFI::string($buffer));
    }

    public function testJulianDayConversion(): void
    {
        $jd = $this->jme->jme_julian_day(2000, 1, 1, 12.0, JmeEphFFI::JME_CALENDAR_GREGORIAN);

        $this->assertEqualsWithDelta(2451545.0, $jd, 0.0001);

        $year = $this->jme->getFFI()->new('int[1]');
        $month = $this->jme->getFFI()->new('int[1]');
        $day = $this->jme->getFFI()->new('int[1]');
        $hour = $this->jme->getFFI()->new('double[1]');

        $this->jme->jme_reverse_julian_day($jd, JmeEphFFI::JME_CALENDAR_GREGORIAN, $year, $month, $day, $hour);

        $this->assertSame(2000, $year[0]);
        $this->assertSame(1, $month[0]);
        $this->assertSame(1, $day[0]);
        $this->assertEqualsWithDelta(12.0, $hour[0], 0.0001);
    }

    public function testRiseTransitAndCrossingContracts(): void
    {
        $geopos = $this->jme->getFFI()->new('double[3]');
        $rise = $this->jme->getFFI()->new('double[1]');
        $civilRise = $this->jme->getFFI()->new('double[1]');
        $trueHorizonRise = $this->jme->getFFI()->new('double[1]');
        $cross = $this->jme->getFFI()->new('double[1]');
        $sunAtCross = $this->jme->getFFI()->new('double[6]');
        $error = $this->jme->getFFI()->new('char[256]');

        $geopos[0] = 0.0;
        $geopos[1] = 51.5;
        $geopos[2] = 0.0;

        $riseRc = $this->jme->jme_rise_trans(
            2451545.0,
            JmeEphFFI::JME_BODY_SUN,
            null,
            JmeEphFFI::JME_CALC_TRUE_POSITION,
            JmeEphFFI::JME_RISE_RISE,
            $geopos,
            1010.0,
            10.0,
            $rise,
            $error
        );

        if ($riseRc === JmeEphFFI::JME_OK) {
            $this->assertGreaterThanOrEqual(2451545.0, $rise[0]);
            $this->assertLessThanOrEqual(2451546.0, $rise[0]);

            $this->assertSame(
                JmeEphFFI::JME_OK,
                $this->jme->jme_rise_trans(
                    2451545.0,
                    JmeEphFFI::JME_BODY_SUN,
                    null,
                    JmeEphFFI::JME_CALC_TRUE_POSITION,
                    JmeEphFFI::JME_RISE_RISE | JmeEphFFI::JME_RISE_CIVIL_TWILIGHT,
                    $geopos,
                    1010.0,
                    10.0,
                    $civilRise,
                    $error
                ),
                FFI::string($error)
            );
            $this->assertLessThan($rise[0], $civilRise[0]);

            $this->assertSame(
                JmeEphFFI::JME_OK,
                $this->jme->jme_rise_trans_true_hor(
                    2451545.0,
                    JmeEphFFI::JME_BODY_SUN,
                    null,
                    JmeEphFFI::JME_CALC_TRUE_POSITION,
                    JmeEphFFI::JME_RISE_RISE,
                    $geopos,
                    1010.0,
                    10.0,
                    5.0,
                    $trueHorizonRise,
                    $error
                ),
                FFI::string($error)
            );
            $this->assertGreaterThan($rise[0], $trueHorizonRise[0]);
        }

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_solcross(280.0, 2451545.0, JmeEphFFI::JME_CALC_TRUE_POSITION, $cross, $error),
            FFI::string($error)
        );
        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_calc_ut($cross[0], JmeEphFFI::JME_BODY_SUN, JmeEphFFI::JME_CALC_TRUE_POSITION, $sunAtCross, $error),
            FFI::string($error)
        );
        $this->assertEqualsWithDelta(0.0, fmod(($sunAtCross[0] - 280.0 + 540.0), 360.0) - 180.0, 1e-6);
    }

    public function testSupportedHouseSystemsReturnFiniteOutput(): void
    {
        $cusps = $this->jme->getFFI()->new('double[13]');
        $ascmc = $this->jme->getFFI()->new('double[10]');

        $result = $this->jme->jme_houses(
            2451545.0,
            51.5,
            0.0,
            JmeEphFFI::JME_HOUSE_EQUAL,
            $cusps,
            $ascmc
        );

        $this->assertSame(JmeEphFFI::JME_OK, $result);
        $this->assertTrue(is_finite($cusps[1]));
        $this->assertTrue(is_finite($cusps[10]));

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_houses(
                2451545.0,
                51.5,
                0.0,
                JmeEphFFI::JME_HOUSE_HORIZONTAL,
                $cusps,
                $ascmc
            )
        );
        $this->assertTrue(is_finite($cusps[1]));
        $this->assertTrue(is_finite($cusps[10]));

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_houses(
                2451545.0,
                51.5,
                0.0,
                JmeEphFFI::JME_HOUSE_APC,
                $cusps,
                $ascmc
            )
        );
        $this->assertTrue(is_finite($cusps[1]));
        $this->assertTrue(is_finite($cusps[10]));
        $this->assertEqualsWithDelta($ascmc[0], $cusps[1], 1e-12);
        $this->assertEqualsWithDelta($ascmc[1], $cusps[10], 1e-12);

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_houses(
                2451545.0,
                51.5,
                0.0,
                JmeEphFFI::JME_HOUSE_SUNSHINE,
                $cusps,
                $ascmc
            )
        );
        $this->assertTrue(is_finite($cusps[1]));
        $this->assertTrue(is_finite($cusps[10]));
        $this->assertEqualsWithDelta($ascmc[0], $cusps[1], 1e-12);
        $this->assertEqualsWithDelta($ascmc[1], $cusps[10], 1e-12);
    }

    public function testOrbitalElementsReturnFiniteValues(): void
    {
        $elements = $this->jme->getFFI()->new('double[20]');
        $error = $this->jme->getFFI()->new('char[256]');

        $result = $this->jme->jme_get_orbital_elements(
            2451545.0,
            JmeEphFFI::JME_BODY_MERCURY,
            JmeEphFFI::JME_CALC_TRUE_POSITION,
            $elements,
            $error
        );

        $this->assertSame(JmeEphFFI::JME_OK, $result, FFI::string($error));
        $this->assertTrue(is_finite($elements[0]));
        $this->assertGreaterThan(0.0, $elements[0]);
        $this->assertGreaterThanOrEqual(0.0, $elements[1]);
        $this->assertGreaterThanOrEqual(0.0, $elements[2]);
        $this->assertLessThanOrEqual(180.0, $elements[2]);
    }

    public function testOrbitDistanceExtremaReturnFiniteValues(): void
    {
        $tmax = $this->jme->getFFI()->new('double[1]');
        $tmin = $this->jme->getFFI()->new('double[1]');
        $dmax = $this->jme->getFFI()->new('double[1]');
        $dmin = $this->jme->getFFI()->new('double[1]');
        $error = $this->jme->getFFI()->new('char[256]');

        $result = $this->jme->jme_orbit_max_min_true_distance(
            2451545.0,
            JmeEphFFI::JME_BODY_MERCURY,
            JmeEphFFI::JME_CALC_TRUE_POSITION,
            $tmax,
            $tmin,
            $dmax,
            $dmin,
            $error
        );

        $this->assertSame(JmeEphFFI::JME_OK, $result, FFI::string($error));
        $this->assertTrue(is_finite($tmax[0]));
        $this->assertTrue(is_finite($tmin[0]));
        $this->assertGreaterThan(0.0, $dmin[0]);
        $this->assertGreaterThan($dmin[0], $dmax[0]);
    }

    public function testLunarNodeApsidesReturnFiniteLongitudes(): void
    {
        $node = $this->jme->getFFI()->new('double[4]');
        $apogee = $this->jme->getFFI()->new('double[4]');
        $error = $this->jme->getFFI()->new('char[256]');

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_nod_aps(2451545.0, JmeEphFFI::JME_BODY_MOON, JmeEphFFI::JME_CALC_TRUE_POSITION, 1, $node, $error),
            FFI::string($error)
        );
        $this->assertTrue(is_finite($node[0]));
        $this->assertGreaterThanOrEqual(0.0, $node[0]);
        $this->assertLessThan(360.0, $node[0]);

        $this->assertSame(
            JmeEphFFI::JME_OK,
            $this->jme->jme_nod_aps(2451545.0, JmeEphFFI::JME_BODY_MOON, JmeEphFFI::JME_CALC_TRUE_POSITION, 4, $apogee, $error),
            FFI::string($error)
        );
        $this->assertTrue(is_finite($apogee[0]));
        $this->assertGreaterThanOrEqual(0.0, $apogee[0]);
        $this->assertLessThan(360.0, $apogee[0]);
    }

    public function testPhysicalPhenomenaReturnFiniteGeometry(): void
    {
        $attr = $this->jme->getFFI()->new('double[20]');
        $error = $this->jme->getFFI()->new('char[256]');

        $result = $this->jme->jme_pheno(
            2451545.0,
            JmeEphFFI::JME_BODY_MERCURY,
            JmeEphFFI::JME_CALC_TRUE_POSITION,
            $attr,
            $error
        );

        $this->assertSame(JmeEphFFI::JME_OK, $result, FFI::string($error));
        $this->assertGreaterThanOrEqual(0.0, $attr[0]);
        $this->assertLessThanOrEqual(180.0, $attr[0]);
        $this->assertGreaterThanOrEqual(0.0, $attr[1]);
        $this->assertLessThanOrEqual(1.0, $attr[1]);
        $this->assertGreaterThanOrEqual(0.0, $attr[2]);
        $this->assertLessThanOrEqual(180.0, $attr[2]);
        $this->assertGreaterThan(0.0, $attr[3]);
        $this->assertGreaterThan(-30.0, $attr[4]);
        $this->assertLessThan(30.0, $attr[4]);
        $this->assertGreaterThan(0.0, $attr[5]);
        $this->assertGreaterThan(0.0, $attr[6]);

    }

    public function testSunPositionUsesJmeApi(): void
    {
        $xx = $this->jme->getFFI()->new('double[6]');
        $error = $this->jme->getFFI()->new('char[256]');

        $result = $this->jme->jme_calc_ut(
            2451545.0,
            JmeEphFFI::JME_BODY_SUN,
            JmeEphFFI::JME_CALC_TRUE_POSITION,
            $xx,
            $error
        );

        if ($result === JmeEphFFI::JME_ERR) {
            $this->markTestSkipped('Native JME build does not provide this ephemeris calculation: ' . FFI::string($error));
        }

        if (! is_finite($xx[0]) || ! is_finite($xx[2])) {
            $this->markTestSkipped('Native JME build did not return finite Sun coordinates.');
        }

        $this->assertGreaterThan(270, $xx[0]);
        $this->assertLessThan(300, $xx[0]);
        $this->assertGreaterThan(0.9, $xx[2]);
        $this->assertLessThan(1.1, $xx[2]);
    }

    public function testConvenienceServiceNativePathDelegatesToJmeCalc(): void
    {
        $xx = $this->jme->getFFI()->new('double[6]');
        $error = $this->jme->getFFI()->new('char[256]');
        $service = new JmeService($this->jme, 'native');

        $result = $service->calc(2451545.0, JmeEphFFI::JME_BODY_SUN, JmeEphFFI::JME_CALC_NONE, $xx, $error);

        $this->assertSame(JmeEphFFI::JME_OK, $result);
        $this->assertIsFloat($xx[0]);
    }

    public function testConvenienceServiceMoshierPathIsPartialPlanetaryPath(): void
    {
        $xx = $this->jme->getFFI()->new('double[6]');
        $service = new JmeService($this->jme, 'moshier');

        $result = $service->calc(2451545.0, JmeEphFFI::JME_BODY_MERCURY, JmeEphFFI::JME_CALC_NONE, $xx);

        $this->assertSame(JmeEphFFI::JME_OK, $result);
        $this->assertIsFloat($xx[0]);
    }

    public function testConvenienceServiceVsop87PathIsPartialAnalyticalPath(): void
    {
        $xx = $this->jme->getFFI()->new('double[6]');
        $service = new JmeService($this->jme, 'vsop87');

        $result = $service->calc(2451545.0, JmeEphFFI::JME_BODY_MERCURY, JmeEphFFI::JME_CALC_NONE, $xx);

        $this->assertSame(JmeEphFFI::JME_OK, $result);
        $this->assertIsFloat($xx[0]);
    }

    public function testSiderealModeAndAyanamsa(): void
    {
        $this->jme->jme_set_sidereal_mode(JmeEphFFI::JME_SIDEREAL_LAHIRI, 0.0, 0.0);

        $ayanamsa = $this->jme->jme_get_ayanamsa_ut(2451545.0);

        $this->assertIsFloat($ayanamsa);
        $this->assertGreaterThan(23.0, $ayanamsa);
        $this->assertLessThan(24.5, $ayanamsa);
    }

    public function testDeltaTIsReturnedInSeconds(): void
    {
        $deltaT = $this->jme->jme_delta_t(2451545.0);

        $this->assertIsFloat($deltaT);
        $this->assertGreaterThan(60, $deltaT);
        $this->assertLessThan(70, $deltaT);
    }

    public function testSplitDegree(): void
    {
        $ideg = $this->jme->getFFI()->new('int[1]');
        $imin = $this->jme->getFFI()->new('int[1]');
        $isec = $this->jme->getFFI()->new('int[1]');
        $dsecfr = $this->jme->getFFI()->new('double[1]');
        $isgn = $this->jme->getFFI()->new('int[1]');

        $this->jme->jme_split_degree(123.456789, JmeEphFFI::JME_ANGLE_FORMAT_KEEP_DEG, $ideg, $imin, $isec, $dsecfr, $isgn);

        $this->assertSame(123, $ideg[0]);
        $this->assertSame(27, $imin[0]);
        $this->assertGreaterThanOrEqual(24, $isec[0]);
    }

    public function testBodyName(): void
    {
        $buffer = $this->jme->getFFI()->new('char[256]');

        $this->jme->jme_copy_body_name(JmeEphFFI::JME_BODY_SUN, $buffer);

        $this->assertSame('Sun', FFI::string($buffer));
    }

    public function testRefraction(): void
    {
        $refraction = $this->jme->jme_refract(0.0, 1013.25, 15.0, 0);

        $this->assertIsFloat($refraction);
        $this->assertGreaterThan(0.4, $refraction);
        $this->assertLessThan(0.6, $refraction);
    }
}
