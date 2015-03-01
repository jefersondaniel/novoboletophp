<?php
require_once '../../vendor/autoload.php';

$serviceClassName =  '\NovoBoletoPHP\Api\Soap\Service';
$currentLocation = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI'];

use WSDL\WSDLCreator;

if(isset($_GET['service']) || isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator($serviceClassName, str_replace('wsdl', '', $currentLocation));
    $wsdl->setNamespace($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/');

    if (isset($_GET['wsdl']))
        $wsdl->renderWSDL();
    else if (isset($_GET['service']))
        $wsdl->renderWSDLService();
    exit;

}

$server = new SoapServer(null, array(
    'uri' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . '/' . $_SERVER['REQUEST_URI']
));
$server->setClass($serviceClassName);
$server->handle();