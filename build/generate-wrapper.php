<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$nativeRoot = getenv('JME_SOURCE_PATH') ?: $root . '/../../jpl-ephemeris-';
$apiTrackingPath = $nativeRoot . '/docs/API_TRACKING.md';
$headerPaths = [
    $nativeRoot . '/include/jme/jme.h',
    $nativeRoot . '/include/jme/jme_extended.h',
];

foreach (array_merge([$apiTrackingPath], $headerPaths) as $path) {
    if (! is_file($path)) {
        fwrite(STDERR, "Required native source file not found: {$path}\n");
        exit(1);
    }
}

$apiTracking = file_get_contents($apiTrackingPath);
preg_match_all('/\|\s*\d+\s*\|\s*`(jme_[A-Za-z0-9_]+)`\s*\|/', $apiTracking, $functionMatches);
$trackedFunctions = array_values(array_unique($functionMatches[1]));

$headers = [];
foreach ($headerPaths as $path) {
    $headers[$path] = file_get_contents($path);
}

$functionDeclarations = [];
foreach ($headers as $header) {
    $normalized = preg_replace('!/\*.*?\*/!s', '', $header);
    $normalized = preg_replace('/^\s*#.*$/m', '', $normalized);
    $chunks = explode(';', $normalized);
    foreach ($chunks as $chunk) {
        if (! str_contains($chunk, 'jme_') || ! str_contains($chunk, '(')) {
            continue;
        }
        $chunk = trim($chunk);
        if ($chunk === '' || str_starts_with($chunk, 'typedef')) {
            continue;
        }
        if (! preg_match('/\b(jme_[A-Za-z0-9_]+)\s*\(/', $chunk, $match)) {
            continue;
        }
        $name = $match[1];
        $chunk = preg_replace('/\s+/', ' ', $chunk);
        $functionDeclarations[$name] = trim($chunk) . ';';
    }
}

$orderedDeclarations = [];
$missingFunctions = [];
foreach ($trackedFunctions as $functionName) {
    if (! isset($functionDeclarations[$functionName])) {
        $missingFunctions[] = $functionName;
        continue;
    }
    $orderedDeclarations[] = $functionDeclarations[$functionName];
}

if ($missingFunctions !== []) {
    fwrite(STDERR, "Missing declarations for tracked functions: " . implode(', ', $missingFunctions) . "\n");
    exit(1);
}

$constantOrder = [];
$constantValues = [];

$evaluate = static function (string $expression) use (&$constantValues) {
    $expression = trim($expression);
    if ($expression === '') {
        return 1;
    }
    if (preg_match('/^"(.*)"$/s', $expression, $match)) {
        return stripcslashes($match[1]);
    }

    $resolved = preg_replace_callback(
        '/\bJME_[A-Z0-9_]+\b/',
        static function (array $match) use (&$constantValues) {
            $name = $match[0];
            if (! array_key_exists($name, $constantValues)) {
                throw new RuntimeException("Unknown constant reference: {$name}");
            }
            return is_string($constantValues[$name]) ? var_export($constantValues[$name], true) : (string) $constantValues[$name];
        },
        $expression
    );

    if (! preg_match('~^[0-9A-Za-z_+\-*/%<>&|(). "\']+$~', $resolved)) {
        throw new RuntimeException("Unsafe expression: {$expression}");
    }

    return eval('return ' . $resolved . ';');
};

foreach ($headers as $header) {
    if (preg_match_all('/^\s*#define\s+(JME_[A-Z0-9_]+)(?:[ \t]+([^\r\n]+))?\s*$/m', $header, $defineMatches, PREG_SET_ORDER)) {
        foreach ($defineMatches as $define) {
            $name = $define[1];
            $value = isset($define[2]) ? trim($define[2]) : '';
            $constantValues[$name] = $evaluate($value);
            if (! in_array($name, $constantOrder, true)) {
                $constantOrder[] = $name;
            }
        }
    }

    if (preg_match_all('/typedef\s+enum\s+[^{]*\{(.*?)\}\s*[A-Za-z0-9_]+\s*;/s', $header, $enumMatches, PREG_SET_ORDER)) {
        foreach ($enumMatches as $enumMatch) {
            $entries = explode(',', $enumMatch[1]);
            $nextValue = null;
            foreach ($entries as $entry) {
                $entry = trim(preg_replace('!/\*.*?\*/!s', '', $entry));
                if ($entry === '') {
                    continue;
                }
                if (! preg_match('/^(JME_[A-Z0-9_]+)\s*(?:=\s*(.+))?$/s', $entry, $enumEntryMatch)) {
                    continue;
                }

                $name = $enumEntryMatch[1];
                if (isset($enumEntryMatch[2]) && trim($enumEntryMatch[2]) !== '') {
                    $nextValue = $evaluate(trim($enumEntryMatch[2]));
                } elseif ($nextValue === null) {
                    $nextValue = 0;
                } else {
                    $nextValue++;
                }

                $constantValues[$name] = $nextValue;
                if (! in_array($name, $constantOrder, true)) {
                    $constantOrder[] = $name;
                }
            }
        }
    }
}

$constantLines = [];
foreach ($constantOrder as $name) {
    $value = $constantValues[$name];
    $constantLines[] = '    public const ' . $name . ' = ' . var_export($value, true) . ';';
}

$cdefLines = array_map(
    static fn (string $declaration): string => '            ' . $declaration,
    $orderedDeclarations
);

$generated = <<<PHP
<?php

declare(strict_types=1);

namespace JmeEph\\FFI;

use FFI;
use RuntimeException;

class JmeEphFFI
{
%s

    private FFI \$ffi;

    public function __construct(?string \$libraryPath = null)
    {
        \$cdef = <<<'CDEF'
%s
CDEF;

        if (\$libraryPath === null) {
            \$libraryPath = self::defaultLibraryPath();
        }

        if (! file_exists(\$libraryPath)) {
            throw new RuntimeException('JME shared library not found at: ' . \$libraryPath);
        }

        \$this->ffi = FFI::cdef(\$cdef, \$libraryPath);
    }

    private static function defaultLibraryPath(): string
    {
        \$family = PHP_OS_FAMILY;
        \$arch = strtolower(php_uname('m'));
        \$arch = match (true) {
            in_array(\$arch, ['x86_64', 'amd64'], true) => 'x64',
            in_array(\$arch, ['aarch64', 'arm64'], true) => 'arm64',
            default => \$arch,
        };

        \$file = match (\$family) {
            'Windows' => 'jme.dll',
            'Darwin' => 'libjme.dylib',
            default => 'libjme.so',
        };

        \$dir = match (\$family) {
            'Windows' => 'windows-' . \$arch,
            'Darwin' => 'macos-' . \$arch,
            default => 'linux-' . \$arch,
        };

        return dirname(__DIR__, 2) . '/libs/' . \$dir . '/' . \$file;
    }

    public function getFFI(): FFI
    {
        return \$this->ffi;
    }

    public function __call(string \$name, array \$arguments)
    {
        return \$this->ffi->\$name(...\$arguments);
    }
}
PHP;

$generated = sprintf($generated, implode("\n", $constantLines), implode("\n", $cdefLines));

$outputPath = $root . '/src/FFI/JmeEphFFI.php';
file_put_contents($outputPath, $generated);

echo "Generated wrapper: {$outputPath}\n";
echo "Functions: " . count($trackedFunctions) . "\n";
echo "Constants: " . count($constantOrder) . "\n";
