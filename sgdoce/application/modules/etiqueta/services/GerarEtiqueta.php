<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Etiqueta\Service;

/**
 * Classe para Service de ModeloMinuta
 *
 * @package  Minuta
 * @category Service
 * @name     ModeloMinuta
 * @version  1.0.0
 */
class GerarEtiqueta extends \Core_ServiceLayer_Service_Crud
{

    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:LoteEtiqueta', $_entity;

    /**
     * Método que popula os objetos para serem salvos no banco
     * @return void
     */
    public function setOperationalEntity ($entityName = NULL)
    {
        $this->_data['sqQuantidadeEtiqueta'] = $this->_createEntityManaged(
                array('sqQuantidadeEtiqueta' => $this->_data['sqQuantidadeEtiqueta']), 'app:QuantidadeEtiqueta'
        );
        $this->_data['sqTipoEtiqueta'] = $this->_createEntityManaged(
                array('sqTipoEtiqueta' => $this->_data['sqTipoEtiqueta']), 'app:TipoEtiqueta'
        );
        $this->_data['sqUnidadeOrg'] = $this->_createEntityManaged(
                array('sqUnidadeOrg' => $this->_data['sqUnidadeOrg']), 'app:VwUnidadeOrg'
        );
    }

    public function listEtiquetaImprimir (\Core_Dto_Search $objDTOSearch)
    {
        return $this->_getRepository()->listEtiquetaImprimir($objDTOSearch);
    }

    public function listEtiquetaPorNumero (\Core_Dto_Search $objDTOSearch, $withNUP = FALSE)
    {
        $arrDigitais = $this->_getRepository()->listEtiquetasPorNumero($objDTOSearch);
        $arrDigitaisAC = array();
        $filter = new \Core_Filter_MaskNumber(array('mask' => '9999999.99999999/9999-99'));

        foreach ($arrDigitais as $key => $arrDigital) {

            $etiqueta = (!$arrDigital['nuNupSiorg']) ?
                    "{$arrDigital['nuEtiqueta']}" :
                    (!$withNUP) ? "{$arrDigital['nuEtiqueta']}" :
                            "{$arrDigital['nuEtiqueta']} - {$filter->filter($arrDigital['nuNupSiorg'])}";

            $arrDigitaisAC[$arrDigital['nuEtiqueta']] = $etiqueta;
        }
        return $arrDigitaisAC;
    }

    public function isValidNuEtiqueta ($mixNuEtiquetas)
    {
        $rsNuEtiquetas = $this->_getRepository('app:EtiquetasUso')->findBy(array(
            'nuEtiqueta' => $mixNuEtiquetas
        ));

        if (count($rsNuEtiquetas) == count($mixNuEtiquetas)) {
            return TRUE;
        }

        return FALSE;
    }

    public function preSave ($service)
    {
        try {
            if (!$this->_data['sqQuantidadeEtiqueta']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Quantidade de Páginas é de preenchimento obrigatório.');
            }
            if (!$this->_data['sqTipoEtiqueta']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Tipo de Etiqueta é de preenchimento obrigatório.');
            }
            if (!$this->_data['sqUnidadeOrg']) {
                throw new \Core_Exception_ServiceLayer_Verification('O campo Unidade é de preenchimento obrigatório.');
            }

            $params             = $this->_data;
            $nuAno              = date('Y');
            $params['nuAno']    = $nuAno;
            $nuInicialNupSiorg  = NULL;
            $nuFinalNupSiorg    = NULL;
            $params['inLoteComNupSiorg'] = isset($params['inLoteComNupSiorg']) ? (boolean) $params['inLoteComNupSiorg'] : TRUE;

            $sqTipoEtiqueta = (integer)$this->_data['sqTipoEtiqueta']->getSqTipoEtiqueta();

            //para lote eletronico força o uso de NUP
            if ($sqTipoEtiqueta == \Core_Configuration::getSgdoceTipoEtiquetaEletronica()) {
                $params['inLoteComNupSiorg'] = TRUE;
            }

            //se for com nup validar se a unidade pode gerar etiqueta
            if ($params['inLoteComNupSiorg']) {
                $entityOrgao = $this->_getRepository('app:VwOrgao')
                                    ->findOneBySqUnidadeOrg($params['sqUnidadeOrg']->getSqUnidadeOrg());
                if (!$entityOrgao) {
                    throw new \Core_Exception_ServiceLayer_Verification('Essa unidade não possui registro no SIORG para geração de etiqueta com NUP');
                }
            }

            //RN #11705
            $this->_checkLiberacaoLoteUnidade($params);

            /* @var $service Etiqueta\Service\GerarEtiqueta */
            $sqQuantidadeEtiqueta = $service->getEntity()->getSqQuantidadeEtiqueta()->getSqQuantidadeEtiqueta();
            $search = \Core_Dto::factoryFromData(array('nuAno' => $nuAno), 'search');

            //ultimo lote do ano
            $ultimoLoteUnidade = $this->_getRepository()->getUltimoLotePessoaUnidadeOrg($search);

            /* @var $service \Sgdoce\Model\Entity\QuantidadeEtiqueta */
            $eQuantidadeEtiqueta = $this->_getRepository('app:QuantidadeEtiqueta')->find($sqQuantidadeEtiqueta);

            $qtdeEtiquetaGerar = $eQuantidadeEtiqueta->getNuQuantidade();

            if (count($ultimoLoteUnidade) === 0) { //1º Lote do ano
                if ($params['inLoteComNupSiorg']) {
                    $nuInicialNupSiorg = 1;
                    $nuFinalNupSiorg = $qtdeEtiquetaGerar;
                }
                $nuInicial = 1;
                $nuFinal = $qtdeEtiquetaGerar;
            } else {

                $nuInicial = $ultimoLoteUnidade[0]['nuFinal'] + 1;
                $nuFinal = $ultimoLoteUnidade[0]['nuFinal'] + $qtdeEtiquetaGerar;

                /**
                 * se for lote com nup e o ultimo lote não tiver sido com nup
                 * obter o ultimo lote com nup para verificar qual é o proximo range
                 */
                if ($params['inLoteComNupSiorg']) {

                    $dtoUltimoLoteComNup = \Core_Dto::factoryFromData(array(
                                'inLoteComNupSiorg' => TRUE,
                                'sqUnidadeOrg' => $service->getEntity()->getSqUnidadeOrg()->getSqUnidadeOrg(),
                                'nuAno' => $nuAno
                                    ), 'search');

                    $ultimoLoteNupUnidade = $this->_getRepository()->getUltimoLotePessoaUnidadeOrg($dtoUltimoLoteComNup);
                    /**
                     * se ainda não existe lote com nup começa o 1º range
                     */
                    if (count($ultimoLoteNupUnidade) === 0) { //1º Lote com NUP
                        $nuInicialNupSiorg = 1;
                        $nuFinalNupSiorg = $qtdeEtiquetaGerar;
                    } else {
                        $nuInicialNupSiorg = $ultimoLoteNupUnidade[0]['nuFinalNupSiorg'] + 1;
                        $nuFinalNupSiorg = $ultimoLoteNupUnidade[0]['nuFinalNupSiorg'] + $qtdeEtiquetaGerar;
                    }
                } else {
                    $nuInicialNupSiorg = $ultimoLoteUnidade[0]['nuFinalNupSiorg'] + 1;
                    $nuFinalNupSiorg = $ultimoLoteUnidade[0]['nuFinalNupSiorg'] + $qtdeEtiquetaGerar;
                }
            }

            $service->getEntity()->setNuAno($nuAno);
            $service->getEntity()->setNuInicial($nuInicial);
            $service->getEntity()->setNuFinal($nuFinal);

            $service->getEntity()->setNuInicialNupSiorg($nuInicialNupSiorg);
            $service->getEntity()->setNuFinalNupSiorg($nuFinalNupSiorg);
            $service->getEntity()->setInLoteComNupSiorg($params['inLoteComNupSiorg']);
            $service->getEntity()->setDtCriacao(\Zend_Date::now());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function postInsert ($service)
    {
        //perisite o lote
        $this->getEntityManager()->flush();

        /**
         * abre outra tranzação para o insert as etiquetas.
         */
        $this->getEntityManager()->getConnection()->beginTransaction();
        try {

            $coSiorg = NULL;
            if ($service->getEntity()->getInLoteComNupSiorg()) {
                $entityOrgao = $this->_getRepository('app:VwOrgao')
                                    ->findOneBySqUnidadeOrg($service->getEntity()->getSqUnidadeOrg()->getSqUnidadeOrg());
                if ($entityOrgao) {
                    $coSiorg = $entityOrgao->getCoSiorg();
                }
            }

            $dtoSearch = \Core_Dto::factoryFromData(array(
                    'sqLoteEtiqueta' => $service->getEntity()->getSqLoteEtiqueta(),
                    'coSiorg'        => $coSiorg
                ),'search');

            $result = $this->_getRepository()->listSeries($dtoSearch);

            foreach ($result as $value) {
                $entityEtiquetaNupSiorg = $this->_newEntity('app:EtiquetaNupSiorg');
                $entityEtiquetaNupSiorg->setSqLoteEtiqueta($service->getEntity());
                $entityEtiquetaNupSiorg->setNuEtiqueta($value['nuEtiqueta']);

                $nuNupSiorg = (!$value['nuNupSiorgSemDv']) ? NULL : $this->_calculaDVNupSiorg($value['nuNupSiorgSemDv']);
                $entityEtiquetaNupSiorg->setNuNupSiorg($nuNupSiorg);
                $this->getEntityManager()->persist($entityEtiquetaNupSiorg);
            }

            $this->getEntityManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getEntityManager()->getConnection()->rollback();

            //remove o lote inserido
            $this->getEntityManager()->remove($service->getEntity());
            $this->getEntityManager()->flush();

            throw $e;
        }
    }

    /**
     * @param string $nuNupSemDV numero do processo sem DV "19 caracteres"
     * @return string
     */
    private function _calculaDVNupSiorg ($nuNupSemDV)
    {

        $numeroNupSemDV = $nuNupSemDV;

        if (strlen($nuNupSemDV) != 19) {
            throw new \Core_Exception_ServiceLayer(
            'Ocorreu um erro na formação do numero do NUP.'
            . ' Impossível calcular o digito verificador');
        }
        /**
         * O cálculo dos dígitos verificadores do Número Único de Protocolo - NUP deve ser efetuado pela aplicação do
         * algoritmo Módulo 97 Base 10, conforme Norma ISO 7064:2003, de acordo com a seguinte fórmula:
         *
         * D1D0 = 98 - (O6O5O4O3O2O1O0S7S6S5S4S3S2S1S0A3A2A1A0 módulo 97)
         *
         * Exemplos:

         De posse de uma calculadora científica ou a partir do acesso a um aplicativo de calculadora científica, deve-se realizar os seguintes passos para o cálculo dos dígitos verificadores (DV) de um NUP:
         1. multiplicar o número-base (19 primeiros dígitos) do NUP por 100 (cem),
         2. pressionar o botão “Mod”,
         3. digitar o número “97” e pressionar o botão “=”,
         4. subtrair o resultado do item anterior (3) do número “98”,
         5. o resultado encontrado no item anterior (4) será o DV do número-base informado, que irá compor o NUP.
         A seguir, é apresentado o exemplo I contemplado na Portaria Interministerial MJ/MP n° 2.321, de 30 de dezembro de 2014:
         digitar “000806010000176201500”, que é o número-base do NUP cujo DV é necessário identificar (“0008060100001762015”) multiplicado por 100,
         pressionar o botão “Mod”,
         digitar o número “97” e pressionar o botão “=”, obtendo como resultado “37”,
         98 – 37 = 61,
         os dígitos verificadores são “6” e “1” e o NUP será “0008060.10000176/2015-61”.
         *
         */

        $resto = bcmod(bcmul($nuNupSemDV, 100),97);
        $numeroNupComDV = $numeroNupSemDV . str_pad(98 - $resto, 2, '0', STR_PAD_LEFT);

        return $numeroNupComDV;
    }

    /**
     *
     * @param array $params
     * @return \Etiqueta\Service\GerarEtiqueta
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    private function _checkLiberacaoLoteUnidade (array $params)
    {
        $search = \Core_Dto::factoryFromData($params, 'search');
        //recupera o ultimo lote da unidade e do tipo de etiqueta selecionados
        $ultimoLoteUnidade = $this->_getRepository()->getUltimoLotePessoaUnidadeOrg($search);

        //RN #11705 / MN #12165
        if (count($ultimoLoteUnidade) === 1 && $ultimoLoteUnidade[0]['nuQuantidadeDisponivel'] !== 0) {
            $msg = sprintf(
                    \Core_Registry::getMessage()->translate('MN145'),
                    $ultimoLoteUnidade[0]['noTipoEtiqueta'],
                    $ultimoLoteUnidade[0]['noUnidadeOrg']
                );
            throw new \Core_Exception_ServiceLayer_Verification($msg);
        }

        return $this;
    }

}
