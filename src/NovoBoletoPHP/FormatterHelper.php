<?php
namespace NovoBoletoPHP;

class FormatterHelper {
    public static function esquerda($entra, $comp)
    {
        return substr($entra, 0, $comp);
    }

    public static function direita($entra, $comp)
    {
        return substr($entra, strlen($entra)-$comp, $comp);
    }

    public static function formataNumero($numero, $loop, $insert, $tipo = "geral")
    {
        if ($tipo == "geral") {
            $numero = str_replace(",", "", $numero);

            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }

        if ($tipo == "valor") {
            $numero = str_replace(",", "", $numero);

            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }
        if ($tipo == "convenio") {
            while (strlen($numero) < $loop) {
                $numero = $numero . $insert;
            }
        }
        return $numero;
    }
}