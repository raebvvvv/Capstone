<?php
// Script to update all home links in Afterlogin files to point to index.php

// Directory to search in
$directory = __DIR__ . '/User/Afterlogin';

// Get all PHP files in the directory
$files = glob($directory . '/*.php');

// Counter for changes
$changesCount = 0;

// Loop through each file
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace references to after-landing.php with index.php
    $newContent = preg_replace('#<a class="nav-link"([^>]*?)href="after-landing\.php"#', '<a class="nav-link"$1href="../../index.php"', $content);
    
    // If changes were made, save the file
    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        $changesCount++;
        echo "Updated: " . basename($file) . "<br>\n";
    }
}

echo "<p>Total files updated: $changesCount</p>";