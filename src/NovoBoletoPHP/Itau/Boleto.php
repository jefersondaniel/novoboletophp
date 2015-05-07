<?php
namespace NovoBoletoPHP\Itau;

use \NovoBoletoPHP\Base\Boleto as BoletoBase;
use \NovoBoletoPHP\FormatterHelper;

class Boleto extends BoletoBase {
    public function getTemplate()
    {
        return 'bancos/layout_itau.html';
    }

    public function getLogoBanco()
    {
        return 'logoitau.jpg';
    }

    public function getCodigoBanco()
    {
        return 341;
    }

    protected function filterData(array $data)
    {
        $data = parent::filterData($data);
        return $data;
    }

    public function montaLinhaDigitavel($codigo)
    {
        return $codigo;
    }

    public function digitoVerificadorBarra($numero)
    {
        return 0;
    }

    /**
     *   Autor:
     *           Pablo Costa <pablo@users.sourceforge.net>
     *
     *   Função:
     *    Calculo do Modulo 11 para geracao do digito verificador 
     *    de boletos bancarios conforme documentos obtidos 
     *    da Febraban - www.febraban.org.br 
     *
     *   Entrada:
     *     $num: string numérica para a qual se deseja calcularo digito verificador;
     *     $base: valor maximo de multiplicacao [2-$base]
     *     $r: quando especificado um devolve somente o resto
     *
     *   Saída:
     *     Retorna o Digito verificador.
     *
     *   Observações:
     *     - Script desenvolvido sem nenhum reaproveitamento de código pré existente.
     *     - Assume-se que a verificação do formato das variáveis de entrada é feita antes da execução deste script.
     */
    public function modulo11($num, $base = 9, $r = 0)
    {
        $soma = 0;
        $fator = 2;
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i-1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            $fator++;
        }
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } else if ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }
}