<?php

namespace NovoBoletoPHP\Api\Soap;

class Boleto
{
    /**
     * @type string
     */
    public $nosso_numero;

    /**
     * @type string
     */
    public $inicio_nosso_numero;

    /**
     * @type string
     */
    public $numero_documento;

    /**
     * @type string
     */
    public $data_vencimento;

    /**
     * @type string
     */
    public $data_documento;

    /**
     * @type string
     */
    public $data_processamento;

    /**
     * @type string
     */
    public $valor_boleto;

    /**
     * @type string
     */
    public $convenio;

    /**
     * @type string
     */
    public $contrato;

    /**
     * @type string
     */
    public $carteira;

    /**
     * @type string
     */
    public $variacao_carteira;

    /**
     * @type string
     */
    public $especie_doc;

    /**
     * @type string
     */
    public $sacado;

    /**
     * @type string
     */
    public $sacado_documento;

    /**
     * @type string
     */
    public $endereco1;

    /**
     * @type string
     */
    public $endereco2;

    /**
     * @type string
     */
    public $demonstrativo1;

    /**
     * @type string
     */
    public $instrucoes1;

    /**
     * @type string
     */
    public $aceite = 'N';

    /**
     * @type string
     */
    public $especie = 'R$';

    /**
     * @type string
     */
    public $agencia;

    /**
     * @type string
     */
    public $conta;

    /**
     * @type string
     */
    public $conta_dv;

    /**
     * @type string
     */
    public $identificacao;

    /**
     * @type string
     */
    public $cpf_cnpj;

    /**
     * @type string
     */
    public $endereco;

    /**
     * @type string
     */
    public $cidade_uf;

    /**
     * @type string
     */
    public $cedente;

    /**
     * @type string
     */
    public $logo_empresa;

    public function __construct($data)
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toArray()
    {
        return array(
            'nosso_numero' => $this->nosso_numero,
            'inicio_nosso_numero' => $this->inicio_nosso_numero,
            'numero_documento' => $this->numero_documento,
            'data_vencimento' => $this->data_vencimento,
            'data_documento' => $this->data_documento,
            'data_processamento' => $this->data_processamento,
            'valor_boleto' => $this->valor_boleto,
            'convenio' => $this->convenio,
            'contrato' => $this->contrato,
            'carteira' => $this->carteira,
            'variacao_carteira' => $this->variacao_carteira,
            'especie_doc' => $this->especie_doc,

            'sacado' => $this->sacado,
            'sacado_documento' => $this->sacado_documento,
            'endereco1' => $this->endereco1,
            'endereco2' => $this->endereco2,
            'demonstrativo1' => $this->demonstrativo1,
            'instrucoes1' => $this->instrucoes1,
            'aceite' => $this->aceite,
            'especie' => $this->especie,
            'agencia' => $this->agencia, // Num da agencia, sem digito
            'conta' => $this->conta, // Num da conta, sem digito
            'conta_dv' => $this->conta_dv,
            'identificacao' => $this->identificacao,
            'cpf_cnpj' => $this->cpf_cnpj,
            'endereco' => $this->endereco,
            'cidade_uf' => $this->cidade_uf,
            'cedente' => $this->cedente,
            'logo_empresa' => $this->logo_empresa,
        );
    }

}