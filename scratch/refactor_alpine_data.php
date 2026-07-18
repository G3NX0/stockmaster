<?php

$file = 'resources/views/store.blade.php';
if (!file_exists($file)) {
    die("File not found: $file\n");
}

$content = file_get_contents($file);

// Find the start and end of the x-data attribute of the html tag
// The tag starts with `<html lang="en" x-data="{`
// The attribute ends with `}">` followed by `<head>`
$startMarker = '<html lang="en" x-data="{';
$endMarker = '}">';

$startPos = strpos($content, $startMarker);
if ($startPos === false) {
    die("Start marker not found.\n");
}

// We want to find the closing `}">` that is right before `<head>`
$endPos = strpos($content, $endMarker, $startPos);
if ($endPos === false) {
    die("End marker not found.\n");
}

// Extract the JS object content
$jsStart = $startPos + strlen($startMarker) - 1; // includes the '{'
$jsLength = $endPos - $jsStart + 1; // includes the '}'
$jsObject = substr($content, $jsStart, $jsLength);

// Build the new html tag
$newHtmlTag = '<html lang="en" x-data="storeApp">';

// Prepare the Alpine.data script
$alpineScript = "\n    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('storeApp', () => ($jsObject));
        });
    </script>\n";

// Replace the html tag and the JS object inside it
$newContent = substr_replace($content, $newHtmlTag, $startPos, strlen($startMarker) + $jsLength + 1); // +1 to cover the ">"

// Now we need to insert the alpineScript before the Alpine.js script tag:
// `<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>`
$alpineJsTag = '<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>';
$insertPos = strpos($newContent, $alpineJsTag);
if ($insertPos === false) {
    die("Alpine JS script tag not found in new content.\n");
}

$newContent = substr_replace($newContent, $alpineScript . "    " . $alpineJsTag, $insertPos, strlen($alpineJsTag));

file_put_contents($file, $newContent);
echo "Successfully refactored Alpine.js data in store.blade.php!\n";
