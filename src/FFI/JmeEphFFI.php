<?php

declare(strict_types=1);

namespace JmeEph\FFI;

use FFI;
use FFI\CData;
use RuntimeException;

/**
 * @method string jme_version(\FFI\CData $buffer, int $buffer_size)
 * @method float jme_julian_day(int $year, int $month, int $day, float $hour, int $calendar)
 * @method void jme_set_sidereal_mode(int $sidereal_mode, float $t0, float $ayan_t0)
 * @method void jme_set_ephemeris_path(string $path)
 * @method void jme_set_jpl_file(string $path)
 * @method void jme_set_astro_models(string $models, int $flags)
 * @method int jme_jpl_open(?string $path, \FFI\CData $error)
 * @method ?\FFI\CData jme_ephemeris_path()
 * @method ?\FFI\CData jme_jpl_file()
 * @method int jme_calc_ut(float $jd_ut, int $body, int $flags, \FFI\CData $results, \FFI\CData $error)
 * @method int jme_houses(float $jd_ut, float $geo_lat, float $geo_lon, int $house_system, \FFI\CData $cusps, \FFI\CData $ascmc)
 * @method float jme_get_ayanamsa_ut(float $jd_ut)
 * @method int jme_lun_eclipse_when(float $jd_start, int $flags, int $iflag, \FFI\CData $tret, int $backward, \FFI\CData $error)
 * @method int jme_sol_eclipse_when_glob(float $jd_start, int $flags, int $epheflag, \FFI\CData $tret, int $backward, \FFI\CData $error)
 * @method int jme_lun_eclipse_how(float $jd_ut, int $flags, \FFI\CData $geopos, \FFI\CData $attr, \FFI\CData $error)
 * @method int jme_lun_eclipse_when_loc(float $jd_start, int $flags, \FFI\CData $geopos, \FFI\CData $tret, \FFI\CData $attr, int $backward, \FFI\CData $error)
 * @method int jme_sol_eclipse_how(float $jd_ut, int $flags, \FFI\CData $geopos, \FFI\CData $attr, \FFI\CData $error)
 * @method int jme_sol_eclipse_when_loc(float $jd_start, int $flags, \FFI\CData $geopos, \FFI\CData $tret, \FFI\CData $attr, int $backward, \FFI\CData $error)
 * @method void jme_jd_to_utc(float $jd, int $calendar, \FFI\CData $year, \FFI\CData $month, \FFI\CData $day, \FFI\CData $hour, \FFI\CData $minute, \FFI\CData $second)
 * @method int jme_rise_trans(float $jd_ut, int $body, ?string $starname, int $flags, int $rsmi, \FFI\CData $geopos, float $atpress, float $attemp, \FFI\CData $tret, \FFI\CData $error)
 */
class JmeEphFFI
{
    public const JME_JME_H = 1;
    public const JME_VERSION = '0.1.0';
    public const JME_AU_KM = 149597870.7;
    public const JME_SPEED_OF_LIGHT_KM_PER_SEC = 299792.458;
    public const JME_SECONDS_PER_DAY = 86400.0;
    public const JME_OK = 0;
    public const JME_ERR = -1;
    public const JME_CALENDAR_JULIAN = 0;
    public const JME_CALENDAR_GREGORIAN = 1;
    public const JME_BODY_SUN = 0;
    public const JME_BODY_MOON = 1;
    public const JME_BODY_MERCURY = 2;
    public const JME_BODY_VENUS = 3;
    public const JME_BODY_MARS = 4;
    public const JME_BODY_JUPITER = 5;
    public const JME_BODY_SATURN = 6;
    public const JME_BODY_URANUS = 7;
    public const JME_BODY_NEPTUNE = 8;
    public const JME_BODY_PLUTO = 9;
    public const JME_BODY_EARTH = 10;
    public const JME_BODY_SOLAR_SYSTEM_BARYCENTER = 11;
    public const JME_BODY_MERCURY_BARYCENTER = 12;
    public const JME_BODY_VENUS_BARYCENTER = 13;
    public const JME_BODY_EARTH_MOON_BARYCENTER = 14;
    public const JME_BODY_MARS_BARYCENTER = 15;
    public const JME_BODY_JUPITER_BARYCENTER = 16;
    public const JME_BODY_SATURN_BARYCENTER = 17;
    public const JME_BODY_URANUS_BARYCENTER = 18;
    public const JME_BODY_NEPTUNE_BARYCENTER = 19;
    public const JME_BODY_PLUTO_BARYCENTER = 20;
    public const JME_BODY_MEAN_NODE = 21;
    public const JME_BODY_TRUE_NODE = 22;
    public const JME_CALC_NONE = 0;
    public const JME_CALC_SPEED = 1;
    public const JME_CALC_EQUATORIAL = 2;
    public const JME_CALC_XYZ = 4;
    public const JME_CALC_RADIANS = 8;
    public const JME_CALC_BARYCENTRIC = 16;
    public const JME_CALC_HELIOCENTRIC = 32;
    public const JME_CALC_TRUE_POSITION = 64;
    public const JME_CALC_J2000 = 128;
    public const JME_CALC_NO_NUTATION = 256;
    public const JME_CALC_SIDEREAL = 512;
    public const JME_SIDEREAL_FAGAN_BRADLEY = 0;
    public const JME_SIDEREAL_LAHIRI = 1;
    public const JME_SIDEREAL_USER = 255;
    public const JME_VECTOR_AU_PER_DAY = 0;
    public const JME_VECTOR_KM_PER_DAY = 1;
    public const JME_VECTOR_AU_PER_SECOND = 2;
    public const JME_VECTOR_KM_PER_SECOND = 3;
    public const JME_ORIENTATION_RAD_PER_DAY = 0;
    public const JME_ORIENTATION_RAD_PER_SECOND = 1;
    public const JME_JPL_TIMESCALE_UNKNOWN = 0;
    public const JME_JPL_TIMESCALE_TDB = 1;
    public const JME_JPL_TIMESCALE_TCB = 2;
    public const JME_EXTENDED_H = 1;
    public const JME_ANGLE_FORMAT_KEEP_DEG = 0;
    public const JME_ANGLE_FORMAT_KEEP_SIGN = 1;
    public const JME_ANGLE_FORMAT_NAKSHATRA = 2;
    public const JME_ANGLE_FORMAT_ROUND_DEG = 3;
    public const JME_ANGLE_FORMAT_ROUND_MIN = 4;
    public const JME_ANGLE_FORMAT_ROUND_SEC = 5;
    public const JME_ANGLE_FORMAT_ZODIACAL = 6;
    public const JME_BODY_ALTJIRA = 7;
    public const JME_BODY_AMYCUS = 8;
    public const JME_BODY_ARROKOTH = 9;
    public const JME_BODY_ASBOLUS = 10;
    public const JME_BODY_ASTEROID_001 = 11;
    public const JME_BODY_ASTEROID_002 = 12;
    public const JME_BODY_ASTEROID_003 = 13;
    public const JME_BODY_ASTEROID_004 = 14;
    public const JME_BODY_ASTEROID_005 = 15;
    public const JME_BODY_ASTEROID_006 = 16;
    public const JME_BODY_ASTEROID_007 = 17;
    public const JME_BODY_ASTEROID_008 = 18;
    public const JME_BODY_ASTEROID_009 = 19;
    public const JME_BODY_ASTEROID_010 = 20;
    public const JME_BODY_ASTEROID_011 = 21;
    public const JME_BODY_ASTEROID_012 = 22;
    public const JME_BODY_ASTEROID_013 = 23;
    public const JME_BODY_ASTEROID_014 = 24;
    public const JME_BODY_ASTEROID_015 = 25;
    public const JME_BODY_ASTEROID_016 = 26;
    public const JME_BODY_ASTEROID_017 = 27;
    public const JME_BODY_ASTEROID_018 = 28;
    public const JME_BODY_ASTEROID_019 = 29;
    public const JME_BODY_ASTEROID_020 = 30;
    public const JME_BODY_ASTEROID_021 = 31;
    public const JME_BODY_ASTEROID_022 = 32;
    public const JME_BODY_ASTEROID_023 = 33;
    public const JME_BODY_ASTEROID_024 = 34;
    public const JME_BODY_ASTEROID_025 = 35;
    public const JME_BODY_ASTEROID_026 = 36;
    public const JME_BODY_ASTEROID_027 = 37;
    public const JME_BODY_ASTEROID_028 = 38;
    public const JME_BODY_ASTEROID_029 = 39;
    public const JME_BODY_ASTEROID_030 = 40;
    public const JME_BODY_ASTEROID_031 = 41;
    public const JME_BODY_ASTEROID_032 = 42;
    public const JME_BODY_ASTEROID_033 = 43;
    public const JME_BODY_ASTEROID_034 = 44;
    public const JME_BODY_ASTEROID_035 = 45;
    public const JME_BODY_ASTEROID_036 = 46;
    public const JME_BODY_ASTEROID_037 = 47;
    public const JME_BODY_ASTEROID_038 = 48;
    public const JME_BODY_ASTEROID_039 = 49;
    public const JME_BODY_ASTEROID_040 = 50;
    public const JME_BODY_ASTEROID_041 = 51;
    public const JME_BODY_ASTEROID_042 = 52;
    public const JME_BODY_ASTEROID_043 = 53;
    public const JME_BODY_ASTEROID_044 = 54;
    public const JME_BODY_ASTEROID_045 = 55;
    public const JME_BODY_ASTEROID_046 = 56;
    public const JME_BODY_ASTEROID_047 = 57;
    public const JME_BODY_ASTEROID_048 = 58;
    public const JME_BODY_ASTEROID_049 = 59;
    public const JME_BODY_ASTEROID_050 = 60;
    public const JME_BODY_ASTEROID_051 = 61;
    public const JME_BODY_ASTEROID_052 = 62;
    public const JME_BODY_ASTEROID_053 = 63;
    public const JME_BODY_ASTEROID_054 = 64;
    public const JME_BODY_ASTEROID_055 = 65;
    public const JME_BODY_ASTEROID_056 = 66;
    public const JME_BODY_ASTEROID_057 = 67;
    public const JME_BODY_ASTEROID_058 = 68;
    public const JME_BODY_ASTEROID_059 = 69;
    public const JME_BODY_ASTEROID_060 = 70;
    public const JME_BODY_ASTEROID_061 = 71;
    public const JME_BODY_ASTEROID_062 = 72;
    public const JME_BODY_ASTEROID_063 = 73;
    public const JME_BODY_ASTEROID_064 = 74;
    public const JME_BODY_ASTEROID_065 = 75;
    public const JME_BODY_ASTEROID_066 = 76;
    public const JME_BODY_ASTEROID_067 = 77;
    public const JME_BODY_ASTEROID_068 = 78;
    public const JME_BODY_ASTEROID_069 = 79;
    public const JME_BODY_ASTEROID_070 = 80;
    public const JME_BODY_ASTEROID_071 = 81;
    public const JME_BODY_ASTEROID_072 = 82;
    public const JME_BODY_ASTEROID_073 = 83;
    public const JME_BODY_ASTEROID_074 = 84;
    public const JME_BODY_ASTEROID_075 = 85;
    public const JME_BODY_ASTEROID_076 = 86;
    public const JME_BODY_ASTEROID_077 = 87;
    public const JME_BODY_ASTEROID_078 = 88;
    public const JME_BODY_ASTEROID_079 = 89;
    public const JME_BODY_ASTEROID_080 = 90;
    public const JME_BODY_ASTEROID_081 = 91;
    public const JME_BODY_ASTEROID_082 = 92;
    public const JME_BODY_ASTEROID_083 = 93;
    public const JME_BODY_ASTEROID_084 = 94;
    public const JME_BODY_ASTEROID_085 = 95;
    public const JME_BODY_ASTEROID_086 = 96;
    public const JME_BODY_ASTEROID_087 = 97;
    public const JME_BODY_ASTEROID_088 = 98;
    public const JME_BODY_ASTEROID_089 = 99;
    public const JME_BODY_ASTEROID_090 = 100;
    public const JME_BODY_ASTEROID_091 = 101;
    public const JME_BODY_ASTEROID_092 = 102;
    public const JME_BODY_ASTEROID_093 = 103;
    public const JME_BODY_ASTEROID_094 = 104;
    public const JME_BODY_ASTEROID_095 = 105;
    public const JME_BODY_ASTEROID_096 = 106;
    public const JME_BODY_ASTEROID_097 = 107;
    public const JME_BODY_ASTEROID_098 = 108;
    public const JME_BODY_ASTEROID_099 = 109;
    public const JME_BODY_ASTEROID_100 = 110;
    public const JME_BODY_ASTEROID_101 = 111;
    public const JME_BODY_ASTEROID_102 = 112;
    public const JME_BODY_ASTEROID_103 = 113;
    public const JME_BODY_ASTEROID_104 = 114;
    public const JME_BODY_ASTEROID_105 = 115;
    public const JME_BODY_ASTEROID_106 = 116;
    public const JME_BODY_ASTEROID_107 = 117;
    public const JME_BODY_ASTEROID_108 = 118;
    public const JME_BODY_ASTEROID_109 = 119;
    public const JME_BODY_ASTEROID_110 = 120;
    public const JME_BODY_BIENOR = 121;
    public const JME_BODY_BORASISI = 122;
    public const JME_BODY_CERES = 123;
    public const JME_BODY_CHARIKLO = 124;
    public const JME_BODY_CHIRON = 125;
    public const JME_BODY_COMET_001 = 126;
    public const JME_BODY_COMET_002 = 127;
    public const JME_BODY_COMET_003 = 128;
    public const JME_BODY_COMET_004 = 129;
    public const JME_BODY_COMET_005 = 130;
    public const JME_BODY_COMET_006 = 131;
    public const JME_BODY_COMET_007 = 132;
    public const JME_BODY_COMET_008 = 133;
    public const JME_BODY_COMET_009 = 134;
    public const JME_BODY_COMET_010 = 135;
    public const JME_BODY_COMET_011 = 136;
    public const JME_BODY_COMET_012 = 137;
    public const JME_BODY_COMET_013 = 138;
    public const JME_BODY_COMET_014 = 139;
    public const JME_BODY_COMET_015 = 140;
    public const JME_BODY_COMET_016 = 141;
    public const JME_BODY_COMET_017 = 142;
    public const JME_BODY_COMET_018 = 143;
    public const JME_BODY_COMET_019 = 144;
    public const JME_BODY_COMET_020 = 145;
    public const JME_BODY_COMET_021 = 146;
    public const JME_BODY_COMET_022 = 147;
    public const JME_BODY_COMET_023 = 148;
    public const JME_BODY_COMET_024 = 149;
    public const JME_BODY_COMET_025 = 150;
    public const JME_BODY_COMET_026 = 151;
    public const JME_BODY_COMET_027 = 152;
    public const JME_BODY_COMET_028 = 153;
    public const JME_BODY_COMET_029 = 154;
    public const JME_BODY_COMET_030 = 155;
    public const JME_BODY_COMET_031 = 156;
    public const JME_BODY_COMET_032 = 157;
    public const JME_BODY_COMET_033 = 158;
    public const JME_BODY_COMET_034 = 159;
    public const JME_BODY_COMET_035 = 160;
    public const JME_BODY_COMET_036 = 161;
    public const JME_BODY_COMET_037 = 162;
    public const JME_BODY_COMET_038 = 163;
    public const JME_BODY_COMET_039 = 164;
    public const JME_BODY_COMET_040 = 165;
    public const JME_BODY_COMET_041 = 166;
    public const JME_BODY_COMET_042 = 167;
    public const JME_BODY_COMET_043 = 168;
    public const JME_BODY_COMET_044 = 169;
    public const JME_BODY_COMET_045 = 170;
    public const JME_BODY_COMET_046 = 171;
    public const JME_BODY_COMET_047 = 172;
    public const JME_BODY_COMET_048 = 173;
    public const JME_BODY_COMET_049 = 174;
    public const JME_BODY_COMET_050 = 175;
    public const JME_BODY_COMET_051 = 176;
    public const JME_BODY_COMET_052 = 177;
    public const JME_BODY_COMET_053 = 178;
    public const JME_BODY_COMET_054 = 179;
    public const JME_BODY_COMET_055 = 180;
    public const JME_BODY_COMET_056 = 181;
    public const JME_BODY_COMET_057 = 182;
    public const JME_BODY_COMET_058 = 183;
    public const JME_BODY_COMET_059 = 184;
    public const JME_BODY_COMET_060 = 185;
    public const JME_BODY_CRANTOR = 186;
    public const JME_BODY_CYLLARUS = 187;
    public const JME_BODY_DEUCALION = 188;
    public const JME_BODY_ECHECLUS = 189;
    public const JME_BODY_ELATUS = 190;
    public const JME_BODY_ERIS = 191;
    public const JME_BODY_GONGGONG = 192;
    public const JME_BODY_HAUMEA = 193;
    public const JME_BODY_HUYA = 194;
    public const JME_BODY_HYLONOME = 195;
    public const JME_BODY_IXION = 196;
    public const JME_BODY_JUNO = 197;
    public const JME_BODY_MAKEMAKE = 198;
    public const JME_BODY_MINOR_PLANET_001 = 199;
    public const JME_BODY_MINOR_PLANET_002 = 200;
    public const JME_BODY_MINOR_PLANET_003 = 201;
    public const JME_BODY_MINOR_PLANET_004 = 202;
    public const JME_BODY_MINOR_PLANET_005 = 203;
    public const JME_BODY_MINOR_PLANET_006 = 204;
    public const JME_BODY_MINOR_PLANET_007 = 205;
    public const JME_BODY_MINOR_PLANET_008 = 206;
    public const JME_BODY_MINOR_PLANET_009 = 207;
    public const JME_BODY_MINOR_PLANET_010 = 208;
    public const JME_BODY_MINOR_PLANET_011 = 209;
    public const JME_BODY_MINOR_PLANET_012 = 210;
    public const JME_BODY_MINOR_PLANET_013 = 211;
    public const JME_BODY_MINOR_PLANET_014 = 212;
    public const JME_BODY_MINOR_PLANET_015 = 213;
    public const JME_BODY_MINOR_PLANET_016 = 214;
    public const JME_BODY_MINOR_PLANET_017 = 215;
    public const JME_BODY_MINOR_PLANET_018 = 216;
    public const JME_BODY_MINOR_PLANET_019 = 217;
    public const JME_BODY_MINOR_PLANET_020 = 218;
    public const JME_BODY_MINOR_PLANET_021 = 219;
    public const JME_BODY_MINOR_PLANET_022 = 220;
    public const JME_BODY_MINOR_PLANET_023 = 221;
    public const JME_BODY_MINOR_PLANET_024 = 222;
    public const JME_BODY_MINOR_PLANET_025 = 223;
    public const JME_BODY_MINOR_PLANET_026 = 224;
    public const JME_BODY_MINOR_PLANET_027 = 225;
    public const JME_BODY_MINOR_PLANET_028 = 226;
    public const JME_BODY_MINOR_PLANET_029 = 227;
    public const JME_BODY_MINOR_PLANET_030 = 228;
    public const JME_BODY_MINOR_PLANET_031 = 229;
    public const JME_BODY_MINOR_PLANET_032 = 230;
    public const JME_BODY_MINOR_PLANET_033 = 231;
    public const JME_BODY_MINOR_PLANET_034 = 232;
    public const JME_BODY_MINOR_PLANET_035 = 233;
    public const JME_BODY_MINOR_PLANET_036 = 234;
    public const JME_BODY_MINOR_PLANET_037 = 235;
    public const JME_BODY_MINOR_PLANET_038 = 236;
    public const JME_BODY_MINOR_PLANET_039 = 237;
    public const JME_BODY_MINOR_PLANET_040 = 238;
    public const JME_BODY_MINOR_PLANET_041 = 239;
    public const JME_BODY_MINOR_PLANET_042 = 240;
    public const JME_BODY_MINOR_PLANET_043 = 241;
    public const JME_BODY_MINOR_PLANET_044 = 242;
    public const JME_BODY_MINOR_PLANET_045 = 243;
    public const JME_BODY_MINOR_PLANET_046 = 244;
    public const JME_BODY_MINOR_PLANET_047 = 245;
    public const JME_BODY_MINOR_PLANET_048 = 246;
    public const JME_BODY_MINOR_PLANET_049 = 247;
    public const JME_BODY_MINOR_PLANET_050 = 248;
    public const JME_BODY_MINOR_PLANET_051 = 249;
    public const JME_BODY_MINOR_PLANET_052 = 250;
    public const JME_BODY_MINOR_PLANET_053 = 251;
    public const JME_BODY_MINOR_PLANET_054 = 252;
    public const JME_BODY_MINOR_PLANET_055 = 253;
    public const JME_BODY_MINOR_PLANET_056 = 254;
    public const JME_BODY_MINOR_PLANET_057 = 255;
    public const JME_BODY_MINOR_PLANET_058 = 256;
    public const JME_BODY_MINOR_PLANET_059 = 257;
    public const JME_BODY_MINOR_PLANET_060 = 258;
    public const JME_BODY_NESSUS = 259;
    public const JME_BODY_OKYRHOE = 260;
    public const JME_BODY_ORCUS = 261;
    public const JME_BODY_PALLAS = 262;
    public const JME_BODY_PELOPS = 263;
    public const JME_BODY_PHOLUS = 264;
    public const JME_BODY_QUAOAR = 265;
    public const JME_BODY_RHADAMANTHUS = 266;
    public const JME_BODY_SALACIA = 267;
    public const JME_BODY_SEDNA = 268;
    public const JME_BODY_THEREUS = 269;
    public const JME_BODY_TYTHONUS = 270;
    public const JME_BODY_VARUNA = 271;
    public const JME_BODY_VESTA = 272;
    public const JME_CALC_APPARENT_POSITION = 0;
    public const JME_CALC_ASTROMETRIC = 3072;
    public const JME_CALC_CENTER_BODY = 4096;
    public const JME_CALC_DISTANCE_AU = 0;
    public const JME_CALC_DISTANCE_KM = 8192;
    public const JME_CALC_HIGH_PRECISION = 16384;
    public const JME_CALC_ICRS = 32768;
    public const JME_CALC_NO_ABERRATION = 1024;
    public const JME_CALC_NO_LIGHT_DEFLECTION = 2048;
    public const JME_CALC_RAW_VECTOR = 4;
    public const JME_CALC_RECTANGULAR = 4;
    public const JME_CALC_SPHERICAL = 0;
    public const JME_CALC_STRICT = 65536;
    public const JME_CALC_TOPOCENTRIC = 131072;
    public const JME_CALC_VELOCITY_PER_DAY = 0;
    public const JME_CALC_VELOCITY_PER_SECOND = 262144;
    public const JME_COORD_APPARENT_TO_TRUE = 289;
    public const JME_COORD_ECLIPTIC_TO_HORIZONTAL = 290;
    public const JME_COORD_EQUATORIAL_TO_HORIZONTAL = 291;
    public const JME_COORD_HORIZONTAL_TO_ECLIPTIC = 292;
    public const JME_COORD_HORIZONTAL_TO_EQUATORIAL = 293;
    public const JME_COORD_TRUE_TO_APPARENT = 294;
    public const JME_ECLIPSE_FIRST_CONTACT = 295;
    public const JME_ECLIPSE_FOURTH_CONTACT = 296;
    public const JME_ECLIPSE_LUNAR_PARTIAL = 297;
    public const JME_ECLIPSE_LUNAR_PENUMBRAL = 298;
    public const JME_ECLIPSE_LUNAR_TOTAL = 299;
    public const JME_ECLIPSE_MAX_VISIBLE = 300;
    public const JME_ECLIPSE_PENUMBRAL_BEGIN = 301;
    public const JME_ECLIPSE_PENUMBRAL_END = 302;
    public const JME_ECLIPSE_SECOND_CONTACT = 303;
    public const JME_ECLIPSE_SOLAR_ANNULAR = 304;
    public const JME_ECLIPSE_SOLAR_CENTRAL = 305;
    public const JME_ECLIPSE_SOLAR_HYBRID = 306;
    public const JME_ECLIPSE_SOLAR_NONCENTRAL = 307;
    public const JME_ECLIPSE_SOLAR_PARTIAL = 308;
    public const JME_ECLIPSE_SOLAR_TOTAL = 309;
    public const JME_ECLIPSE_THIRD_CONTACT = 310;
    public const JME_ECLIPSE_VISIBLE = 311;
    public const JME_HOUSE_ALCABITIUS = 312;
    public const JME_HOUSE_APC = 313;
    public const JME_HOUSE_AZIMUTHAL = 314;
    public const JME_HOUSE_CAMPANUS = 315;
    public const JME_HOUSE_EQUAL = 316;
    public const JME_HOUSE_GAUQUELIN = 317;
    public const JME_HOUSE_HORIZONTAL = 318;
    public const JME_HOUSE_KOCH = 319;
    public const JME_HOUSE_KRUSINSKI = 320;
    public const JME_HOUSE_MERIDIAN = 321;
    public const JME_HOUSE_MORINUS = 322;
    public const JME_HOUSE_PLACIDUS = 323;
    public const JME_HOUSE_POLICH_PAGE = 324;
    public const JME_HOUSE_PORPHYRIUS = 325;
    public const JME_HOUSE_REGIOMONTANUS = 326;
    public const JME_HOUSE_SUNSHINE = 327;
    public const JME_HOUSE_VEHLOW_EQUAL = 328;
    public const JME_HOUSE_WHOLE_SIGN = 329;
    public const JME_MODEL_BIAS_IAU2000 = 330;
    public const JME_MODEL_BIAS_IAU2006 = 331;
    public const JME_MODEL_BIAS_NONE = 332;
    public const JME_MODEL_NUT_IAU_1980 = 333;
    public const JME_MODEL_NUT_IAU_2000A = 334;
    public const JME_MODEL_NUT_IAU_2000B = 335;
    public const JME_MODEL_OBL_IAU_1980 = 336;
    public const JME_MODEL_OBL_IAU_2000 = 337;
    public const JME_MODEL_OBL_IAU_2006 = 338;
    public const JME_MODEL_PREC_IAU_1976 = 339;
    public const JME_MODEL_PREC_IAU_2000 = 340;
    public const JME_MODEL_PREC_IAU_2006 = 341;
    public const JME_MODEL_PREC_LASKAR_1986 = 342;
    public const JME_MODEL_PREC_VONDRAK_2011 = 343;
    public const JME_MODEL_SIDT_IAU_1976 = 344;
    public const JME_MODEL_SIDT_IAU_2006 = 345;
    public const JME_MODEL_DELTAT_STEPHENSON_MORRISON_1984 = 346;
    public const JME_MODEL_DELTAT_STEPHENSON_1997 = 347;
    public const JME_MODEL_DELTAT_STEPHENSON_MORRISON_2004 = 348;
    public const JME_MODEL_DELTAT_ESPENAK_MEEUS_2006 = 349;
    public const JME_MODEL_DELTAT_STEPHENSON_ETC_2016 = 350;
    public const JME_RISE_ANTI_MERIDIAN_TRANSIT = 8;
    public const JME_RISE_ASTRONOMICAL_TWILIGHT = 512;
    public const JME_RISE_CIVIL_TWILIGHT = 128;
    public const JME_RISE_DISC_BOTTOM = 2048;
    public const JME_RISE_DISC_CENTER = 256;
    public const JME_RISE_FIXED_DISC_SIZE = 1024;
    public const JME_RISE_HINDU_RISING = 4096;
    public const JME_RISE_MERIDIAN_TRANSIT = 4;
    public const JME_RISE_NAUTICAL_TWILIGHT = 64;
    public const JME_RISE_NO_REFRACTION = 8192;
    public const JME_RISE_RISE = 1;
    public const JME_RISE_SET = 2;
    public const JME_SIDEREAL_ALDEBARAN_15TAU = 363;
    public const JME_SIDEREAL_ARYABHATA = 364;
    public const JME_SIDEREAL_B1950 = 365;
    public const JME_SIDEREAL_BABYL_ETPSC = 366;
    public const JME_SIDEREAL_BABYL_HUBER = 367;
    public const JME_SIDEREAL_BABYL_KUGLER1 = 368;
    public const JME_SIDEREAL_BABYL_KUGLER2 = 369;
    public const JME_SIDEREAL_BABYL_KUGLER3 = 370;
    public const JME_SIDEREAL_DELUCE = 371;
    public const JME_SIDEREAL_GALCENT_0SAG = 372;
    public const JME_SIDEREAL_HIPPARCHOS = 373;
    public const JME_SIDEREAL_J1900 = 374;
    public const JME_SIDEREAL_J2000 = 375;
    public const JME_SIDEREAL_JN_BHASIN = 376;
    public const JME_SIDEREAL_KRISHNAMURTI = 377;
    public const JME_SIDEREAL_RAMAN = 378;
    public const JME_SIDEREAL_SASSANIAN = 379;
    public const JME_SIDEREAL_SS_CITRA = 380;
    public const JME_SIDEREAL_SS_REVATI = 381;
    public const JME_SIDEREAL_SURYASIDDHANTA = 382;
    public const JME_SIDEREAL_TRUE_CITRA = 383;
    public const JME_SIDEREAL_TRUE_MULA = 384;
    public const JME_SIDEREAL_TRUE_PUSHYA = 385;
    public const JME_SIDEREAL_TRUE_REVATI = 386;
    public const JME_SIDEREAL_USHASHASHI = 387;
    public const JME_SIDEREAL_YUKTESHWAR = 388;
    public const JME_TIME_DELTAT_AUTOMATIC = 389;
    public const JME_TIME_TIDAL_AUTOMATIC = 390;
    public const JME_TIME_TIDAL_DE200 = 391;
    public const JME_TIME_TIDAL_DE403 = 392;
    public const JME_TIME_TIDAL_DE404 = 393;
    public const JME_TIME_TIDAL_DE405 = 394;
    public const JME_TIME_TIDAL_DE406 = 395;
    public const JME_TIME_TIDAL_DE421 = 396;
    public const JME_TIME_TIDAL_DE430 = 397;
    public const JME_TIME_TIDAL_DE431 = 398;
    public const JME_TIME_TIDAL_DE441 = 399;
    public const JME_VERSION_ID = 400;
    public const JME_MODEL_REVISED_IAU_2000 = 401;
    public const JME_MODEL_REVISED_IAU_2006 = 402;
    public const JME_MODEL_REVISED_PREC_LASKAR = 403;
    public const JME_MODEL_REVISED_PREC_VONDRAK = 404;
    public const JME_MODEL_REVISED_PREC_LIESKE = 405;
    /** @var array<string, array{calls: int, ns: int}> */
    private static array $profile = [];

    /** @var array<string, array<string, true>> */
    private static array $profileUniqueInputs = [];

    private static ?bool $profileEnabled = null;

    private static bool $profileWritten = false;

    private FFI $ffi;

    public function __construct(?string $libraryPath = null)
    {
        $cdef = <<<'CDEF'
            int jme_body_id_from_name(const char *name);
            int jme_body_naif_id(int body);
            int jme_calc(double jd_et, int body, int flags, double *results, char *error);
            int jme_calc_pctr(double jd_et, int body, int center, int flags, double *results, char *error);
            int jme_calc_ut(double jd_ut, int body, int flags, double *results, char *error);
            int jme_calendar_is_leap_year(int year, int calendar);
            int jme_centiseconds_difference(int p1, int p2);
            int jme_centiseconds_difference_signed(int p1, int p2);
            int jme_centiseconds_normalize(int p);
            int jme_centiseconds_round_second(int x);
            void jme_close(void);
            int jme_date_is_valid(int year, int month, int day, int calendar);
            int jme_day_of_week(double jd);
            int jme_day_of_year(int year, int month, int day, int calendar);
            int jme_days_in_month(int year, int month, int calendar);
            double jme_decimal_hour(int hour, int minute, double second);
            double jme_degree_midpoint(double x1, double x0);
            double jme_degree_normalize(double x);
            double jme_degrees_difference(double p1, double p2);
            double jme_degrees_difference_signed(double p1, double p2);
            double jme_degrees_to_hours(double degrees);
            double jme_degrees_to_radians(double degrees);
            double jme_delta_t(double jd_ut);
            double jme_delta_t_ex(double jd_ut, int model, char *error);
            int jme_double_to_long(double x);
            void jme_ecliptic_to_equatorial( double lon, double lat, double eps, double *ra, double *dec );
            int jme_ecliptic_to_equatorial_rectangular_state( const double *ecliptic, double eps, double *equatorial );
            int jme_elp2000_moon_state(double jd_et, double *results);
            void jme_equatorial_to_ecliptic( double ra, double dec, double eps, double *lon, double *lat );
            int jme_equatorial_to_ecliptic_rectangular_state( const double *equatorial, double eps, double *ecliptic );
            void jme_equatorial_to_horizontal( double hour_angle, double dec, double geo_lat, double *azimuth, double *altitude );
            int jme_fixstar(const char *star, double jd_et, int flags, double *results, char *error);
            int jme_fixstar_mag(const char *star, double *mag, char *error);
            int jme_fixstar_ut(const char *star, double jd_ut, int flags, double *results, char *error);
            int jme_fixstar2(const char *star, double jd_et, int flags, double *results, char *error);
            int jme_fixstar2_mag(const char *star, double *mag, char *error);
            int jme_fixstar2_ut(const char *star, double jd_ut, int flags, double *results, char *error);
            int jme_gauquelin_sector(double jd_ut, int body, const char *starname, int flags, int imeth, double *geopos, double atpress, double attemp, double *dgsect, char *error);
            int jme_get_astro_models(char *models, int flags);
            double jme_get_ayanamsa(double jd_et);
            int jme_get_ayanamsa_ex(double jd_et, int model, double *ayan, char *error);
            int jme_get_ayanamsa_ex_ut(double jd_ut, int model, double *ayan, char *error);
            double jme_get_ayanamsa_ut(double jd_ut);
            int jme_get_nutation(double jd_et, int model, double *dpsi, double *deps, char *error);
            void jme_get_nutation_matrix(double dpsi_rad, double deps_rad, double eps_rad, double *m);
            int jme_get_obliquity(double jd_et, int model, double *eps, char *error);
            int jme_get_orbital_elements(double jd_et, int body, int flags, double *elem, char *error);
            int jme_get_precession_matrix(double jd_start, double jd_end, int model, double *m);
            void jme_get_sidereal_mode(int *sidereal_mode, double *t0, double *ayan_t0);
            double jme_get_tid_acc(void);
            int jme_get_topo_pos(double jd_et, double *pos_au, char *error);
            double jme_heliacal_angle(double jd_ut, double *geopos, double *dat_hel, char *error);
            int jme_heliacal_pheno_ut(double jd_ut, double *geopos, double *dat_hel, char *error);
            int jme_heliacal_ut(double jd_ut, double *geopos, double *dat_hel, char *error);
            int jme_helio_cross(int body, double x2cross, double jd_ut, int flags, double *tret, char *error);
            int jme_helio_cross_ut(int body, double x2cross, double jd_ut, int flags, double *tret, char *error);
            void jme_horizontal_to_equatorial( double azimuth, double altitude, double geo_lat, double *hour_angle, double *dec );
            double jme_hours_normalize(double hours);
            double jme_hours_to_degrees(double hours);
            double jme_house_pos(double armc, double geo_lat, double eps, int house_system, double *xpin, char *error);
            int jme_houses(double jd_ut, double geo_lat, double geo_lon, int house_system, double *cusps, double *ascmc);
            int jme_houses_armc(double armc, double geo_lat, double eps, int house_system, double *cusps, double *ascmc);
            int jme_houses_armc_ex2(double armc, double geo_lat, double eps, int house_system, double *cusps, double *ascmc, double *cusps_speed, double *ascmc_speed);
            int jme_houses_ex(double jd_ut, int flags, double geo_lat, double geo_lon, int house_system, double *cusps, double *ascmc);
            int jme_houses_ex2(double jd_ut, int flags, double geo_lat, double geo_lon, int house_system, double *cusps, double *ascmc, double *cusps_speed, double *ascmc_speed);
            double jme_jd_add_seconds(double jd, double seconds);
            double jme_jd_difference_seconds(double jd_end, double jd_start);
            void jme_jd_to_utc( double jd, int calendar, int *year, int *month, int *day, int *hour, int *minute, double *second );
            int jme_jpl_body_state( double jd_time, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_body_state_naif( double jd_time, int target_naif, int center_naif, int unit, double *state, char *error );
            int jme_jpl_body_state_native( double jd_time, int target_body, int center_body, double *state, char *error );
            int jme_jpl_body_state_native_naif( double jd_time, int target_naif, int center_naif, double *state, char *error );
            int jme_jpl_body_state_native_split( double jd0, double time_offset, int target_body, int center_body, double *state, char *error );
            int jme_jpl_body_state_native_split_naif( double jd0, double time_offset, int target_naif, int center_naif, double *state, char *error );
            int jme_jpl_body_state_order( double jd0, double time_offset, int target_body, int center_body, int unit, int order, double *state, char *error );
            int jme_jpl_body_state_order_naif( double jd0, double time_offset, int target_naif, int center_naif, int unit, int order, double *state, char *error );
            int jme_jpl_body_state_split( double jd0, double time_offset, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_body_state_split_naif( double jd0, double time_offset, int target_naif, int center_naif, int unit, double *state, char *error );
            int jme_jpl_body_state_utc( int year, int month, int day, int hour, int minute, double second, int calendar, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_body_state_utc_naif( int year, int month, int day, int hour, int minute, double second, int calendar, int target_naif, int center_naif, int unit, double *state, char *error );
            void jme_jpl_close(void);
            int jme_jpl_constant(const char *name, double *value, char *error);
            int jme_jpl_constant_count(char *error);
            int jme_jpl_constant_index(int index, char *name, unsigned int name_size, double *value, char *error);
            int jme_jpl_constant_string(const char *name, char *value, unsigned int value_size, char *error);
            int jme_jpl_constant_string_vector( const char *name, char *values, unsigned int single_value_size, int value_count, char *error );
            int jme_jpl_constant_vector(const char *name, double *values, int value_count, char *error);
            int jme_jpl_coverage(double *first_time, double *last_time, int *continuous, char *error);
            int jme_jpl_current_file_data( char *path, unsigned int path_size, double *first_time, double *last_time, int *continuous, char *error );
            int jme_jpl_ecliptic_state( double jd_time, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_ecliptic_state_naif( double jd_time, int target_naif, int center_naif, int unit, double *state, char *error );
            int jme_jpl_ecliptic_state_split( double jd0, double time_offset, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_ecliptic_state_split_naif( double jd0, double time_offset, int target_naif, int center_naif, int unit, double *state, char *error );
            int jme_jpl_ecliptic_state_utc( int year, int month, int day, int hour, int minute, double second, int calendar, int target_body, int center_body, int unit, double *state, char *error );
            int jme_jpl_ecliptic_state_utc_naif( int year, int month, int day, int hour, int minute, double second, int calendar, int target_naif, int center_naif, int unit, double *state, char *error );
            int jme_jpl_file_version(char *buffer, unsigned int buffer_size, char *error);
            int jme_jpl_id_by_name(const char *name, int *id, char *error);
            int jme_jpl_is_available(void);
            int jme_jpl_is_open(void);
            int jme_jpl_is_thread_safe(char *error);
            int jme_jpl_max_supported_order(int segment_type);
            int jme_jpl_name_by_id(int id, char *name, unsigned int name_size, char *error);
            int jme_jpl_open(const char *path, char *error);
            int jme_jpl_open_array(int path_count, const char *const *paths, char *error);
            int jme_jpl_orientation_record_count(char *error);
            int jme_jpl_orientation_record_index( int index, int *target, double *first_time, double *last_time, int *frame, int *segment_type, char *error );
            int jme_jpl_orientation_state_naif( double jd_time, int target_naif, int unit, double *state, char *error );
            int jme_jpl_orientation_state_order_naif( double jd0, double time_offset, int target_naif, int unit, int order, double *state, char *error );
            int jme_jpl_orientation_state_split_naif( double jd0, double time_offset, int target_naif, int unit, double *state, char *error );
            int jme_jpl_orientation_state_utc_naif( int year, int month, int day, int hour, int minute, double second, int calendar, int target_naif, int unit, double *state, char *error );
            int jme_jpl_position_record_count(char *error);
            int jme_jpl_position_record_index( int index, int *target, int *center, double *first_time, double *last_time, int *frame, int *segment_type, char *error );
            int jme_jpl_prefetch(char *error);
            int jme_jpl_rotational_angular_momentum_state_naif( double jd_time, int target_naif, int unit, double *state, char *error );
            int jme_jpl_rotational_angular_momentum_state_order_naif( double jd0, double time_offset, int target_naif, int unit, int order, double *state, char *error );
            int jme_jpl_rotational_angular_momentum_state_split_naif( double jd0, double time_offset, int target_naif, int unit, double *state, char *error );
            int jme_jpl_rotational_angular_momentum_state_utc_naif( int year, int month, int day, int hour, int minute, double second, int calendar, int target_naif, int unit, double *state, char *error );
            int jme_jpl_timescale(void);
            double jme_julian_day(int year, int month, int day, double hour, int calendar);
            int jme_lat_to_lmt(double jd_lat, double geo_lon, double *jd_lmt, char *error);
            int jme_lmt_to_lat(double jd_lmt, double geo_lon, double *jd_lat, char *error);
            int jme_lun_eclipse_how(double jd_ut, int flags, double *geopos, double *attr, char *error);
            int jme_lun_eclipse_when(double jd_start, int flags, int iflag, double *tret, int backward, char *error);
            int jme_lun_eclipse_when_loc(double jd_start, int flags, double *geopos, double *tret, double *attr, int backward, char *error);
            int jme_lun_occult_when_glob(double jd_start, int body, const char *starname, int flags, int iflag, double *tret, int backward, char *error);
            int jme_lun_occult_when_loc(double jd_start, int body, const char *starname, int flags, double *geopos, double *tret, double *attr, int backward, char *error);
            int jme_lun_occult_where(double jd_ut, int body, const char *starname, int flags, double *geopos, double *attr, char *error);
            void jme_matrix_identity(double *m);
            void jme_matrix_multiply(const double *a, const double *b, double *out);
            void jme_matrix_rotate_x(double angle_rad, double *m);
            void jme_matrix_rotate_y(double angle_rad, double *m);
            void jme_matrix_rotate_z(double angle_rad, double *m);
            void jme_matrix_transform_state(const double *m, const double *input, double *output);
            int jme_meeus_moon_state(double jd_et, double *results);
            int jme_meeus_planet_state(double jd_et, int body, double *results);
            int jme_meeus_sun_state(double jd_et, double *results);
            int jme_mooncross(double x2cross, double jd_ut, int flags, double *tret, char *error);
            int jme_mooncross_node(double jd_ut, int flags, double *tret, char *error);
            int jme_mooncross_node_ut(double jd_ut, int flags, double *tret, char *error);
            int jme_mooncross_ut(double x2cross, double jd_ut, int flags, double *tret, char *error);
            int jme_moshier_planet_state(double jd_et, int body, double *results);
            int jme_nod_aps(double jd_et, int body, int flags, int method, double *tret, char *error);
            int jme_nod_aps_ut(double jd_ut, int body, int flags, int method, double *tret, char *error);
            int jme_orbit_max_min_true_distance(double jd_et, int body, int flags, double *tmax, double *tmin, double *dmax, double *dmin, char *error);
            int jme_pheno(double jd_et, int body, int flags, double *attr, char *error);
            int jme_pheno_ut(double jd_ut, int body, int flags, double *attr, char *error);
            double jme_radian_midpoint(double x1, double x0);
            double jme_radian_normalize(double x);
            double jme_radians_difference_signed(double p1, double p2);
            double jme_radians_to_degrees(double radians);
            int jme_rectangular_to_spherical_state(const double *rectangular, double *spherical);
            double jme_refract(double altitude, double pressure, double temperature, int calc_flag);
            double jme_refract_extended( double altitude, double geoalt, double pressure, double temperature, double lapse_rate, int calc_flag, double *out );
            void jme_reverse_julian_day( double jd, int calendar, int *year, int *month, int *day, double *hour );
            int jme_rise_trans(double jd_ut, int body, const char *starname, int flags, int rsmi, double *geopos, double atpress, double attemp, double *tret, char *error);
            int jme_rise_trans_true_hor(double jd_ut, int body, const char *starname, int flags, int rsmi, double *geopos, double atpress, double attemp, double horhgt, double *tret, char *error);
            void jme_set_astro_models(const char *models, int flags);
            void jme_set_delta_t_userdef(double dt);
            void jme_set_ephemeris_path(const char *path);
            void jme_set_interpolate_nut(int on);
            void jme_set_jpl_file(const char *path);
            void jme_set_lapse_rate(double lapse_rate);
            void jme_set_sidereal_mode(int sidereal_mode, double t0, double ayan_t0);
            void jme_set_tid_acc(double t_acc);
            void jme_set_topo(double lon, double lat, double altitude);
            double jme_sidereal_time(double jd_ut);
            double jme_sidereal_time0(double jd_ut, double eps, double nut);
            int jme_sol_eclipse_how(double jd_ut, int flags, double *geopos, double *attr, char *error);
            int jme_sol_eclipse_when_glob(double jd_start, int flags, int epheflag, double *tret, int backward, char *error);
            int jme_sol_eclipse_when_loc(double jd_start, int flags, double *geopos, double *tret, double *attr, int backward, char *error);
            int jme_sol_eclipse_where(double jd_ut, int flags, double *geopos, double *attr, char *error);
            int jme_solcross(double x2cross, double jd_ut, int flags, double *tret, char *error);
            int jme_solcross_ut(double x2cross, double jd_ut, int flags, double *tret, char *error);
            double jme_spherical_angular_separation(double lon1, double lat1, double lon2, double lat2);
            double jme_spherical_position_angle(double lon1, double lat1, double lon2, double lat2);
            int jme_spherical_to_rectangular_state(const double *spherical, double *rectangular);
            void jme_split_degree( double ddeg, int roundflag, int *ideg, int *imin, int *isec, double *dsecfr, int *isgn );
            int jme_state_add(const double *left, const double *right, double *output);
            int jme_state_convert_units(const double *input, int input_unit, int output_unit, double *output);
            double jme_state_distance(const double *state);
            double jme_state_light_time_days(const double *state, int unit);
            double jme_state_position_velocity_dot(const double *state);
            int jme_state_scale(const double *input, double factor, double *output);
            double jme_state_speed(const double *state);
            int jme_state_subtract(const double *left, const double *right, double *output);
            int jme_time_equ(double jd_ut, double *e, char *error);
            double jme_topo_arcus_visionis(double jd_ut, double *geopos, double *dat_hel, char *error);
            void jme_utc_time_zone( int year, int month, int day, int hour, int minute, double second, double timezone, int *out_year, int *out_month, int *out_day, int *out_hour, int *out_minute, double *out_second );
            int jme_utc_to_jd( int year, int month, int day, int hour, int minute, double second, int calendar, double *jd_utc );
            int jme_vis_limit_mag(double jd_ut, double *geopos, double *dat_hel, char *error);
            int jme_vsop87_planet_state(double jd_et, int body, double *results);
            int jme_get_frame_bias_matrix(int model, double *m);
            const char *jme_body_name(int body);
            char *jme_centiseconds_to_degree_string(int cs, char *buffer);
            char *jme_centiseconds_to_lonlat_string(int cs, char *buffer);
            char *jme_centiseconds_to_time_string(int cs, char *buffer);
            char *jme_copy_body_name(int body, char *buffer);
            const char *jme_ephemeris_path(void);
            const char *jme_get_ayanamsa_name(int model);
            const char *jme_house_system_name(int house_system);
            const char *jme_jpl_engine_version(char *buffer, size_t buffer_size);
            const char *jme_jpl_file(void);
            const char *jme_library_path(void);
            const char *jme_version(char *buffer, size_t buffer_size);
CDEF;

        if ($libraryPath === null) {
            $libraryPath = self::defaultLibraryPath();
        }

        if (! file_exists($libraryPath)) {
            throw new RuntimeException('JME shared library not found at: ' . $libraryPath);
        }

        self::prepareLibraryDirectory($libraryPath);
        $this->ffi = FFI::cdef($cdef, $libraryPath);
    }

    public function __call(string $name, array $arguments)
    {
        if (! self::isProfileEnabled()) {
            return $this->ffi->$name(...$arguments);
        }

        $start = hrtime(true);
        try {
            return $this->ffi->$name(...$arguments);
        } finally {
            self::recordProfile($name, hrtime(true) - $start);
        }
    }

    public function jme_julian_day(int $year, int $month, int $day, float $hour, int $calendar): float
    {
        return $this->ffi->jme_julian_day($year, $month, $day, $hour, $calendar);
    }

    public function jme_set_sidereal_mode(int $sidereal_mode, float $t0, float $ayan_t0): void
    {
        $this->ffi->jme_set_sidereal_mode($sidereal_mode, $t0, $ayan_t0);
    }

    public function jme_set_ephemeris_path(string $path): void
    {
        $this->profileVoidCall('jme_set_ephemeris_path', fn (): mixed => $this->ffi->jme_set_ephemeris_path($path));
    }

    public function jme_set_jpl_file(string $path): void
    {
        $this->profileVoidCall('jme_set_jpl_file', fn (): mixed => $this->ffi->jme_set_jpl_file($path));
    }

    public function jme_set_astro_models(string $models, int $flags): void
    {
        $this->profileVoidCall('jme_set_astro_models', fn (): mixed => $this->ffi->jme_set_astro_models($models, $flags));
    }

    public function configureEngine(string $engine, ?string $ephemerisPath = null, ?string $jplFile = null): void
    {
        $engine = $this->normalizeEngine($engine);

        if (is_string($ephemerisPath) && $ephemerisPath !== '') {
            $this->jme_set_ephemeris_path($ephemerisPath);
        }

        if (is_string($jplFile) && $jplFile !== '') {
            $this->jme_set_jpl_file($jplFile);
        }

        if ($engine === 'JPL') {
            $kernelPath = $this->resolveUsableJplKernelPath($ephemerisPath, $jplFile);
            $error = $this->ffi->new('char[256]');

            $this->jme_set_jpl_file($kernelPath);
            $openResult = $this->jme_jpl_open($kernelPath, $error);
            if ($openResult !== self::JME_OK) {
                throw new RuntimeException('ENGINE=JPL is configured but unusable: ' . FFI::string($error));
            }
        }

        $this->jme_set_astro_models('ENGINE=' . $engine, 0);
    }

    public function jme_jpl_open(?string $path, CData $error): int
    {
        return $this->profileCall('jme_jpl_open', fn (): int => $this->ffi->jme_jpl_open($path, $error));
    }

    public function jme_calc_ut(float $jd_ut, int $body, int $flags, CData $results, CData $error): int
    {
        $name = 'jme_calc_ut/body=' . $body . '/flags=' . $flags;
        if (self::isProfileEnabled()) {
            self::$profileUniqueInputs[$name][$body . '|' . $flags . '|' . sprintf('%.17g', $jd_ut)] = true;
        }

        return $this->profileCall($name, fn (): int => $this->ffi->jme_calc_ut($jd_ut, $body, $flags, $results, $error));
    }

    public function jme_houses(float $jd_ut, float $geo_lat, float $geo_lon, int $house_system, CData $cusps, CData $ascmc): int
    {
        return $this->profileCall('jme_houses', fn (): int => $this->ffi->jme_houses($jd_ut, $geo_lat, $geo_lon, $house_system, $cusps, $ascmc));
    }

    public function jme_get_ayanamsa_ut(float $jd_ut): float
    {
        return $this->profileCall('jme_get_ayanamsa_ut', fn (): float => $this->ffi->jme_get_ayanamsa_ut($jd_ut));
    }

    public function jme_lun_eclipse_when(float $jd_start, int $flags, int $iflag, CData $tret, int $backward, CData $error): int
    {
        return $this->profileCall('jme_lun_eclipse_when', fn (): int => $this->ffi->jme_lun_eclipse_when($jd_start, $flags, $iflag, $tret, $backward, $error));
    }

    public function jme_sol_eclipse_when_glob(float $jd_start, int $flags, int $epheflag, CData $tret, int $backward, CData $error): int
    {
        return $this->profileCall('jme_sol_eclipse_when_glob', fn (): int => $this->ffi->jme_sol_eclipse_when_glob($jd_start, $flags, $epheflag, $tret, $backward, $error));
    }

    public function jme_lun_eclipse_how(float $jd_ut, int $flags, CData $geopos, CData $attr, CData $error): int
    {
        return $this->profileCall('jme_lun_eclipse_how', fn (): int => $this->ffi->jme_lun_eclipse_how($jd_ut, $flags, $geopos, $attr, $error));
    }

    public function jme_lun_eclipse_when_loc(float $jd_start, int $flags, CData $geopos, CData $tret, CData $attr, int $backward, CData $error): int
    {
        return $this->profileCall('jme_lun_eclipse_when_loc', fn (): int => $this->ffi->jme_lun_eclipse_when_loc($jd_start, $flags, $geopos, $tret, $attr, $backward, $error));
    }

    public function jme_sol_eclipse_how(float $jd_ut, int $flags, CData $geopos, CData $attr, CData $error): int
    {
        return $this->profileCall('jme_sol_eclipse_how', fn (): int => $this->ffi->jme_sol_eclipse_how($jd_ut, $flags, $geopos, $attr, $error));
    }

    public function jme_sol_eclipse_when_loc(float $jd_start, int $flags, CData $geopos, CData $tret, CData $attr, int $backward, CData $error): int
    {
        return $this->profileCall('jme_sol_eclipse_when_loc', fn (): int => $this->ffi->jme_sol_eclipse_when_loc($jd_start, $flags, $geopos, $tret, $attr, $backward, $error));
    }

    public function jme_jd_to_utc(float $jd, int $calendar, CData $year, CData $month, CData $day, CData $hour, CData $minute, CData $second): void
    {
        $this->profileVoidCall('jme_jd_to_utc', fn (): mixed => $this->ffi->jme_jd_to_utc($jd, $calendar, $year, $month, $day, $hour, $minute, $second));
    }

    public function jme_rise_trans(float $jd_ut, int $body, ?string $starname, int $flags, int $rsmi, CData $geopos, float $atpress, float $attemp, CData $tret, CData $error): int
    {
        return $this->profileCall('jme_rise_trans', fn (): int => $this->ffi->jme_rise_trans($jd_ut, $body, $starname, $flags, $rsmi, $geopos, $atpress, $attemp, $tret, $error));
    }

    public function getFFI(): FFI
    {
        return $this->ffi;
    }

    public function jme_ephemeris_path(): string
    {
        $path = $this->ffi->jme_ephemeris_path();

        if ($path === null) {
            return '';
        }

        return is_string($path) ? $path : FFI::string($path);
    }

    public function jme_jpl_file(): string
    {
        $path = $this->ffi->jme_jpl_file();

        if ($path === null) {
            return '';
        }

        return is_string($path) ? $path : FFI::string($path);
    }

    public static function writeProfileReport(): void
    {
        if (self::$profileWritten) {
            return;
        }
        self::$profileWritten = true;

        if (self::$profile === []) {
            return;
        }

        uasort(self::$profile, static fn (array $a, array $b): int => $b['ns'] <=> $a['ns']);
        fwrite(STDERR, "[jme-ffi-profile] method,calls,total_s,per_call_s\n");
        foreach (self::$profile as $name => $row) {
            $calls = max(1, $row['calls']);
            $total = $row['ns'] / 1_000_000_000;
            fwrite(
                STDERR,
                sprintf("[jme-ffi-profile] %s,%d,%.9f,%.12f\n", $name, $row['calls'], $total, $total / $calls)
            );
            if (isset(self::$profileUniqueInputs[$name])) {
                fwrite(STDERR, sprintf("[jme-ffi-profile-unique] %s,%d\n", $name, count(self::$profileUniqueInputs[$name])));
            }
        }
    }

    private static function defaultLibraryPath(): string
    {
        $family = PHP_OS_FAMILY;
        $arch = strtolower(php_uname('m'));
        $arch = match (true) {
            in_array($arch, ['x86_64', 'amd64'], true) => 'x64',
            in_array($arch, ['aarch64', 'arm64'], true) => 'arm64',
            default => $arch,
        };

        $file = match ($family) {
            'Windows' => 'jme.dll',
            'Darwin' => 'libjme.dylib',
            default => 'libjme.so',
        };

        $dir = match ($family) {
            'Windows' => 'windows-' . $arch,
            'Darwin' => 'macos-' . $arch,
            default => 'linux-' . $arch,
        };

        return dirname(__DIR__, 2) . '/libs/' . $dir . '/' . $file;
    }

    private static function prepareLibraryDirectory(string $libraryPath): void
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return;
        }

        $directory = dirname($libraryPath);
        $currentPath = getenv('PATH');
        $segments = $currentPath === false ? [] : array_filter(explode(PATH_SEPARATOR, $currentPath), static fn (string $segment): bool => $segment !== '');

        foreach ($segments as $segment) {
            if (strcasecmp(rtrim($segment, '\\/'), rtrim($directory, '\\/')) === 0) {
                return;
            }
        }

        $updatedPath = $directory . PATH_SEPARATOR . ($currentPath === false ? '' : $currentPath);
        putenv('PATH=' . $updatedPath);
        $_ENV['PATH'] = $updatedPath;
        $_SERVER['PATH'] = $updatedPath;
    }

    private function normalizeEngine(string $engine): string
    {
        return match (strtoupper($engine)) {
            'AUTO' => 'AUTO',
            'JPL' => 'JPL',
            'MOSHIER' => 'MOSHIER',
            'VSOP_ELP_MEEUS', 'ANALYTICAL' => 'VSOP_ELP_MEEUS',
            default => strtoupper($engine),
        };
    }

    private function resolveUsableJplKernelPath(?string $ephemerisPath, ?string $jplFile): string
    {
        $candidates = [];

        foreach ([$jplFile, $ephemerisPath, $this->jme_jpl_file(), $this->jme_ephemeris_path()] as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                $candidates[] = $candidate;
            }
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }

            if (is_dir($candidate)) {
                $matches = array_merge(
                    glob(rtrim($candidate, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.bsp') ?: [],
                    glob(rtrim($candidate, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.BSP') ?: []
                );
                sort($matches, SORT_STRING);
                if ($matches !== []) {
                    return $matches[0];
                }
            }
        }

        throw new RuntimeException(
            'ENGINE=JPL requires a readable .bsp kernel file, but no usable kernel was found in the configured JPL file or ephemeris path. '
            . 'Download kernels from: https://github.com/jayeshmepani/jpl-ephemeris/releases/tag/jpl-kernels'
        );
    }

    /**
     * @template T
     *
     * @param callable(): T $call
     *
     * @return T
     */
    private function profileCall(string $name, callable $call): mixed
    {
        if (! self::isProfileEnabled()) {
            return $call();
        }

        $start = hrtime(true);
        try {
            return $call();
        } finally {
            self::recordProfile($name, hrtime(true) - $start);
        }
    }

    /** @param callable(): mixed $call */
    private function profileVoidCall(string $name, callable $call): void
    {
        if (! self::isProfileEnabled()) {
            $call();
            return;
        }

        $start = hrtime(true);
        try {
            $call();
        } finally {
            self::recordProfile($name, hrtime(true) - $start);
        }
    }

    private static function isProfileEnabled(): bool
    {
        if (self::$profileEnabled !== null) {
            return self::$profileEnabled;
        }

        self::$profileEnabled = getenv('JME_FFI_PROFILE') !== false;
        if (self::$profileEnabled) {
            register_shutdown_function([self::class, 'writeProfileReport']);
            if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
                pcntl_async_signals(true);
                pcntl_signal(SIGINT, static function (): void {
                    self::writeProfileReport();
                    exit(130);
                });
                pcntl_signal(SIGTERM, static function (): void {
                    self::writeProfileReport();
                    exit(143);
                });
            }
        }

        return self::$profileEnabled;
    }

    private static function recordProfile(string $name, int $elapsedNs): void
    {
        self::$profile[$name] ??= ['calls' => 0, 'ns' => 0];
        self::$profile[$name]['calls']++;
        self::$profile[$name]['ns'] += $elapsedNs;
    }
}
