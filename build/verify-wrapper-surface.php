<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$nativeRoot = getenv('JME_SOURCE_PATH') ?: $root . '/../../jpl-ephemeris-';
$wrapperPath = $root . '/src/FFI/JmeEphFFI.php';
$apiTrackingPath = $nativeRoot . '/docs/API_TRACKING.md';
$headerPaths = [
    $nativeRoot . '/include/jme/jme.h',
    $nativeRoot . '/include/jme/jme_extended.h',
];

foreach (array_merge([$wrapperPath, $apiTrackingPath], $headerPaths) as $path) {
    if (! is_file($path)) {
        fwrite(STDERR, "Required file not found: {$path}\n");
        exit(1);
    }
}

$wrapperText = file_get_contents($wrapperPath);
$apiTracking = file_get_contents($apiTrackingPath);
$headerText = implode("\n", array_map('file_get_contents', $headerPaths));

preg_match_all('/\|\s*\d+\s*\|\s*`(jme_[A-Za-z0-9_]+)`\s*\|/', $apiTracking, $trackedFunctionMatches);
$trackedFunctions = array_values(array_unique($trackedFunctionMatches[1]));

preg_match('/\$cdef = <<<' . "'CDEF'" . "\n(.*?)\nCDEF;/s", $wrapperText, $cdefMatch);
if (! isset($cdefMatch[1])) {
    fwrite(STDERR, "Could not locate cdef block in wrapper.\n");
    exit(1);
}

$normalizeDeclaration = static function (string $declaration): string {
    $declaration = preg_replace('!/\*.*?\*/!s', ' ', $declaration);
    $declaration = preg_replace('/\s+/', ' ', trim($declaration));
    $declaration = preg_replace('/\s*([(),;])\s*/', '$1', $declaration);
    $declaration = preg_replace('/\s*\*\s*/', ' *', $declaration);
    $declaration = preg_replace('/\bconst \*/', 'const*', $declaration);
    $declaration = str_replace('const*', 'const *', $declaration);
    return trim($declaration);
};

$extractDeclarations = static function (string $text) use ($normalizeDeclaration): array {
    preg_match_all('/((?:const\s+char\s*\*|char\s*\*|int|double|void)\s*jme_[A-Za-z0-9_]+\s*\([^;]*\);)/s', $text, $matches);
    $out = [];
    foreach ($matches[1] as $declaration) {
        if (! preg_match('/\b(jme_[A-Za-z0-9_]+)\s*\(/', $declaration, $nameMatch)) {
            continue;
        }
        $out[$nameMatch[1]] = $normalizeDeclaration($declaration);
    }
    return $out;
};

$headerDeclarations = $extractDeclarations($headerText);
$cdefDeclarations = $extractDeclarations($cdefMatch[1]);

$prototypeErrors = [];
foreach ($trackedFunctions as $functionName) {
    if (! isset($headerDeclarations[$functionName])) {
        $prototypeErrors[] = "Missing header declaration for {$functionName}";
        continue;
    }
    if (! isset($cdefDeclarations[$functionName])) {
        $prototypeErrors[] = "Missing cdef declaration for {$functionName}";
        continue;
    }
    if ($headerDeclarations[$functionName] !== $cdefDeclarations[$functionName]) {
        $prototypeErrors[] = "Prototype mismatch for {$functionName}\nHEADER: {$headerDeclarations[$functionName]}\nCDEF:   {$cdefDeclarations[$functionName]}";
    }
}

if (count($cdefDeclarations) !== count($trackedFunctions)) {
    $prototypeErrors[] = 'cdef declaration count mismatch: expected ' . count($trackedFunctions) . ', got ' . count($cdefDeclarations);
}

if ($prototypeErrors !== []) {
    fwrite(STDERR, implode("\n\n", $prototypeErrors) . "\n");
    exit(1);
}

require_once $wrapperPath;
$reflection = new ReflectionClass(\JmeEph\FFI\JmeEphFFI::class);
$phpConstants = $reflection->getConstants();

preg_match_all('/\bJME_[A-Z0-9_]+\b/', $headerText, $constantMatches);
$trackedConstants = array_values(array_unique($constantMatches[0]));

if (count($trackedConstants) !== 462) {
    fwrite(STDERR, 'Tracked header constant count mismatch: expected 462, got ' . count($trackedConstants) . "\n");
    exit(1);
}

$probeCode = [];
$probeCode[] = '#include <stdio.h>';
$probeCode[] = '#include "jme/jme.h"';
$probeCode[] = 'int main(void) {';
foreach ($trackedConstants as $name) {
    if ($name === 'JME_VERSION') {
        $probeCode[] = '    printf("' . $name . '\\tstring\\t%s\\n", ' . $name . ');';
        continue;
    }
    if (in_array($name, ['JME_AU_KM', 'JME_SPEED_OF_LIGHT_KM_PER_SEC', 'JME_SECONDS_PER_DAY'], true)) {
        $probeCode[] = '    printf("' . $name . '\\tdouble\\t%.17g\\t%a\\n", (double)(' . $name . '), (double)(' . $name . '));';
        continue;
    }
    if (in_array($name, ['JME_JME_H', 'JME_EXTENDED_H'], true)) {
        $probeCode[] = '    printf("' . $name . '\\tint\\t1\\t0x1\\n");';
        continue;
    }
    $probeCode[] = '    printf("' . $name . '\\tint\\t%lld\\t0x%llx\\n", (long long)(' . $name . '), (unsigned long long)(' . $name . '));';
}
$probeCode[] = '    return 0;';
$probeCode[] = '}';

$tmpDir = sys_get_temp_dir() . '/jme_php_verify_' . getmypid();
@mkdir($tmpDir, 0775, true);
$probeC = $tmpDir . '/constants_probe.c';
$probeBin = $tmpDir . '/constants_probe';
file_put_contents($probeC, implode("\n", $probeCode) . "\n");

$compileCommand = sprintf(
    'cc -I%s %s -o %s 2>&1',
    escapeshellarg($nativeRoot . '/include'),
    escapeshellarg($probeC),
    escapeshellarg($probeBin)
);
$compileOutput = [];
$compileExit = 0;
exec($compileCommand, $compileOutput, $compileExit);
if ($compileExit !== 0) {
    fwrite(STDERR, "Failed to compile constant probe:\n" . implode("\n", $compileOutput) . "\n");
    exit(1);
}

$probeOutput = [];
$probeExit = 0;
exec(escapeshellarg($probeBin), $probeOutput, $probeExit);
if ($probeExit !== 0) {
    fwrite(STDERR, "Constant probe execution failed.\n");
    exit(1);
}

$cValues = [];
foreach ($probeOutput as $line) {
    [$name, $type, $value, $aux] = array_pad(explode("\t", $line), 4, '');
    $cValues[$name] = ['type' => $type, 'value' => $value, 'aux' => $aux];
}

$constantErrors = [];
foreach ($trackedConstants as $name) {
    if (! array_key_exists($name, $phpConstants)) {
        $constantErrors[] = "Missing PHP constant {$name}";
        continue;
    }
    if (! isset($cValues[$name])) {
        $constantErrors[] = "Missing C probe value for {$name}";
        continue;
    }
    $phpValue = $phpConstants[$name];
    $cValue = $cValues[$name];

    if ($cValue['type'] === 'string') {
        if ((string) $phpValue !== $cValue['value']) {
            $constantErrors[] = "String constant mismatch for {$name}: PHP=" . var_export($phpValue, true) . ' C=' . var_export($cValue['value'], true);
        }
        continue;
    }

    if ($cValue['type'] === 'double') {
        $phpString = sprintf('%.17g', (float) $phpValue);
        if ($phpString !== $cValue['value']) {
            $constantErrors[] = "Double constant mismatch for {$name}: PHP={$phpString} C={$cValue['value']}";
        }
        continue;
    }

    $phpInt = (string) (int) $phpValue;
    if ($phpInt !== $cValue['value']) {
        $constantErrors[] = "Integer constant mismatch for {$name}: PHP={$phpInt} C={$cValue['value']}";
    }
}

if (count($phpConstants) !== 462) {
    $constantErrors[] = 'PHP constant count mismatch: expected 462, got ' . count($phpConstants);
}

if ($constantErrors !== []) {
    fwrite(STDERR, implode("\n", $constantErrors) . "\n");
    exit(1);
}

echo 'verified_functions=' . count($trackedFunctions) . PHP_EOL;
echo 'verified_constants=' . count($trackedConstants) . PHP_EOL;
echo 'verified_cdef_declarations=' . count($cdefDeclarations) . PHP_EOL;
