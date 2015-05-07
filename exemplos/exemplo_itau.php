<?php
require_once '../vendor/autoload.php';

use \NovoBoletoPHP\BoletoFactory;


$factory = new NovoBoletoPHP\BoletoFactory(array(
    'cachePath' => false, // Em produção, sempre definir uma pasta para os caches do Twig.
));

$dados = array(
    'codigo_cedente' => '12345',
    'nosso_numero' => '108480',
    'inicio_nosso_numero' => '7000',
    'numero_documento' => '0123',
    'data_vencimento' => '26/04/2015',
    'data_documento' => '21/04/2015',
    'data_processamento' => '21/04/2015',
    'valor_boleto' => '320,00',
    'carteira' => '175',
    'especie_doc' => 'DV', // DS = Duplicata de Serviço, DM = Duplicata Mercantil, DV = Duplicata de Verso

    'sacado' => 'Jeferson Daniel',
    'sacado_documento' => '643.149.352-43',
    'endereco1' => 'Rua de Teste, 472 - Vila Teste',
    'endereco2' => 'Cidade de Teste, São Paulo - BR',
    'demonstrativo1' => 'Pagamento 1 de 5 da mensalidade',
    'instrucoes1' => '- Desconto de R$ 224,00 até 06/04/2015',
    'instrucoes2' => '- Receber até 10 dias após o vencimento',
    'instrucoes3' => '- Sr. Caixa, cobrar multa de R$ 6,40 após o vencimento e R$ 0,33 de juros diários',
    'aceite' => 'N',
    'especie' => 'R$',
    'agencia' => '123', // Num da agencia, sem digito
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
