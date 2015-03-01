<?php
namespace NovoBoletoPHP\Api\Soap;

use \NovoBoletoPHP\BoletoFactory;

class Service
{
    /**
     * Gera um boleto como html
     * @param int $codigoBanco
     * @param wrapper $boleto @className=\NovoBoletoPHP\Api\Soap\Boleto
     * @return string
     */
    public function makeBoletoAsHTML($codigoBanco, $boleto)
    {
        $boleto = new Boleto($boleto);
        $factory = new BoletoFactory;
        return $factory->makeBoletoAsHTML($codigoBanco, $boleto->toArray());
    }
}