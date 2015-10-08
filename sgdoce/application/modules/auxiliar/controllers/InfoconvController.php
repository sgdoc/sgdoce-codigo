<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

/**
 * SISICMBio
 *
 * Classe Controller Index
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         Infoconv
 * @version      1.0.0
 * @since        2015-08-11
 */
class Auxiliar_InfoconvController extends \Core_Controller_Action_CrudDto
{

    /**
     * Inicializa operacoes iniciais
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Realiza consulta sobre serviço infoconv
     */
    public function serviceInfoconvAction()
    {
        $criteria = array();
        try {
            if ($this->_getParam('nuCnpj')) {
                $cnpj   = Zend_Filter::filterStatic($this->_getParam('nuCnpj'), 'Digits');
                $result = $this->getServiceLocator()
                        ->getService('VwPessoa')
                        ->getDataInfoconvBy( $cnpj, 'cnpj' );

                $result = \Core_Integration_Abstract_Soap::xmlToArray($result);

                $criteria['code'] = $result['errocode'];

                if ('failure' == $result['status']) {
                    throw new \Exception($result['response']);
                }

                $criteria['noPessoa']              = str_replace("&amp;", "&", $result['response']['nome_empresarial']);
                $criteria['noFantasia']            = $result['response']['nome_fantasia'] ? str_replace("&amp;", "&", $result['response']['nome_fantasia']) : '';
                $natJurInfoconv                    = $result['response']['natureza_juridica'];
                $naturezaJuridicaPai               = trim(substr($natJurInfoconv, 0, 1) . '00');
                $naturezaJuridica                  = trim(substr($natJurInfoconv, 0, 3));
                $criteria['sqNaturezaJuridicaPai'] = ($naturezaJuridicaPai > 0) ? $naturezaJuridicaPai : '';
                $criteria['sqNaturezaJuridica']    = ($naturezaJuridica > 0) ? $naturezaJuridica : '';

            } else {
                $cpf    = Zend_Filter::filterStatic($this->_getParam('nuCpf'), 'Digits');
                $result = $this->getServiceLocator()
                               ->getService('VwPessoa')
                               ->getDataInfoconvBy( $cpf, 'cpf' );
                
                $result = \Core_Integration_Abstract_Soap::xmlToArray($result);

                $criteria['code'] = $result['errocode'];

                if ('failure' == $result['status']) {
                    throw new \Exception($result['response']);
                }

                $criteria['noPessoa']           = $result['response']['nome'] ? $result['response']['nome'] : '';
                $criteria['dtNascimento']       = $result['response']['data_nascimento'] ? $result['response']['data_nascimento'] : '';
                $criteria['noMae']              = $result['response']['nome_mae'] ? trim($result['response']['nome_mae']) : '';
                $criteria['nacionalidade']      = (bool)$result['response']['estrangeiro'] ? 2 : 1;
                $criteria['sqPaisNaturalidade'] = (int) $result['response']['codigo_pais_exterior'];
                $criteria['noPaisNaturalidade'] = $result['response']['nome_pais_exterior'];
            }

            $criteria['dtIntegracaoInfoconv'] = Zend_Date::now()->toString(); 
            $criteria['personId']             = trim(\Core_Integration_Sica_User::getPersonId());
            $criteria['success']              = true;
        } catch (Exception $e) {
            $criteria['response'] = $e->getMessage();
            $criteria['success']  = false;
        }
        
        $this->_helper->json($criteria);
        die();
    }
}