<?php
/**
 * Doctors Verification 0.0.5 by Dmitry Shumilin
 * License: GNU GPL v3, see LICENSE
 */
spl_autoload_register(function($classname) {

    if (strpos($classname, 'Chirontex\\DocsVer') !== false) {

        $path = __DIR__.'/src/';

        $file = explode('\\', $classname);

        if (count($file) > 3) $path .= $file[count($file) - 2].'/';

        $file = $file[count($file) - 1].'.php';

        if (file_exists($path.$file)) require_once $path.$file;

    }

});
