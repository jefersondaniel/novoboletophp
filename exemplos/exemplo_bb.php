<?php
require_once '../vendor/autoload.php';

use \NovoBoletoPHP\BoletoFactory;

$factory = new BoletoFactory(array(
    'cachePath' => false, // Em produção, sempre definir uma pasta para os caches do Twig.
));

$dados = array(
    'nosso_numero' => '7002',
    'inicio_nosso_numero' => '7000',
    'numero_documento' => '7002',
    'data_vencimento' => '02/03/2015',
    'data_documento' => '22/02/2015',
    'data_processamento' => '22/02/2015',
    'valor_boleto' => '10,00',
    'convenio' => '1208625',
    'contrato' => '',
    'carteira' => '18',
    'variacao_carteira' => '27',
    'especie_doc' => 'DS',

    'sacado' => 'Jeferson Daniel',
    'sacado_documento' => '643.149.352-43',
    'endereco1' => 'Rua de Teste, 472 - Vila Teste',
    'endereco2' => 'Cidade de Teste, São Paulo - BR',
    'demonstrativo1' => 'Pagamento 1 de 5 da mensalidade',
    'instrucoes1' => 'Não receber após vencimento',
    'aceite' => 'N',
    'especie' => 'R$',
    'agencia' => '4567', // Num da agencia, sem digito
    'conta' => '12519', // Num da conta, sem digito
    'conta_dv' => '9',
    'identificacao' => 'Empresa de Teste',
    'cpf_cnpj' => '15.262.543/0001-54',
    'endereco' => 'Rua da Empresa, 18, Vila Teste',
    'cidade_uf' => 'São Paulo - SP',
    'cedente' => 'Empresa de Teste LTDA',
    'logo_empresa' => 'http://placehold.it/200&text=logo',
);

echo $factory->makeBoletoAsHTML(BoletoFactory::BANCO_DO_BRASIL, $dados);
