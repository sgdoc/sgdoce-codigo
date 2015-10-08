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

namespace Artefato\Service;

use Doctrine\ORM\Query\ParameterTypeInferer;

/**
 * Classe para Service de Pessoa
 *
 * @package  Minuta
 * @category Service
 * @name     Pessoa
 * @version  1.0.0
 */
class Pessoa extends \Auxiliar\Service\PessoaSgdoce
{
    /**
     * Variável para receber a entidade Pessoa
     * @var    string
     * @access protected
     * @name   $_entityName
     */
    protected $_entityName            = 'app:PessoaArtefato';
    protected $_entityNameCorp        = 'app:VwPessoa';
    protected $_entityMotivacao       = 'app:Motivacao';
    protected $_entityNameSgdoce      = 'app:PessoaSgdoce';
    protected $_entityNameAssinante   = 'app:PessoaAssinanteArtefato';
    protected $_entityNameInteressado = 'app:PessoaInteressadaArtefato';

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do corporativo
     * @return array
     */
    public function listPessoa($params,$limit = 10)
    {
        $pessoa = array();
        foreach ($this->_getRepository($this->_entityNameCorp)->listPessoa($params, $limit) as $dPessoa) {
            $noPessoa = $dPessoa->getNoPessoa();
            if ($params['extraParam'] == \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos()) {

                $sigla = ($dPessoa->getSqUnidadeOrgExterna()->getSgPai())?$dPessoa->getSqUnidadeOrgExterna()->getSgPai() . ' - ': '';

                $noPessoa = $sigla . $dPessoa->getNoPessoa();
            }
            $pessoa[$dPessoa->getSqPessoa()] = $noPessoa;
        }

        if ($params['extraParam'] == \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos()) {
            foreach ($this->_getRepository('app:VwRppn')->listPessoa($params, $limit) as $rppn) {
                $pessoa[$rppn->getSqPessoa()->getSqPessoa()] = $rppn->getSgRppn();
            }
        }
        return $pessoa;
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do corporativo
     * @return array
     */
    public function searchDadosProfissinal($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchDadosProfissinal($dtoSearch);
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do corporativo
     * @return array
     */
    public function searchPessoaInterna($dtoSearch, $limit = NULL)
    {
        return $this->_getRepository('app:VwProfissional')->searchPessoaInterna($dtoSearch, $limit);
    }

    public function searchPessoaExterna($dtoSearch, $limit=10)
    {
        switch ($dtoSearch->getTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos() :
                $result = $this->_getRepository('app:VwPessoaFisica')->searchPessoaFisica($dtoSearch, TRUE, $limit);
                break;
            default:
                $result =  array();
                break;

// ANTES OS CASES ESTAVAM ASSIM:
// alterado em 03/08/15
//            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
//                return $this->_getRepository('app:VwPessoaFisica')->searchPessoaFisica($dtoSearch, FALSE);
//                break;
//            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
//                return $this->_getRepository('app:VwPessoaJuridica')->searchPessoaJuridica($dtoSearch, FALSE);
//                break;
//            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos() :
//                $pessoa = $this->_getRepository('app:VwUnidadeOrgExterna')
//                    ->searchUnidadesOrganizacionaisExternas($dtoSearch);
//
//                foreach ($this->_getRepository('app:VwRppn')->listPessoa($dtoSearch) as $rppn) {
//                    $pessoa[$rppn->getSqPessoa()->getSqPessoa()] = $rppn->getSgRppn();
//                }
//                return $pessoa;
//                break;
        }
        return $result;
    }

    /**
     * Método que retorna a entidade pessoas do profissional
     * @return object
     */
    public function findProfissional($obj)
    {
        return $this->_getRepository('app:VwProfissional')->find(
                $obj->getSqPessoaSgdoce()->getSqPessoaCorporativo()->getSqPessoa() );
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do corporativo
     * @return array
     */
    public function searchInterna($dtoSearch)
    {
        return $this->_getRepository('app:VwProfissional')->searchPessoaInterna($dtoSearch);
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo para as pessoas do sgdoce
     * @return array
     */
    public function listBySqTipoPessoa($sqTipoPessoa)
    {
        $pessoa = array();
        $dadosPessoa = $this->_getRepository($this->_entityNameCorp)->listPessoa($sqTipoPessoa);
        foreach ($dadosPessoa as $dPessoa) {
            $pessoa[$dPessoa->getSqPessoa()] = $dPessoa->getNoPessoa();
        }
        return $pessoa;
    }

    /**
     * Método que retorna cpf ou cnpj da pessoa
     * @return array
     */
    public function getTipoPessoa($dtoSearch,$value)
    {
        $cpfCnpj = '';
        switch ($dtoSearch->getSqTipoPessoa()){

            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $cpf = $value->getSqPessoaFisica() ? $value->getSqPessoaFisica()->getNuCpf() : NULL;
                $cpfCnpj = substr($cpf, 0 , 3) . '.' . substr($cpf, 3 , 3)
                . '.' . substr($cpf, 6 , 3). '-' . substr($cpf, 9 , 2);
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $cnpj = $value->getSqPessoaJuridica() ? $value->getSqPessoaJuridica()->getNuCnpj() : NULL;
                $cpfCnpj = substr($cnpj, 0 , 2) . '.' . substr($cnpj, 2 , 3) . '.' . substr($cnpj, 5 , 3) . '/'
                        . substr($cnpj, 8 , 4) . '-' . substr($cnpj, 12 , 2);
                break;
            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro() :
                $cpfCnpj = $value->getSqDocumento()
                ? $value->getSqPessoaEstrangeira()->getNuPassaporte()
                : NULL;
                break;
            default:
                break;
        };
        return $cpfCnpj;
    }

    /**
     * Método que retorna os dados da assinatura
     * @return array
     */
    public function getPessoaAssinatura($dtoSearch = NULL)
    {
        return $this->_getRepository($this->_entityNameCorp)->getPessoaAssinatura($dtoSearch);
    }

    /**
     * Obtém lista de destinatários
     * @param \Core_Dto_Search $search
     * @return json
     */
    public function listAssinatura(\Core_Dto_Search $search)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityNameAssinante);
        $result     = $repository->searchPageDto('listAssinatura', $search);
        return $result;
    }

    /**
     * Realiza ajustes antes do instert da pessoa
     * @param $entity
     * @param $dto
     */
    public function preInsert($entity, $dto = NULL)
    {
        $params['sqArtefato'] = $entity->getSqArtefato();
        $params['sqPessoaFuncao'] = $entity->getSqPessoaFuncao();
        $search = \Core_Dto::factoryFromData($params, 'search');
        $entity->setNuHistoricoPessoa($this->getNextNuHistoricoPessoa($search));
    }

    /**
     * Valida dados
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function validaDados(\Core_Dto_Abstract $dto)
    {
        if(!$dto->getSqTipoPessoaInteressado())
        {
            return $this->_getRepository('app:VwProfissional')->validaDadosInterno($dto);
        }
        return $this->_getRepository($this->_entityNameSgdoce)->validaDados($dto);
    }

    /**
     * Método para listagem da Grid
     * @param array $params
     * @return json
     */
    public function listGridInteressado(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityNameSgdoce);
        return $repository->searchPageDto('listInteressado', $dtoSearch);
    }
    /**
     * Método para listagem da Grid
     * @param array $params
     * @return json
     */
    public function listGridDocumento(\Core_Dto_Search $dtoSearch)
    {
        $repository = $this->getEntityManager()->getRepository('app:Artefato');
        return $repository->searchPageDto('listGridDocumentoEletronico', $dtoSearch);
    }

    /**
     * Método que obtén informarções das pessoas relacionadas ao artefato
     * @param \Core_Dto_Search $dto
     * @return json
     */
    public function findbyPessoa($dto)
    {
        $result = $this->_getRepository('app:PessoaSgdoce')->findbyPessoa($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'origem' relacionada ao artefato
     * @param integer
     * @return json
     */
    public function getPessoaArtefatoOrigem($dto)
    {
        $result = $this->_getRepository('app:PessoaSgdoce')->getPessoaArtefatoOrigem($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'autor' relacionada ao artefato
     * @param integer
     * @return json
     */
    public function getPessoaArtefatoAutor($dto)
    {
        $result = $this->_getRepository('app:PessoaSgdoce')->getPessoaArtefatoAutor($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'destinatario' relacionada ao artefato
     * @param integer
     * @return json
     */
    public function getPessoaArtefatoDestinatario($dto)
    {
        $result = $this->_getRepository('app:PessoaSgdoce')->getPessoaArtefatoDestinatario($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'interessado' relacionada ao artefato
     * @param integer
     * @return json
     */
    public function getPessoaArtefatoInteressado($dto)
    {
        $result = $this->_getRepository('app:PessoaInteressadaArtefato')->getPessoaArtefatoInteressado($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'rodape' relacionada ao artefato
     * @param integer
     * @return json
     */
    public function getPessoaArtefatoRodape($dto)
    {
        $result = $this->_getRepository('app:PessoaArtefato')->getPessoaArtefatoRodape($dto);
        return $result;
    }

    /**
     * Método que obtén informarções da pessoa 'assinatura' relacionada ao artefato
     * @param integer
     * @return array
     */
    public function getPessoaArtefatoAssinatura($dto)
    {
        $result = $this->_getRepository('app:Motivacao')->getPessoaArtefatoAssinatura($dto);

        if(empty($result)){
            $result = $this->_getRepository('app:PessoaAssinanteArtefato')->getPessoaArtefatoAssinatura($dto);
        }

        return $result;
    }

    /**
     * Método que obtén informarções das pessoas relacionadas ao artefato
     * @param \Core_Dto_Search $dto
     * @return json
     */
    public function findbyPessoaCorporativo(\Core_Dto_Abstract $dto)
    {
        $entityPessoa   = $this->_getRepository($this->_entityNameCorp)->find($dto->getSqPessoa());

        $endereco       = $this->_getRepository('app:VwEndereco')->findEndereco($dto->getSqPessoa());
        $telefone = $this->getServiceLocator()->getService('VwTelefone')->findTelefone($dto->getSqPessoa());
        $email = $this->getServiceLocator()->getService('VwEmail')->findEmail($dto->getSqPessoa());

        if(!$endereco) {
            $endereco = $this->_newEntity('app:VwEndereco');
        }

        if(!$email->getSqEmail()) {
            $email = $this->_newEntity('app:VwEmail');
        }

        if(!$telefone->getSqTelefone()) {
            $telefone = $this->_newEntity('app:VwTelefone');
        }

        $entityPessoa->setSqEndereco($endereco);
        $entityPessoa->setSqEmail($email);
        $entityPessoa->setSqTelefone($telefone);

        return $entityPessoa;
    }

    /**
     * Realiza save extras
     * @param \Core_Dto_Search $dtoSearch
     */
    public function deleteAssinatura(\Core_Dto_Abstract $dtoAssinante)
    {
        $repository = $this->_getRepository($this->_entityMotivacao);
        $repository->deleteMotivacao($dtoAssinante);
        $repository = $this->_getRepository($this->_entityNameAssinante);
        $repository->deleteAssinatura($dtoAssinante);
    }

    /**
     * Realiza consulta pessoa existente
     * @param \Core_Dto_Entity $dtoSearch
     */
    public function findPessoaSgdoce($entityPessoaSgdoce)
    {
        $repository = $this->_getRepository($this->_entityNameSgdoce);
        return $repository->findPessoaSgdoce($entityPessoaSgdoce);
    }

    /**
     * Realiza consulta pessoa existente
     * @param \Core_Dto_Entity $dtoSearch
     */
    public function findPessoaAssinaturaArtefato($entityPessoaSgdoce)
    {
        $repository = $this->_getRepository($this->_entityNameSgdoce);
        return $repository->findPessoaAssinaturaArtefato($entityPessoaSgdoce);
    }

    /**
     * Exclui a pessoa interessado do artefato
     * @param \Core_Dto_Search $dtoSearch
     * @return boolean
     */
    public function deleteInteressado(\Core_Dto_Abstract $dtoSearch)
    {
        $repository = $this->_getRepository($this->_entityNameInteressado);
        $criteria = array(
            'sqArtefato' => $dtoSearch->getSqArtefato()->getSqArtefato(),
            'sqPessoaSgdoce' => $dtoSearch->getSqPessoaSgdoce()->getSqPessoaSgdoce()
        );
        $interessado = $repository->findOneBy($criteria);
        $this->getEntityManager()->remove($interessado);
        $this->getEntityManager()->flush();
        return TRUE;
    }

    /**
     * Exclui o destinatario do artefato
     * @param \Core_Dto_Search $dtoSearch
     * @return boolean
     */
    public function deleteDestinatario(\Core_Dto_Abstract $dtoSearch)
    {
        $repository = $this->_getRepository($this->_entityName);
        $criteria = array(
            'sqArtefato' => $dtoSearch->getSqArtefato()->getSqArtefato(),
            'sqPessoaSgdoce' => $dtoSearch->getSqPessoaSgdoce()->getSqPessoaSgdoce(),
            'sqPessoaFuncao' => $dtoSearch->getSqPessoaFuncao()->getSqPessoaFuncao()
        );

        $destinario = $repository->findOneBy($criteria);
        $this->getEntityManager()->remove($destinario);
        $this->getEntityManager()->flush();
        return TRUE;
    }

    /**
     * Monta o Dto pessoa
     * @return array
     */
    public function mountDtoPessoaSgdoce($params)
    {
        //sgdoce
        $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
        $params = $this->getPessoaCorporativo($dtoSearch,$params);

        $dtoPessoaSgdoce = \Core_Dto::factoryFromData($params,
                'entity', array('entity'=> 'Sgdoce\Model\Entity\PessoaSgdoce',
                        'mapping' => array(
                                'sqTipoPessoa'         => 'Sgdoce\Model\Entity\VwTipoPessoa'
                                ,'sqPessoaCorporativo'  => array('sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa'))));
        $sqPessoaSgdoce = $this->getServiceLocator()->getService('Pessoa')->findPessoaSgdoce($dtoPessoaSgdoce);
        if(!$sqPessoaSgdoce){
            $pessoaCorp = $this->_getRepository('app:VwPessoa')->find($dtoPessoaSgdoce->getSqPessoaCorporativo()->getSqPessoa());
            switch ($pessoaCorp->getSqTipoPessoa()) {
                case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()://fisica
                    if($pessoaCorp->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == 1 ||
                    $pessoaCorp->getSqPessoaFisica()->getSqNacionalidade()->getSqPais() == NULL)
                    {
                        $dtoPessoaSgdoce->setNuCpfCnpjPassaporte($pessoaCorp->getSqPessoaFisica()->getNuCpf());
                    }else{
                        $repository = $this->_getRepository('app:VwPessoa');
                        $atributoTipoDocumento = $repository->getDocumento();
                        $repositoryDoc = $this->_getRepository('app:VwDocumento');
                        $nuDocumento  = $repositoryDoc->findBy(array('sqPessoa' =>
                                $pessoaCorp->getSqPessoa(),
                                'sqAtributoTipoDocumento' => $atributoTipoDocumento['sqAtributoTipoDocumento']));
                        if(count($nuDocumento)){
                            $dtoPessoaSgdoce->setNuCpfCnpjPassaporte($nuDocumento[0]->getTxValor());
                        }
                    }
                    break;
                case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica()://juridica
                    if($pessoaCorp->getSqPessoaJuridica())
                    {
                        $dtoPessoaSgdoce->setNuCpfCnpjPassaporte($pessoaCorp->getSqPessoaJuridica()->getNuCnpj());
                    }
                    break;
                default:
                    ;
                    break;
            }
            $return = $this->getServiceLocator()->getService('MinutaEletronica')->saveDestinatario($dtoPessoaSgdoce);
            $sqPessoaSgdoce = $return->getSqPessoaSgdoce();
        }
        $params['sqPessoaSgdoce'] = $sqPessoaSgdoce;
        return $params;
    }

    public function findByPessoaFisica($criteria)
    {
    	return $this->_getRepository('app:vwPessoaFisica')->findBy($criteria);
    }

    public function getPessoa($dto)
    {
        if ($dto->getSqTipoPessoa() == 1) {
            return $this->_getRepository('app:VwPessoaFisica')->find($dto->getSqPessoaCorporativo());
        } else {
            return $this->_getRepository('app:VwPessoaJuridica')->find($dto->getSqPessoaCorporativo());
        }
    }

    public function searchPessoaJuridicaPorCnpj($dtoSearch)
    {
        $nuCnpj = str_replace(array('.', '-', '/'), '',  $dtoSearch->getNuCnpj());
        return $this->_getRepository('app:VwPessoaJuridica')->searchPessoaJuridicaPorCnpj($nuCnpj);
    }

    /**
     * Método que retorna os possiveis assinantes unicos da minuta
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getAssinantesUnicos()
    {
        $result = array();

        for($i=1; $i<=3; $i++) {
            $params['sqPessoa'] = \Core_Integration_Sica_User::getPersonId();
            $params['sqUnidadeOrg'] = \Core_Integration_Sica_User::getUserUnit();
            $params['sqTipoPessoa'] = $i;
            $params['txRota'] = '/artefato/visualizar-caixa-minuta/assinar-minuta';
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $pessoa = $this->_getRepository($this->_entityNameCorp)->getPessoaAssinatura($dtoSearch);
            $params['sqPessoa'] = !empty($pessoa[0]['sqPessoa']) ? $pessoa[0]['sqPessoa'] : null;
            $dtoSearch = \Core_Dto::factoryFromData($params, 'search');
            $podeAssinar = $this->_getRepository($this->_entityNameCorp)->verificaUsuarioRota($dtoSearch);

            if ($podeAssinar) {
                $result[$i] = $pessoa[0];
            }
        }

        return $result;
    }

    /**
     * Método para listagem de interessados.
     *
     * @param array $params
     * @return json
     */
    public function listInteressados(\Core_Dto_Search $dtoSearch)
    {
        return $this->getEntityManager()
                    ->getRepository($this->_entityNameSgdoce)
                    ->listInteressado($dtoSearch)->getQuery()->execute();

    }
}