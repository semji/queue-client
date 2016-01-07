<?php

/*
Sample atoum configuration file to have code coverage in html format and the treemap.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

$cloverWriter = new atoum\writers\file('build/logs/clover.xml');
$stdOutWriter = new \mageekguy\atoum\writers\std\out();
$cli = new \mageekguy\atoum\reports\realtime\cli();
$cli->addWriter($stdOutWriter);
$cloverReport = new atoum\reports\asynchronous\clover();
$cloverReport->addWriter($cloverWriter);
$runner->addReport($cloverReport);
$runner->addReport($cli);
