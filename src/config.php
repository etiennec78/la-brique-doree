<?php
$configPath = __DIR__ . '/../conf/settings.json';
return json_decode(file_get_contents($configPath), true);
