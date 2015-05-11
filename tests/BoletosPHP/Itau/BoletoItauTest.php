<?php
namespace NovoBoletoPHP\Tests\BancoDoBrasil;

use \NovoBoletoPHP\Itau\BoletoItau;
use \NovoBoletoPHP\BoletoFactory;


class BoletoItauTest extends \PHPUnit_Framework_TestCase {
    public function getData()
    {
        return array(
            'nosso_numero' => '7002',
            'inicio_nosso_numero' => '7000',
            'numero_documento' => '7002',
            'data_vencimento' => '02/03/2015',
            'data_documento' => '22/02/2015',
            'data_processamento' => '22/02/2015',
            'valor_boleto' => '10,55',
            'carteira' => '175',
            'sacado' => 'Jeferson Daniel',
            'sacado_cpf' => '643.149.352-43',
            'endereco1' => 'Rua de Teste, 472 - Vila Teste',
            'endereco2' => 'Cidade de Teste, São Paulo - BR',
            'demonstrativo1' => 'Pagamento 1 de 5 da mensalidade',
            'instrucoes1' => 'Não receber após vencimento',
            'aceite' => 'N',
            'especie' => 'R$',
            'agencia' => '3491', // Num da agencia, sem digito
            'conta' => '12519', // Num da conta, sem digito
            'conta_dv' => '9',
            'identificacao' => 'Empresa de Teste',
            'cpf_cnpj' => '15.262.543/0001-54',
            'endereco' => 'Rua da Empresa, 18, Vila Teste',
            'cidade_uf' => 'São Paulo - SP',
            'cedente' => 'Empresa de Teste LTDA',
            'logo_empresa' => 'http://placehold.it/200&text=logo',
        );
    }

    public function makeBoleto()
    {
        $factory = new BoletoFactory();
        $data = $this->getData();
        return $factory->makeBoleto(BoletoFactory::ITAU, $data);
    }

    public function testCodigoDeBarras()
    {
        $boleto = $this->makeBoleto();

        $table = array(
            '1:3' => array(
                'value' => '341',
                'description' => 'Tem código do banco',
            ),
            '4:4' => array(
                'value' => '9',
                'description' => 'Tem código da moeda',
            ),
            '5:5' => array(
                'value' => '3',
                'description' => 'Tem dac do código de barras',
            ),
            '6:9' => array(
                'value' => '6355',
                'description' => 'Tem fator de vencimento',
            ),
            '10:19' => array(
                'value' => '0000001055',
                'description' => 'Tem valor',
            ),
            '20:22' => array(
                'value' => '175',
                'description' => 'Tem carteira',
            ),
            '23:30' => array(
                'value' => '00007002',
                'description' => 'Tem nosso número',
            ),
            '31:31' => array(
                'value' => '6',
                'description' => 'Tem DAC [Agência /Conta/Carteira/Nosso Número]',
            ),
            '32:35' => array(
                'value' => '3491',
                'description' => 'Tem agência',
            ),
            '36:40' => array(
                'value' => '12519',
                'description' => 'Tem conta',
            ),
            '41:41' => array(
                'value' => '5',
                'description' => 'Tem dac agência / conta',
            ),
            '42:44' => array(
                'value' => '000',
                'description' => 'Tem zeros',
            ),
        );

        foreach($table as $position => $information)
        {
            $aux = explode(':', $position);
            $this->assertEquals($information['value'], substr($boleto->data['codigo_barras'], 
                $aux[0] - 1, $aux[1] - $aux[0] + 1), ' [ ] '.$information['description']);
        }
    }

    public function testLinhaDigitavel()
    {
        $boleto = $this->makeBoleto();

        $table = array(
            '1:3' => array(
                'value' => '341',
                'description' => 'Tem código do banco',
            ),
            '4:4' => array(
                'value' => '9',
                'description' => 'Tem código da moeda',
            ),
            '5:8' => array(
                'value' => '1.75',
                'description' => 'Tem carteira',
            ),
            '9:10' => array(
                'value' => '00',
                'description' => 'Tem dois primeiros dígitos do nosso número',
            ),
            '11:11' => array(
                'value' => '9',
                'description' => 'Tem dac primeiro campo',
            ),
            '12:12' => array(
                'value' => ' ',
                'description' => 'Tem separação entre o 1º e 2º campo',
            ),
            '13:19' => array(
                'value' => '000700.2',
                'description' => 'Tem nosso número',
            ),
            '20:20' => array(
                'value' => '6',
                'description' => 'DAC do campo [ Agência/Conta/Carteira/ Nosso Número ]',
            ),
            '21:23' => array(
                'value' => '349',
                'description' => 'Tem 3 primeiros números da agência',
            ),
            '24:24' => array(
                'value' => '4',
                'description' => 'DAC que amarra o campo 2',
            ),
            '25:25' => array(
                'value' => ' ',
                'description' => 'Separação entre o 2º e 3º campo',
            ),
            '26:26' => array(
                'value' => '1',
                'description' => 'Restante do número que identifica a agência',
            ),
            '27:33' => array(
                'value' => '1251.95',
                'description' => 'Número da conta corrente + DAC',
            ),
            '34:36' => array(
                'value' => '000',
                'description' => 'Zeros ( Não utilizado )',
            ),
            '37:37' => array(
                'value' => '9',
                'description' => 'DAC que amarra o campo 3 ',
            ),
            '38:38' => array(
                'value' => ' ',
                'description' => 'Separação entre o 3º e 4º campo',
            ),
            '39:39' => array(
                'value' => '3',
                'description' => 'DAC do Código de Barras',
            ),
            '40:40' => array(
                'value' => ' ',
                'description' => 'Tem separação entre o 4º e 5º campo',
            ),
            '41:44' => array(
                'value' => '6355',
                'description' => 'Fator de vencimento',
            ),
            '45:54' => array(
                'value' => '0000001055',
                'description' => 'Valor do Título',
            ),
        );

        foreach($table as $position => $information)
        {
            $aux = explode(':', $position);
            $this->assertEquals($information['value'], substr($boleto->data['linha_digitavel'], 
                $aux[0] - 1, $aux[1] - $aux[0] + 1), ' [ ] '.$information['description'].' ('.$aux[0].'-'.$aux[1].')');
        }
    }
}
