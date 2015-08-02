<?php
namespace NovoBoletoPHP\Base;

abstract class Boleto {
    protected $twig;
    public $data;

    public function __construct($twig, array $data)
    {
        $this->twig = $twig;
        $this->data = $this->filterData($data);
        $this->configure();
    }

    protected function configure()
    {

    }

    protected function filterData(array $data)
    {
        $codigoBanco = $this->getCodigoBancoComDv();

        $data = array_merge($data, array(
            'logo_banco' => $this->getLogoBanco(),
            'codigo_banco_com_dv' => $this->getCodigoBancoComDv(),
            'texto_sacado' => 'Pagador',
            'texto_cedente' => 'Beneficiário',
            'exibir_demonstrativo_na_ficha' => true,
            'exibir_demonstrativo_no_recibo' => false,
        ));

        return $data;
    }

    abstract public function getTemplate();

    abstract public function getLogoBanco();

    abstract public function getCodigoBanco();

    public function getCodigoBancoFormatado()
    {
        return sprintf('%03d', $this->getCodigoBanco());
    }

    protected function getCodigoBancoComDv()
    {
        $numero = $this->getCodigoBancoFormatado();
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->modulo11($parte1);
        return $parte1 . "-" . $parte2;
    }

    public function asHTML()
    {
        return $this->twig->render($this->getTemplate(), $this->data);
    }

    public function fatorVencimento($data)
    {
        $data = explode("/", $data);
        $ano = $data[2];
        $mes = $data[1];
        $dia = $data[0];
        return(abs(($this->dateToDays("1997","10","07")) - ($this->dateToDays($ano, $mes, $dia))));
    }

    protected function dateToDays($year,$month,$day)
    {
        $century = substr($year, 0, 2);
        $year = substr($year, 2, 2);

        if ($month > 2) {
            $month -= 3;
        } else {
            $month += 9;
            if ($year) {
                $year--;
            } else {
                $year = 99;
                $century --;
            }
        }

        return ( floor((  146097 * $century)    /  4 ) +
                floor(( 1461 * $year)        /  4 ) +
                floor(( 153 * $month +  2) /  5 ) +
                    $day +  1721119);
    }

    public function modulo10($num)
    {
        $numtotal10 = 0;
        $fator = 2;
     
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial10[$i] = $numeros[$i] * $fator;
            $numtotal10 .= $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            }
            else {
                $fator = 2; 
            }
        }
        
        $soma = 0;
        for ($i = strlen($numtotal10); $i > 0; $i--) {
            $numeros[$i] = substr($numtotal10,$i-1,1);
            $soma += $numeros[$i]; 
        }
        $resto = $soma % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;
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
        } elseif ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }
}