<?php
namespace NovoBoletoPHP\Santander;

use \NovoBoletoPHP\Base\Boleto as BoletoBase;
use \NovoBoletoPHP\FormatterHelper;

class Boleto extends BoletoBase {
    public function getTemplate()
    {
        return 'bancos/layout_santander.html';
    }

    public function getLogoBanco() {
        return 'logosantander2.jpg';
    }
}