<?php
// CLI script to scaffold a new kid folder under scripts/ by copying the 'kid' template,
// and update index.php and scripts/home.php to include the new kid routes/views.

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

$root = __DIR__ . '..';
$template = __DIR__ . '/kid';

echo "Create a new kid (alphanumeric and underscore only).\n";
echo "Kid name: ";
$handle = fopen('php://stdin', 'r');
$kid = trim(fgets($handle));
fclose($handle);

if ($kid === '') {
    echo "No name provided. Aborting.\n";
    exit(1);
}

if (!preg_match('/^[A-Za-z0-9_]+$/', $kid)) {
    echo "Invalid kid name. Use only letters, numbers and underscore.\n";
    exit(1);
}

$dest = __DIR__ . '/' . $kid;
if (file_exists($dest)) {
    echo "Destination $dest already exists. Aborting.\n";
    exit(1);
}

// Recursive copy
function rcopy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0755, true);
    while(false !== ($file = readdir($dir))) {
        if (($file !== '.') && ($file !== '..')) {
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                rcopy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }
    closedir($dir);
}

if (!is_dir($template)) {
    echo "Template directory '$template' not found. Make sure 'kid' template exists.\n";
    exit(1);
}

rcopy($template, $dest);
echo "Copied template to $dest\n";

// Replace placeholder $kid assignment inside the copied kid.php
$kidFile = $dest . '/kid.php';
if (file_exists($kidFile)) {
    $kidPhp = file_get_contents($kidFile);
    if ($kidPhp !== false) {
        // Replace a line like: $kid = "kid"; or $kid='kid';
        $kidPhp = preg_replace('/\$kid\s*=\s*["\']kid["\']\s*;/', '$kid = "' . $kid . '";', $kidPhp, 1);
        file_put_contents($kidFile, $kidPhp);
        echo "Patched $kidFile with kid name '$kid'\n";
    }
} else {
    echo "Warning: expected file $kidFile not found; skipping kid.php patch.\n";
}

// Update index.php: insert cases for the new kid before the default: case
$indexFile = __DIR__ . '/../index.php';
$index = file_get_contents($indexFile);
if ($index === false) {
    echo "Failed to read index.php\n";
    exit(1);
}

$cases = "  case '/$kid':\n    require __DIR__ . \$viewDir . '$kid/home.php';\n    break;\n  case '/$kid/modify':\n    require __DIR__ . \$viewDir . '$kid/modify.php';\n    break;\n  case '/$kid/read':\n    require __DIR__ . \$viewDir . '$kid/read.php';\n    break;\n  case '/$kid/add':\n    require __DIR__ . \$viewDir . '$kid/add.php';\n    break;\n";

$pos = strpos($index, "default:");
if ($pos !== false) {
    // insert just before the default: label
    $index = substr_replace($index, $cases . "\n", $pos, 0);
    file_put_contents($indexFile, $index);
    echo "Updated index.php with routes for '$kid'\n";
} else {
    echo "Could not find insertion point in index.php; please add routes for $kid manually.\n";
}

// Update scripts/home.php: add a require for the new kid before the closing PHP tag
$homeFile = __DIR__ . '/home.php';
$home = file_get_contents($homeFile);
if ($home === false) {
    echo "Failed to read scripts/home.php\n";
    exit(1);
}

$requireLine = "\n    echo '<br>';\n    require __DIR__ . '/$kid/home.php';\n";

// Insert the require after the last existing require for a kid. We'll try to insert before the closing PHP tag 

$closingPos = strrpos($home, '?>');
if ($closingPos !== false) {
    // place before php closing tag
    $home = substr_replace($home, $requireLine, $closingPos, 0);
    file_put_contents($homeFile, $home);
    echo "Updated scripts/home.php to include $kid/home.php\n";
} else {
    echo "Couldn't find closing PHP tag in scripts/home.php; please add require for $kid manually: require __DIR__ . '/$kid/home.php';\n";
}

echo "Done.\n";
