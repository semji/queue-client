<?php

/*
Sample atoum configuration file to have code coverage in html format and the treemap.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

// HTML

/*
Please replace in next line /path/to/destination/directory by your destination directory path for html files.
*/
$coverageHtmlField = new atoum\report\fields\runner\coverage\html('Queue Client', 'codeCoverage');

/*
Please replace in next line http://url/of/web/site by the root url of your code coverage web site.
*/
$coverageHtmlField->setRootUrl('/');

// Treemap (not mandatory)

/*
Please replace in next line /path/to/destination/directory by your destination directory path for html files.
*/
$coverageTreemapField = new atoum\report\fields\runner\coverage\treemap('Queue Client', 'codeCoverage');

/*
Please replace in next line http://url/of/treemap by the root url of your treemap web site.
*/
$coverageTreemapField
	->setTreemapUrl('')
	->setHtmlReportBaseUrl($coverageHtmlField->getRootUrl())
;

$script
	->addDefaultReport()
		->addField($coverageHtmlField)
		->addField($coverageTreemapField)
;

$cloverWriter = new atoum\writers\file('coverage.xml');
$cloverReport = new atoum\reports\asynchronous\clover();
$cloverReport->addWriter($cloverWriter);
$runner->addReport($cloverReport);
