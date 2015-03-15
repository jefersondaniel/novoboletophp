<?php
namespace NovoBoletoPHP\Base;

class Boleto {
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
        $data['logo_banco'] = $this->getLogoBanco();
        return $data;
    }

    public function getTemplate()
    {
        return 'layout_base.html';
    }

    public function getLogoBanco() {
        return 'logobb.jpg';
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

    public function modulo11($num, $base=9, $r=0) {
        $soma = 0;
        $fator = 2; 
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num,$i-1,1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            
            //corrigido
            if ($digito == 10) {
                $digito = "X";
            }

            /*
            alterado por mim, Daniel Schultz

            Vamos explicar:

            O módulo 11 só gera os digitos verificadores do nossonumero,
            agencia, conta e digito verificador com codigo de barras (aquele que fica sozinho e triste na linha digitável)
            só que é foi um rolo...pq ele nao podia resultar em 0, e o pessoal do phpboleto se esqueceu disso...
            
            No BB, os dígitos verificadores podem ser X ou 0 (zero) para agencia, conta e nosso numero,
            mas nunca pode ser X ou 0 (zero) para a linha digitável, justamente por ser totalmente numérica.

            Quando passamos os dados para a função, fica assim:

            Agencia = sempre 4 digitos
            Conta = até 8 dígitos
            Nosso número = de 1 a 17 digitos

            A unica variável que passa 17 digitos é a da linha digitada, justamente por ter 43 caracteres

            Entao vamos definir ai embaixo o seguinte...

            se (strlen($num) == 43) { não deixar dar digito X ou 0 }
            */
            
            if (strlen($num) == "43") {
                //então estamos checando a linha digitável
                if ($digito == "0" or $digito == "X" or $digito > 9) {
                        $digito = 1;
                }
            }
            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;
            return $resto;
        }
    }

    protected function geraCodigoBanco($numero)
    {
        $parte1 = substr($numero, 0, 3);
        $parte2 = $this->modulo11($parte1);
        return $parte1 . "-" . $parte2;
    }
}