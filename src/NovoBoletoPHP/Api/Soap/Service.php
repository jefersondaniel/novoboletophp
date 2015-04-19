<?php
namespace NovoBoletoPHP\Api\Soap;

use \NovoBoletoPHP\BoletoFactory;

class Service
{
    public $config;

    public function __construct(array $config=array())
    {
        $this->config = $config;
    }

    /**
     * Gera um boleto como html
     * @param int $codigoBanco
     * @param wrapper $boleto @className=\NovoBoletoPHP\Api\Soap\Boleto
     * @return string
     */
    public function makeBoletoAsHTML($codigoBanco, $boleto)
    {
        $boleto = new Boleto($boleto);
        $factory = new BoletoFactory($this->config);
        return $factory->makeBoletoAsHTML($codigoBanco, $boleto->toArray());
    }

    /**
     * Gera um boleto como pdf
     * @param int $codigoBanco
     * @param wrapper $boleto @className=\NovoBoletoPHP\Api\Soap\Boleto
     * @return string
     */
    public function makeBoletoAsPDF($codigoBanco, $boleto)
    {
        $boleto = new Boleto($boleto);
        $factory = new BoletoFactory($this->config);
        return $factory->makeBoletoAsPDF($codigoBanco, $boleto->toArray());
    }
}