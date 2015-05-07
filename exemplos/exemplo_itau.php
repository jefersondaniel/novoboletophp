<?php
require_once '../vendor/autoload.php';

use \NovoBoletoPHP\BoletoFactory;

$currentLocation = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

$factory = new NovoBoletoPHP\BoletoFactory(array(
    'cachePath' => false, // Em produção, sempre definir uma pasta para os caches do Twig.
    'imageUrl' => dirname($currentLocation).'/images'
));

$dados = array(
    'codigo_cedente' => '13877',
    'nosso_numero' => '/12345678',
    'inicio_nosso_numero' => '7000',
    'numero_documento' => '0123',
    'data_vencimento' => '26/04/2015',
    'data_documento' => '21/04/2015',
    'data_processamento' => '21/04/2015',
    'valor_boleto' => 2952.95,
    'carteira' => '175',
    'especie_doc' => 'DS',

    'sacado' => 'Jeferson Daniel',
    'sacado_documento' => '643.149.352-43',
    'endereco1' => 'Rua de Teste, 472 - Vila Teste',
    'endereco2' => 'Cidade de Teste, São Paulo - BR',
    'demonstrativo1' => 'Pagamento 1 de 5 da mensalidade',
    'instrucoes1' => 'Não receber após vencimento',
    'aceite' => 'N',
    'especie' => 'R$',
    'agencia' => '1565', // Num da agencia, sem digito
    'conta' => '13877', // Num da conta, sem digito
    'conta_dv' => '1',
    'identificacao' => 'Empresa de Teste',
    'cpf_cnpj' => '15.262.543/0001-54',
    'endereco' => 'Rua da Empresa, 18, Vila Teste',
    'cidade_uf' => 'São Paulo - SP',
    'cedente' => 'Empresa de Teste LTDA',
    'logo_empresa' => 'http://placehold.it/200&text=logo',
);

echo $factory->makeBoletoAsHTML(BoletoFactory::ITAU, $dados);
