NovoBoletoPHP
=======

[![Build Status](https://secure.travis-ci.org/jefersondaniel/novoboletophp.png?branch=master)](http://travis-ci.org/jefersondaniel/novoboletophp)
[![Latest Stable Version](https://poser.pugx.org/jefersondaniel/novoboletophp/v/stable.svg)](https://packagist.org/packages/jefersondaniel/novoboletophp)
[![Latest Unstable Version](https://poser.pugx.org/jefersondaniel/novoboletophp/v/unstable.svg)](https://packagist.org/packages/jefersondaniel/novoboletophp)

Projeto baseado no boletophp que visa manter os boletos atualizados e ser mais fácil de contribuir.


## Bancos suportados


| Banco           | Carteira                | Testado      |
|-----------------|-------------------------|--------------|
| Banco do Brasil | Carteira 18 Variação 27 | Sim          |
| Itaú            |                         | Sim          |
| Santander       |                         | Sim          |

Como os layouts serão baseados no boletophp, deixamos crédito aos desenvolvedores listados no síte http://boletophp.com.br/ 


## Instalação
### Composer
Se você já conhece o **Composer**, adicione a dependência abaixo à diretiva *"require"* no seu **composer.json**:
```
"jefersondaniel/novoboletophp": "0.5.*"
```

## Como Usar
```php
use \NovoBoletoPHP\BoletoFactory;

$factory = new BoletoFactory;

$data = array(
    'nosso_numero' => '7002',
    'inicio_nosso_numero' => '7000',
    'numero_documento' => '7002',
    'data_vencimento' => '02/03/2015',
    'data_documento' => '22/02/2015',
    'data_processamento' => '22/02/2015',
    'valor_boleto' => '10,00',
    'convenio' => '1234567',
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
    'agencia' => '4321', // Num da agencia, sem digito
    'conta' => '12345', // Num da conta, sem digito
    'conta_dv' => '9',
    'identificacao' => 'Empresa de Teste',
    'cpf_cnpj' => '15.262.543/0001-54',
    'endereco' => 'Rua da Empresa, 18, Vila Teste',
    'cidade_uf' => 'São Paulo - SP',
    'cedente' => 'Empresa de Teste LTDA',
    'logo_empresa' => 'http://placehold.it/200&text=logo',
);

echo $factory->makeBoletoAsHTML(BoletoFactory::BANCO_DO_BRASIL, $data);
```

## Licença
Este projeto esta sobre a licença LGPL
