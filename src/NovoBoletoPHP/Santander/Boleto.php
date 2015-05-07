<?php
namespace NovoBoletoPHP\Santander;

use \NovoBoletoPHP\Base\Boleto as BoletoBase;
use \NovoBoletoPHP\FormatterHelper;

class Boleto extends BoletoBase {
    public function getTemplate()
    {
        return 'bancos/layout_santander.html';
    }

    public function getLogoBanco()
    {
        return 'logosantander2.jpg';
    }

    public function getCodigoBanco()
    {
        return 33;
    }

    protected function filterData(array $data)
    {
        $data = parent::filterData($data);
        $data = array_merge($data, array(
            'texto_sacado' => 'Pagador',
            'texto_cedente' => 'Beneficiário',
            'texto_valor_unitario' => 'x Valor',
            'exibir_demonstrativo_na_ficha' => false,
            'exibir_demonstrativo_no_recibo' => true,
            'agencia_codigo' => $data['agencia'] . ' / '. $data['conta'] . $data['conta_dv'],
        ));

        $codigoBanco = $this->getCodigoBancoFormatado();
        $moeda = "9";
        $fixo  = "9";
        $ios   = "0";

        $carteira = $data['carteira'];
        $fator_vencimento = $this->fatorVencimento($data["data_vencimento"]);
        $valor = FormatterHelper::formataNumero($data["valor_boleto"], 10, 0, "valor");
        $codigoCedente = FormatterHelper::formataNumero($data["codigo_cedente"], 7, 0);
        $nossoNumero = FormatterHelper::formataNumero($data["nosso_numero"], 7, 0);
        //dv do nosso número
        $dvNossoNumero = $this->modulo11($nossoNumero, 9, 0);
        // nosso número (com dvs) são 13 digitos
        $nossoNumero = "00000".$nossoNumero.$dvNossoNumero;
        $vencimento = $data["data_vencimento"];
        /* Código de Barras */
        $barra = "$codigoBanco$moeda$fator_vencimento$valor$fixo$codigoCedente$nossoNumero$ios$carteira";

        /* Linha Digitável */
        $dv = $this->digitoVerificadorBarra($barra);
        $linha = substr($barra, 0, 4) . $dv . substr($barra, 4);

        $data["codigo_barras"] = $linha;
        $data["linha_digitavel"] = $this->montaLinhaDigitavel($linha);
        $data["nosso_numero"] = $nossoNumero;

        return $data;
    }

    public function montaLinhaDigitavel($codigo)
    {
        // Posição  Conteúdo
        // 1 a 3    Número do banco
        // 4        Código da Moeda - 9 para Real ou 8 - outras moedas
        // 5        Fixo "9'
        // 6 a 9    PSK - codigo cliente (4 primeiros digitos)
        // 10 a 12  Restante do PSK (3 digitos)
        // 13 a 19  7 primeiros digitos do Nosso Numero
        // 20 a 25  Restante do Nosso numero (8 digitos) - total 13 (incluindo digito verificador)
        // 26 a 26  IOS
        // 27 a 29  Tipo Modalidade Carteira
        // 30 a 30  Dígito verificador do código de barras
        // 31 a 34  Fator de vencimento (qtdade de dias desde 07/10/1997 até a data de vencimento)
        // 35 a 44  Valor do título
        
        // 1. Primeiro Grupo - composto pelo código do banco, código da moéda, Valor Fixo "9"
        // e 4 primeiros digitos do PSK (codigo do cliente) e DV (modulo10) deste campo
        $campo1 = substr($codigo, 0, 3) . substr($codigo, 3, 1) . substr($codigo, 19, 1) . substr($codigo, 20, 4);
        $campo1 = $campo1 . $this->modulo10($campo1);
        $campo1 = substr($campo1, 0, 5).'.'.substr($campo1, 5);

        // 2. Segundo Grupo - composto pelas 3 últimas posiçoes do PSK e 7 primeiros dígitos do Nosso Número
        // e DV (modulo10) deste campo
        $campo2 = substr($codigo, 24, 10);
        $campo2 = $campo2 . $this->modulo10($campo2);
        $campo2 = substr($campo2, 0, 5).'.'.substr($campo2, 5);

        // 3. Terceiro Grupo - Composto por : Restante do Nosso Numero (6 digitos), IOS, Modalidade da Carteira
        // e DV (modulo10) deste campo
        $campo3 = substr($codigo, 34, 10);
        $campo3 = $campo3 . $this->modulo10($campo3);
        $campo3 = substr($campo3, 0, 5).'.'.substr($campo3, 5);

        // 4. Campo - digito verificador do codigo de barras
        $campo4 = substr($codigo, 4, 1);

        // 5. Campo composto pelo fator vencimento e valor nominal do documento, sem
        // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
        // tratar de valor zerado, a representacao deve ser 0000000000 (dez zeros).
        $campo5 = substr($codigo, 5, 4) . substr($codigo, 9, 10);

        return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }

    public function digitoVerificadorBarra($numero)
    {
        $resto2 = $this->modulo11($numero, 9, 1);

        if ($resto2 == 0 || $resto2 == 1 || $resto2 == 10) {
            $dv = 1;
        } else {
            $dv = 11 - $resto2;
        }

        return $dv;
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