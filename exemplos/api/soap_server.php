<?php
require_once '../../vendor/autoload.php';

$serviceClassName =  '\NovoBoletoPHP\Api\Soap\Service';

$service = new NovoBoletoPHP\BoletoFactory();

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator($serviceClassName, 'http://localhost/wsdl-creator/ClassName.php');
    $wsdl->setNamespace("http://foo.bar/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(null, array(
    'uri' => 'http://localhost/wsdl-creator/ClassName.php'
));
$server->setClass($serviceClassName);
$server->handle();