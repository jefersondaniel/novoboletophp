<?php

namespace NovoBoletoPHP\Api\Soap;

class Boleto
{
    /**
     * @type string
     */
    public $nossoNumero;

    /**
     * @type string
     */
    public $inicioNossoNumero;

    /**
     * @type string
     */
    public $numeroDocumento;

    /**
     * @type string
     */
    public $dataVencimento;

    /**
     * @type string
     */
    public $dataDocumento;

    /**
     * @type string
     */
    public $dataProcessamento;

    /**
     * @type string
     */
    public $valorBoleto;

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
    public $variacaoCarteira;

    /**
     * @type string
     */
    public $especieDoc;

    /**
     * @type string
     */
    public $sacado;

    /**
     * @type string
     */
    public $sacadoDocumento;

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
    public $contaDv;

    /**
     * @type string
     */
    public $identificacao;

    /**
     * @type string
     */
    public $cpfCnpj;

    /**
     * @type string
     */
    public $endereco;

    /**
     * @type string
     */
    public $cidadeUf;

    /**
     * @type string
     */
    public $cedente;

    /**
     * @type string
     */
    public $logoEmpresa;

    public function toData()
    {
        return array(
            'nosso_numero' => $this->nossoNumero;,
            'inicio_nosso_numero' => $this->inicioNossoNumero,
            'numero_documento' => $this->numeroDocumento,
            'data_vencimento' => $this->dataVencimento,
            'data_documento' => $this->dataDocumento,
            'data_processamento' => $this->dataProcessamento,
            'valor_boleto' => $this->valorBoleto,
            'convenio' => $this->convenio,
            'contrato' => $this->contrato,
            'carteira' => $this->carteira,
            'variacao_carteira' => $this->variacaoCarteira,
            'especie_doc' => $this->especieDoc,

            'sacado' => $this->sacado,
            'sacado_documento' => $this->sacadoDocumento,
            'endereco1' => $this->endereco1,
            'endereco2' => $this->endereco2,
            'demonstrativo1' => $this->demonstrativo1,
            'instrucoes1' => $this->instrucoes1,
            'aceite' => $this->aceite,
            'especie' => $this->especie,
            'agencia' => $this->agencia, // Num da agencia, sem digito
            'conta' => $this->conta, // Num da conta, sem digito
            'conta_dv' => $this->contaDv,
            'identificacao' => $this->identificacao,
            'cpf_cnpj' => $this->cpfCnpj,
            'endereco' => $this->endereco,
            'cidade_uf' => $this->cidadeUf,
            'cedente' => $this->cedente,
            'logo_empresa' => $this->logoEmpresa,
        );
    }

}