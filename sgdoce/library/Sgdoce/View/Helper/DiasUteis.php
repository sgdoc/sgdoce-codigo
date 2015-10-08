<?php

class Sgdoce_View_Helper_DiasUteis extends Zend_View_Helper_Abstract
{

    public function diasUteis($dataInicio, $qtdDias, $arrFeriado = array())
    {
        $feriados = $this->organizaArrFeriado($arrFeriado);

        $qtdFeriados = $this->getQtdFeriados($feriados);

        $anoInicio = $this->getAnoInicio($dataInicio);

        for ($index = 1; $index <= $qtdDias; $index++) {
            if ($index > 1) {
                if ($this->verificaDia($dataInicio, $feriados, $qtdFeriados, $anoInicio)) {
                    $qtdDias++;
                }
            }

            if(substr($dataInicio, 0, 5) == '31/12'){
                $anoInicio++;
            }

            $dataInicio = $this->soma1dia($dataInicio);
        }

        $diaSemana = date("w", $this->dataToTimestamp($dataInicio));

        # dia sendo sabado
        if ($diaSemana == 6) {
            $dataInicio = $this->soma1dia($dataInicio);
            $dataInicio = $this->soma1dia($dataInicio);
        }

        # dia sendo domingo
        if ($diaSemana == 0) {
            $dataInicio = $this->soma1dia($dataInicio);
        }

        # dia sendo feriado
        for ($i = 0; $i <= $qtdFeriados; $i++) {
            if ($dataInicio == $this->feriados($feriados, $anoInicio, $i)) {
                $dataInicio = $this->soma1dia($dataInicio);
            }
        }

        return $dataInicio;
    }

    public function getAnoInicio($dataInicio = NULL){

        $anoInicio = NULL;

        if($dataInicio){
            $data = explode('/', $dataInicio);
            $data = $data[2];
            $anoInicio = $data;
        }

        return $anoInicio;
    }

    public function organizaArrFeriado($arrFeriado)
    {
        $feriados = array();
        foreach ($arrFeriado as $data) {
            $feriados[] = $data['dtFeriado']->get('dd/MM');
        }

        return $feriados;
    }

    public function verificaDia($dataInicio, $arrFeriado, $qtdFeriados, $anoInicio)
    {
        $diaSemana = date("w", $this->dataToTimestamp($dataInicio));

        if ($diaSemana == 0 || $diaSemana == 6) {
            return TRUE;
        } else {
            for ($i = 0; $i <= $qtdFeriados; $i++) {
                if ($dataInicio == $this->feriados($arrFeriado, $anoInicio, $i)) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function feriados($arrFeriado, $ano, $posicao)
    {
        $datas = array();
        foreach ($arrFeriado as $data) {
            $datas[] = $data;
        }

        $dia = 86400;
        $pescoa = easter_date($ano);
        $sexta_santa = $pescoa - (2 * $dia);
        $carnaval = $pescoa - (47 * $dia);
        $corpus_cristi = $pescoa + (60 * $dia);

        $datas[] = date('d/m', $carnaval); // carnaval
        $datas[] = date('d/m', $sexta_santa); //sexta_santa
        $datas[] = date('d/m', $corpus_cristi); //corpus_cristi

        return $datas[$posicao] . "/" . $ano;
    }

    public function getQtdFeriados($arrFeriado)
    {
        return count($arrFeriado) + 2;
    }

    public function dataToTimestamp($data)
    {
        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);

        return mktime(0, 0, 0, $mes, $dia, $ano);
    }

    public function soma1dia($data)
    {
        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);

        return date("d/m/Y", mktime(0, 0, 0, $mes, $dia + 1, $ano));
    }


    public static function calculaDataPrazo($dataInicial, $qtDias, $somenteUteis = FALSE, $listaFeriados = Array() ) {
        $i = 0;
        $data = new DateTime($dataInicial);
        while ($i <= $qtDias) {
            if ($somenteUteis) {
                echo 'teste';die;
                if (($dS = date("w", strtotime($dataInicial))) != "0" && $dS != "6") {
                    foreach ($listaFeriados as $feriado) {

                        if ($feriado == $data->format('Y-m-d')) {
                            break;
                        }
                    }
                    $data->add(new DateInterval('P1D'));
                }
            } else {
                $data->add(new DateInterval('P1D'));
            }
            $i++;
        }
        return $data;
    }

}
