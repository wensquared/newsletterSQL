<?php 

function config(string $config){

    $config2Array = explode('.', $config);

    $pfad = __DIR__.'/../config/'.$config2Array[0].'.php';

    if (!file_exists($pfad)) {
        $msg = 'Config file not found '.$pfad;
        throw new Exception($msg);
    }
    else {
        $configArray = require($pfad);
    }
    
    unset($config2Array[0]);

    foreach ($config2Array as $configKey) {
        if (!isset($configArray[$configKey])) {
            throw new Exception('Key existiert nicht in der database datei: '.$configKey);
        }
        else{
            $configArray = $configArray[$configKey];
        }
    }
    return $configArray;
}
