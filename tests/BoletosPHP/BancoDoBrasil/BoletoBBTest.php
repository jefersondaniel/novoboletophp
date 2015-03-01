<?php
namespace NovoBoletoPHP\Tests\BancoDoBrasil;

use \NovoBoletoPHP\BancoDoBrasil\BoletoBB;
use \NovoBoletoPHP\BoletoFactory;


class BoletoBBTest extends \PHPUnit_Framework_TestCase {
    public function getData()
    {
        return array(
            'nosso_numero' => '7002',
            'inicio_nosso_numero' => '7000',
            'numero_documento' => '7002',
            'data_vencimento' => '02/03/2015',
            'data_documento' => '22/02/2015',
            'data_processamento' => '22/02/2015',
            'valor_boleto' => '10,00',
            //'convenio' => '1208625', // Definido no método makeBoleto
            'contrato' => '',
            'carteira' => '18',
            'variacao_carteira' => '27',

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

    public function makeBoleto($convenio = 7)
    {
        $factory = new BoletoFactory();

        $convenios = array(
            7 => '1208625'
        );

        $data = $this->getData();
        $data['convenio'] = $convenios[$convenio];

        return $factory->makeBoleto(BoletoFactory::BANCO_DO_BRASIL, $data);
    }

    public function testCodigoDeBarrasTemCodigoDoBanco()
    {
        $boleto = $this->makeBoleto();
        $this->assertEquals('001', substr($boleto->data['codigo_barras'], 0, 3));
    }

    public function testCodigoDeBarrasTemMoeda()
    {
        $boleto = $this->makeBoleto();
        $this->assertEquals('9', substr($boleto->data['codigo_barras'], 3, 1));
    }

    public function testFatorDeVencimentoCalculaDiasEntreVencimentoEBase()
    {
        $boleto = $this->makeBoleto();
        $base = \DateTime::createFromFormat('d/m/Y', '07/10/1997');
        $vencimento = \DateTime::createFromFormat(
            'd/m/Y',
            $boleto->data['data_vencimento']
        );
        $diff = $base->diff($vencimento);

        $this->assertEquals($diff->days, substr($boleto->data['codigo_barras'], 5, 4));
    }

    public function testCodigoDeBarrasTemValor()
    {
        $boleto = $this->makeBoleto();
        $valor = $this->getData()['valor_boleto'];
        $valor = str_replace(',', '.', $valor);
        $valor = number_format($valor, 2, '', '');
        $this->assertEquals($valor, substr($boleto->data['codigo_barras'], 9, 10));
    }

    public function testCodigoDeBarrasTemZerosParaConvenioDe7Digitos()
    {
        $boleto = $this->makeBoleto(7);
        $this->assertEquals(str_repeat('0', '6'), substr($boleto->data['codigo_barras'], 19, 6));
    }

    public function testCodigoDeBarrasFormataConvenioParaConvenioDe7Digitos()
    {
        $boleto = $this->makeBoleto(7);
        $convenio = $boleto->data['convenio'];
        $convenio = sprintf('%07d', $convenio);
        $this->assertEquals($convenio, substr($boleto->data['codigo_barras'], 25, 7));
    }

    public function testCodigoDeBarrasFormataNossoNumeroParaConvenioDe7Digitos()
    {
        $boleto = $this->makeBoleto(7);
        $nossoNumero = $this->getData()['nosso_numero'];
        $nossoNumero = sprintf('%010d', $nossoNumero);
        $this->assertEquals($nossoNumero, substr($boleto->data['codigo_barras'], 32, 10));
    }

    public function testCodigoDeBarrasTemCarteira()
    {
        $boleto = $this->makeBoleto();
        $carteira = $boleto->data['carteira'];
        $this->assertEquals($carteira, substr($boleto->data['codigo_barras'], 42, 2));
    }

    public function testLinhaDigitavelTemCodigoDoBanco()
    {
        $boleto = $this->makeBoleto();
        $this->assertEquals('001', substr($boleto->data['linha_digitavel'], 0, 3));
    }

    public function testLinhaDigitavelTemCodigoMoeda()
    {
        $boleto = $this->makeBoleto();
        $this->assertEquals('9', substr($boleto->data['linha_digitavel'], 3, 1));
    }

    public function testLinhaDigitavelTemPosicao20A24DoCodigoDeBarras()
    {
        $boleto = $this->makeBoleto();
        $parteCodigoDeBarras = substr($boleto->data['codigo_barras'], 19, 5);
        $parteCodigoDeBarras = preg_replace('#([0-9]{1})([0-9]{4})#', '$1.$2', $parteCodigoDeBarras);
        $this->assertSame($parteCodigoDeBarras, substr($boleto->data['linha_digitavel'], 4, 6));
    }

    public function testLinhaDigitavelTemDVDoCampo1()
    {
        $boleto = $this->makeBoleto();
        $campo1 = explode(' ', $boleto->data['linha_digitavel'])[0];
        $digitos = str_replace('.', '', substr($campo1, 0, 10));
        $dv = $boleto->modulo10($digitos);
        $this->assertEquals($dv, substr($campo1, 10, 1));
    }

    public function testLinhaDigitavelTemPosicao25A34DoCodigoDeBarras()
    {
        $boleto = $this->makeBoleto();
        $parteCodigoDeBarras = substr($boleto->data['codigo_barras'], 24, 10);
        $parteCodigoDeBarras = preg_replace('#([0-9]{5})([0-9]{5})#', '$1.$2', $parteCodigoDeBarras);
        $campo2 = explode(' ', $boleto->data['linha_digitavel'])[1];
        $this->assertSame($parteCodigoDeBarras, substr($campo2, 0, 11));
    }

    public function testLinhaDigitavelTemDVDoCampo2()
    {
        $boleto = $this->makeBoleto();
        $campo2 = explode(' ', $boleto->data['linha_digitavel'])[1];
        $digitos = str_replace('.', '', substr($campo2, 0, 11));
        $dv = '' . $boleto->modulo10($digitos);
        $this->assertSame($dv, substr($campo2, 11, 1));
    }

    public function testLinhaDigitavelTemPosicao35A44DoCodigoDeBarras()
    {
        $boleto = $this->makeBoleto();
        $parteCodigoDeBarras = substr($boleto->data['codigo_barras'], 34, 10);
        $parteCodigoDeBarras = preg_replace('#([0-9]{5})([0-9]{5})#', '$1.$2', $parteCodigoDeBarras);
        $campo3 = explode(' ', $boleto->data['linha_digitavel'])[2];
        $this->assertSame($parteCodigoDeBarras, substr($campo3, 0, 11));
    }

    public function testLinhaDigitavelTemDVDoCampo3()
    {
        $boleto = $this->makeBoleto();
        $campo3 = explode(' ', $boleto->data['linha_digitavel'])[2];
        $digitos = str_replace('.', '', substr($campo3, 0, 11));
        $dv = '' . $boleto->modulo10($digitos);
        $this->assertSame($dv, substr($campo3, 11, 1));
    }

    public function testLinhaDigitavelTemDVDoCodigoDeBarras()
    {
        $boleto = $this->makeBoleto();
        $campo4 = explode(' ', $boleto->data['linha_digitavel'])[3];

        $dv = $boleto->data['codigo_barras'][4];
        $this->assertSame($dv, $campo4);
    }

    public function testLinhaDigitavelTemFatorDeVencimento()
    {
        $boleto = $this->makeBoleto();
        $campo5 = explode(' ', $boleto->data['linha_digitavel'])[4];
        $this->assertEquals(
            $boleto->fatorVencimento($boleto->data['data_vencimento']),
            substr($campo5, 0, 4)
        );
    }

    public function testLinhaDigitavelTemValorDoTitulo()
    {
        $boleto = $this->makeBoleto();
        $campo5 = explode(' ', $boleto->data['linha_digitavel'])[4];
        $valor = $this->getData()['valor_boleto'];
        $valor = str_replace(',', '.', $valor);
        $valor = number_format($valor, 2, '', '');
        $valor = sprintf('%010d', $valor);
        $this->assertEquals(
            $valor,
            substr($campo5, 4, 10)
        );
    }
}