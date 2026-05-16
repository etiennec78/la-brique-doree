<?php
$configPath = __DIR__ . '/../conf/settings.json';

if (file_exists($configPath)) {
    $content = file_get_contents($configPath);
    return json_decode($content, true) ?: [];
}

return [];
