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
        $data = array_merge($data, array(
            'exibir_demonstrativo_na_ficha' => false,
            'exibir_demonstrativo_no_recibo' => true,
            'texto_valor_unitario' => 'Valor Documento',
        ));

        $codigobanco = $this->getCodigoBanco();
        $codigo_banco_com_dv = $this->getCodigoBancoComDv();
        $nummoeda = "9";
        $fator_vencimento = $this->fatorVencimento($data["data_vencimento"]);
        //valor tem 10 digitos, sem virgula
        $valor = FormatterHelper::formataNumero($data["valor_boleto"], 10, 0, "valor");
        //agencia é 4 digitos
        $agencia = FormatterHelper::formataNumero($data["agencia"], 4, 0);
        //conta é 5 digitos + 1 do dv
        $conta = FormatterHelper::formataNumero($data["conta"], 5, 0);
        $conta_dv = FormatterHelper::formataNumero($data["conta_dv"], 1, 0);
        //carteira 175
        $carteira = $data["carteira"];
        //nosso_numero no maximo 8 digitos
        $nnum = FormatterHelper::formataNumero($data["nosso_numero"], 8, 0);
        $codigo_barras = $codigobanco.$nummoeda.$fator_vencimento.$valor.$carteira.$nnum.$this->modulo10($agencia.$conta.$carteira.$nnum).$agencia.$conta.$this->modulo10($agencia.$conta).'000';
        // 43 numeros para o calculo do digito verificador
        $dv = $this->digitoVerificadorBarra($codigo_barras);
        // Numero para o codigo de barras com 44 digitos
        $linha = substr($codigo_barras, 0, 4).$dv.substr($codigo_barras, 4, 43);
        $nossonumero = $carteira.'/'.$nnum.'-'.$this->modulo10($agencia.$conta.$carteira.$nnum);
        $agencia_codigo = $agencia." / ". $conta."-".$this->modulo10($agencia.$conta);
        $data["codigo_barras"] = $linha;
        $data["linha_digitavel"] = $this->montaLinhaDigitavel($linha); // verificar
        $data["agencia_codigo"] = $agencia_codigo ;
        $data["nosso_numero"] = $nossonumero;
        $data["codigo_banco_com_dv"] = $codigo_banco_com_dv;

        return $data;
    }

    public function digitoVerificadorBarra($numero)
    {
        $resto2 = $this->modulo11($numero, 9, 1);
        $digito = 11 - $resto2;

        if ($digito == 0 || $digito == 1 || $digito == 10  || $digito == 11) {
            $dv = 1;
        } else {
            $dv = $digito;
        }

        return $dv;
    }

    public function montaLinhaDigitavel($codigo)
    {
            // campo 1
            $banco    = substr($codigo, 0, 3);
            $moeda    = substr($codigo, 3, 1);
            $ccc      = substr($codigo, 19, 3);
            $ddnnum   = substr($codigo, 22, 2);
            $dv1      = $this->modulo10($banco.$moeda.$ccc.$ddnnum);
            // campo 2
            $resnnum  = substr($codigo, 24, 6);
            $dac1     = substr($codigo, 30, 1);//$this->modulo10($agencia.$conta.$carteira.$nnum);
            $dddag    = substr($codigo, 31, 3);
            $dv2      = $this->modulo10($resnnum.$dac1.$dddag);
            // campo 3
            $resag    = substr($codigo, 34, 1);
            $contadac = substr($codigo, 35, 6); //substr($codigo, 35, 5).$this->modulo10(substr($codigo, 35, 5));
            $zeros    = substr($codigo, 41, 3);
            $dv3      = $this->modulo10($resag.$contadac.$zeros);
            // campo 4
            $dv4      = substr($codigo, 4, 1);
            // campo 5
            $fator    = substr($codigo, 5, 4);
            $valor    = substr($codigo, 9, 10);
            
            $campo1 = substr($banco.$moeda.$ccc.$ddnnum.$dv1, 0, 5) . '.' . substr($banco.$moeda.$ccc.$ddnnum.$dv1, 5, 5);
            $campo2 = substr($resnnum.$dac1.$dddag.$dv2, 0, 5) . '.' . substr($resnnum.$dac1.$dddag.$dv2, 5, 6);
            $campo3 = substr($resag.$contadac.$zeros.$dv3, 0, 5) . '.' . substr($resag.$contadac.$zeros.$dv3, 5, 6);
            $campo4 = $dv4;
            $campo5 = $fator.$valor;

            return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }

    public function modulo10($num)
    {
        $numtotal10 = 0;
        $fator = 2;

        // Separacao dos numeros
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i-1, 1);
            // Efetua multiplicacao do numero pelo (falor 10)
            // 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
            $temp = $numeros[$i] * $fator;
            $temp0=0;
            foreach (preg_split('//', $temp, -1, PREG_SPLIT_NO_EMPTY) as $k => $v) {
                $temp0+=$v;
            }
            $parcial10[$i] = $temp0; //$numeros[$i] * $fator;
            // monta sequencia para soma dos digitos no (modulo 10)
            $numtotal10 += $parcial10[$i];
            if ($fator == 2) {
                $fator = 1;
            } else {
                $fator = 2; // intercala fator de multiplicacao (modulo 10)
            }
        }
        
        // várias linhas removidas, vide função original
        // Calculo do modulo 10
        $resto = $numtotal10 % 10;
        $digito = 10 - $resto;
        if ($resto == 0) {
            $digito = 0;
        }

        return $digito;
    }
}