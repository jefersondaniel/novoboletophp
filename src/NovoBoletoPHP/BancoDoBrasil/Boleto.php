<?php
namespace NovoBoletoPHP\BancoDoBrasil;

use \NovoBoletoPHP\Base\Boleto as BoletoBase;
use \NovoBoletoPHP\FormatterHelper;

class Boleto extends BoletoBase {
    public function getTemplate()
    {
        return 'bancos/layout_bb.html';
    }

    public function getLogoBanco()
    {
        return 'logobb.jpg';
    }

    public function getCodigoBanco()
    {
        return 1;
    }

    protected function filterData(array $dadosboleto)
    {
        $dadosboleto = parent::filterData($dadosboleto);

        $tamanhoNumeroConvenio = strlen($dadosboleto['convenio']);
        $tamanhoNossoNumero = strlen($dadosboleto['nosso_numero']);

        if ($tamanhoNumeroConvenio == 6) {
            $dadosboleto["formatacao_convenio"] = '6';
        } else if ($tamanhoNumeroConvenio == 7) {
            $dadosboleto["formatacao_convenio"] = '7';
        } else if ($tamanhoNumeroConvenio == 8) {
            $dadosboleto["formatacao_convenio"] = '8';
        }

        if ($tamanhoNossoNumero <= 5) {
            $dadosboleto['formatacao_nosso_numero'] = '1';
        } else {
            $dadosboleto['formatacao_nosso_numero'] = '2';
        }

        $codigobanco = $this->getCodigoBancoFormatado();
        $codigo_banco_com_dv = $this->getCodigoBancoComDv();
        $nummoeda = "9";
        $fator_vencimento = $this->fatorVencimento($dadosboleto["data_vencimento"]);

        //valor tem 10 digitos, sem virgula
        $valor = FormatterHelper::formataNumero($dadosboleto["valor_boleto"],10,0,"valor"); //agencia é sempre 4 digitos
        $agencia = FormatterHelper::formataNumero($dadosboleto["agencia"],4,0);
        //conta é sempre 8 digitos
        $conta = FormatterHelper::formataNumero($dadosboleto["conta"],8,0);
        //carteira 18
        $carteira = $dadosboleto["carteira"];
        //agencia e conta
        $agencia_codigo = $agencia."-". $this->modulo11($agencia) ." / ". $conta ."-". $this->modulo11($conta);
        //Zeros: usado quando convenio de 7 digitos
        $livre_zeros='000000';

        // Carteira 18 com Convênio de 8 dígitos
        if ($dadosboleto["formatacao_convenio"] == "8") {
            $convenio = FormatterHelper::formataNumero($dadosboleto["convenio"], 8, 0, "convenio");
            // Nosso número de até 9 dígitos
            $nossonumero = FormatterHelper::formataNumero($dadosboleto["nosso_numero"],9,0);
            $dv=$this->modulo11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
            $linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
            //montando o nosso numero que aparecerá no boleto
            $nossonumero = $convenio . $nossonumero ."-". $this->modulo11($convenio.$nossonumero);
        }

        // Carteira 18 com Convênio de 7 dígitos
        if ($dadosboleto["formatacao_convenio"] == "7") {
            $convenio = FormatterHelper::formataNumero($dadosboleto["convenio"],7,0,"convenio");
            //var_dump($convenio);die;
            // Nosso número de até 10 dígitos
            $nossonumero = FormatterHelper::formataNumero($dadosboleto["nosso_numero"],10,0);
            $dv=$this->modulo11("$codigobanco$nummoeda$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira");
            $linha="$codigobanco$nummoeda$dv$fator_vencimento$valor$livre_zeros$convenio$nossonumero$carteira";
            $nossonumero = $convenio.$nossonumero;
            //Não existe DV na composição do nosso-número para convênios de sete posições
        }

        // Carteira 18 com Convênio de 6 dígitos
        if ($dadosboleto["formatacao_convenio"] == "6") {
            $convenio = FormatterHelper::formataNumero($dadosboleto["convenio"],6,0,"convenio");
            
            if ($dadosboleto["formatacao_nosso_numero"] == "1") {
                
                // Nosso número de até 5 dígitos
                $nossonumero = FormatterHelper::formataNumero($dadosboleto["nosso_numero"],5,0);
                $dv = $this->modulo11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira");
                $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$agencia$conta$carteira";
                //montando o nosso numero que aparecerá no boleto
                $nossonumero = $convenio . $nossonumero ."-". $this->modulo11($convenio.$nossonumero);
            }
            
            if ($dadosboleto["formatacao_nosso_numero"] == "2") {
                
                // Nosso número de até 17 dígitos
                $nservico = "21";
                $nossonumero = FormatterHelper::formataNumero($dadosboleto["nosso_numero"],17,0);
                $dv = $this->modulo11("$codigobanco$nummoeda$fator_vencimento$valor$convenio$nossonumero$nservico");
                $linha = "$codigobanco$nummoeda$dv$fator_vencimento$valor$convenio$nossonumero$nservico";
            }
        }

        $dadosboleto["codigo_barras"] = $linha;
        $dadosboleto["linha_digitavel"] = $this->montaLinhaDigitavel($linha);
        $dadosboleto["agencia_codigo"] = $agencia_codigo;
        $dadosboleto["nosso_numero"] = $nossonumero;
        $dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

        return $dadosboleto;
    }

    protected function montaLinhaDigitavel($linha)
    {
        // Posição  Conteúdo
        // 1 a 3    Número do banco
        // 4        Código da Moeda - 9 para Real
        // 5        Digito verificador do Código de Barras
        // 6 a 19   Valor (12 inteiros e 2 decimais)
        // 20 a 44  Campo Livre definido por cada banco

        // 1. Campo - composto pelo código do banco, código da moéda, as cinco primeiras posições
        // do campo livre e DV (modulo10) deste campo
        $p1 = substr($linha, 0, 4);
        $p2 = substr($linha, 19, 5);
        $p3 = $this->modulo10("$p1$p2");
        $p4 = "$p1$p2$p3";
        $p5 = substr($p4, 0, 5);
        $p6 = substr($p4, 5);
        $campo1 = "$p5.$p6";

        // 2. Campo - composto pelas posiçoes 6 a 15 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($linha, 24, 10);
        $p2 = $this->modulo10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo2 = "$p4.$p5";

        // 3. Campo composto pelas posicoes 16 a 25 do campo livre
        // e livre e DV (modulo10) deste campo
        $p1 = substr($linha, 34, 10);
        $p2 = $this->modulo10($p1);
        $p3 = "$p1$p2";
        $p4 = substr($p3, 0, 5);
        $p5 = substr($p3, 5);
        $campo3 = "$p4.$p5";

        // 4. Campo - digito verificador do codigo de barras
        $campo4 = substr($linha, 4, 1);

        // 5. Campo composto pelo valor nominal pelo valor nominal do documento, sem
        // indicacao de zeros a esquerda e sem edicao (sem ponto e virgula). Quando se
        // tratar de valor zerado, a representacao deve ser 000 (tres zeros).
        $campo5 = substr($linha, 5, 14);

        return "$campo1 $campo2 $campo3 $campo4 $campo5";
    }

    public function modulo11($num, $base = 9, $r = 0)
    {
        $soma = 0;
        $fator = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num, $i-1, 1);
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
}