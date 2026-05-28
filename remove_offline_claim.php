<?php
$file = 'resources/views/donor/dashboard.blade.php';
$lines = file($file);
$newLines = [];
foreach ($lines as $index => $line) {
    // 0-indexed, so lines 64-121 are index 63-120
    if ($index >= 63 && $index <= 120) {
        continue;
    }
    $newLines[] = $line;
}
file_put_contents($file, implode('', $newLines));
echo "Removed offline-claim section from dashboard.";
