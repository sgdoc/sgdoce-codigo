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

namespace Migracao\Service;

/**
 * Classe para Service de Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class ArtefatoMigracao extends \Artefato\Service\ArtefatoExtensao
{

    /**
     * @var string
     */
    protected $_entityName = 'app:Artefato';
    protected $_pessoaArtefato = 'app:PessoaArtefato';

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridMaterial (\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->searchPageDto('listGridMaterialApoio', $dto);
        return $result;
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridMaterialDocumento (\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->searchPageDto('listGridMaterialDocumento', $dto);
        return $result;
    }

    /**
     * método para pesquisa de artefato pela chave
     * @param \Core_Dto_Search $dto
     */
    public function findArtefato (\Core_Dto_Search $dto)
    {
        if ($dto->getSqArtefato()) {
            return $this->_getRepository()->find($dto->getSqArtefato());
        }
    }

    public function listGridHistorico($dto)
    {
        return $this->_getRepository('app:HistoricoArtefato')->searchPageDto('listGridHistorico', $dto);
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function getHistoricoByArtefato(\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:HistoricoArtefato')->listAllHistorico($dto);
    }

    /**
     * método para pesquisa de assinatura pela chave sqArtefato
     * @param \Core_Dto_Search $dto
     */
    public function findAssinaturaArtefato (\Core_Dto_Search $dto)
    {
        return $this->_getRepository('app:Assinatura')->findAssinatura($dto);
    }

    /**
     * método para pesquisa de artefato pela chave
     * @param \Core_Dto_Search $dto
     */
    public function findVisualizarArtefato (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->findVisualizarArtefato($dto);
    }

    /**
     * método para pesquisa de artefato pela chave
     * @param \Core_Dto_Search $dto
     */
    public function findTextoComplemetar (\Core_Dto_Search $dto)
    {
        $criteria = array('nuDigital' => $dto->getNuDigitalMaterial());
        $sqArtefato = $this->_getRepository('app:Artefato')->findOneBy($criteria);
        return $sqArtefato->getTxAssuntoComplementar();
    }

    public function transformarMinutaDocumentoEletronico (\Core_Dto_Abstract $dto)
    {
        $artefato = $this->findArtefato($dto);
        $assunto = $artefato->getSqTipoArtefatoAssunto()
                ->getSqAssunto()
                ->getSqAssunto();

        $tipoArtefatoAssunto = $this->_getRepository('app:TipoArtefatoAssunto')->findOneBy(array(
            'sqAssunto' => $assunto,
            'sqTipoArtefato' => \Core_Configuration::getSgdoceTipoArtefatoDocumento()
        ));

        $artefato->setSqTipoArtefatoAssunto($tipoArtefatoAssunto);
        $artefato->setNuArtefato($dto->getNuArtefato());
        $artefato->setNuDigital($dto->getNuDigital());

        $this->getEntityManager()->persist($artefato);
        $this->getEntityManager()->flush($artefato);
    }

    /**
     * consulta artefato pelo numero
     * @param object $dto
     * @return object
     */
    public function findNumeroDigital ($dto)
    {
        return $this->_getRepository()->findNumeroDigital($dto->getQuery(), $dto);
    }

    /**
     *
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     * @throws \Core_Exception_ServiceLayer
     */
    public function getNextElectronicDigitalNumber (\Core_Dto_Search $dtoSearch)
    {
        $repo = $this->getEntityManager()->getRepository('app:LoteEtiqueta');
        $sqLoteEtiqueta = $repo->recuperaNumeroLoteEtiquetaEletronica($dtoSearch);

        if (null === $sqLoteEtiqueta) {
            throw new \Core_Exception_ServiceLayer('Não existe lote de etiqueta eletrônica disponível para sua unidade');
        }

        $digital = $repo->getNextDigitalNumber($sqLoteEtiqueta);

        if (!$digital) {
            throw new \Core_Exception_ServiceLayer('Não há etiqueta eletrônica disponível para sua unidade');
        }

        $aux['sqLoteEtiqueta'] = $digital[0]['sqLoteEtiqueta'];
        $aux['nuDigital'] = $digital[0]['nuEtiqueta'];

        return $aux;
    }

    /**
     * [RN2.7] Gerar Digital Eletronica
     * @return String numero da digital
     */
    public function createNumeroDigital ()
    {
        trigger_error("Metodo 'Artefato\Service\Artefato::createNumeroDigital()' depreciado, use 'Artefato\Service\Artefato::getNextElectronicDigitalNumber()'", E_USER_ERROR);

        // recupera a ultima digital gerada
        $ultimaDigital = $this->_getRepository()->lastDigitalNumber();
        // caso não retorne nenhum registro, retorna o primeiro codigo para o ano.
        if (!count($ultimaDigital)) {
            return 'E0000001-' . date('y');
        }

        // gerando e retornando o numero posterior ao recuperado
        $lastCode = substr($ultimaDigital[0]['nu_digital'], 1, -3);
        return 'E' . str_pad($lastCode + 1, 7, '0', STR_PAD_LEFT) . '-' . date('y');
    }

    /**
     * [RN3.13] Recuperar o próximo numero para o artefato
     * @return String numero do documento
     */
    public function recuperaProximoNumeroArtefato ($params)
    {
        if (isset($params['action']) && $params['action'] == 'alterar-sequencial') {
            $criteria = $params;
        } else {
            // o numero do documento deve gerado a partir do sequencial do primeiro assinante
            $primeiroAssinante = $this->getServiceLocator()
                    ->getService('PessoaAssinanteArtefato')
                    ->findBy(
                    array('sqArtefato' => $params['sqArtefato']), array('dtAssinado' => 'ASC')
            );

            $unidadeAssinante = $primeiroAssinante[0]->getSqPessoaUnidadeOrg()
                    ->getSqPessoaUnidadeOrgCorp()
                    ->getSqUnidadeOrg();
            $criteria = array(
                'sqUnidadeOrg' => $unidadeAssinante,
                'sqTipoDocumento' => $params['sqTipoDocumento'],
                'sqTipoArtefato' => $params['sqTipoArtefato']
            );
        }

        // recupera o sequencial para o tipo de decumento, unidade e ano
        $sequencial = $this->getServiceLocator()->getService('Sequnidorg')->getSequencialPorUnidade($criteria);
        $numeroSequencial = $sequencial->getNuSequencial();
        //verifica qual o proximo numero que pode ser utilizado
        $disponivel = FALSE;
        $params['nuArtefato'] = $numeroSequencial;
        do {
            $params['nuArtefato'] = str_pad($params['nuArtefato'] + 1, 4, '0', STR_PAD_LEFT) . '/' . date('Y');
            $disponivel = $this->_getRepository()->verificaNuArtefatoDisponivel($params);


            if (!$disponivel) {
                $sequencial->setNuSequencial($sequencial->getNuSequencial() + 1);
            }
        } while ($disponivel == FALSE);
        return $sequencial;
    }

    /**
     * Alualia o numero sequencial
     */
    public function atualizarSequencial ($ultimNumeroArtefato)
    {
        $ultimNumeroArtefato->setNuSequencial($ultimNumeroArtefato->getNuSequencial() + 1);
        $this->getEntityManager()->persist($ultimNumeroArtefato);
        $this->getEntityManager()->flush($ultimNumeroArtefato);
    }

    /**
     * Metódo que realiza a persistencia de DESTINO e ORIGEM
     * @param Object $entity
     * @param Object $dto
     */
    public function salvaOrigemDestino ($entity, $dto = NULL)
    {
        // salvando Origem
        self::_salvaOrigem($entity, $dto);
        // salvando Destino
        self::_salvaDestino($entity, $dto);
    }

    /**
     * Metódo que realiza a persistencia de ORIGEM
     * @param Object $entity
     * @param Object $dto
     * @return Object
     */
    protected function _salvaOrigem ($entity, $dto = NULL)
    {
        // colocando valores em variaves genericas
//         $cpfCnpjPassaport = $this->retornaRegistro($dto);
        switch ($dto->getProcedenciaInterno()) {
            case 'externo':
                $stProcedencia = FALSE;
                break;
            case 'interno':
                $stProcedencia = TRUE;
                break;

            default:
                $stProcedencia = NULL;
                break;
        }

        $filter = new \Zend_Filter_Digits();
        if ($dto->getSqTipoPessoaOrigem() != '') {
            $sqTipoPessoa = $dto->getSqTipoPessoaOrigem();
        } else {
            $sqTipoPessoa = $dto->getSqTipoPessoaOrigemIcmbio();
        }

        if ($dto->getSqPessoaOrigem() != '') {
            $sqPessoaOrigem = $dto->getSqPessoaOrigem();
        } else {
            $sqPessoaOrigem = $dto->getSqPessoaIcmbio();
        }

        if ($dto->getSqPessoaOrigem_autocomplete()) {
            $noPessoaOrigem = $dto->getSqPessoaOrigem_autocomplete();
        } else {
            $noPessoaOrigem = $dto->getSqPessoaIcmbio_autocomplete();
        }
        if ($sqPessoaOrigem != 0) {
            $data['sqPessoaCorporativo'] = $sqPessoaOrigem;
            $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
            $cpfCnpjPassaport = $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch);
            $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaport);
        }
        // verificando se existe Pessoa cadastrada no PessoaSgdoce
        $entPessoaSgdoce = $this->searchPessoaSgdoce($sqPessoaOrigem);

        // Se nao existir registro na base de PessoaSgdoce, verifica se é PF ou PJ e recupera as informacoes
        // na base Corporativo e realiza o cadastro na base de PessoaSgdoce
        if (!count($entPessoaSgdoce)) {
            // Não existindo registro em PessoaSgdoce, faz cadastro na mesma
            $entPessoaSgdoce = $this->addPessoaSgdoce($sqPessoaOrigem, $noPessoaOrigem, $cpfCnpjPassaport);
            $entPessoaSgdoce->setNuCpfCnpjPassaporte($cpfCnpjPassaport);

            // retorna o numero do registro
            $criteriaTpPessoa = array('sqTipoPessoa' => $sqTipoPessoa);
            $entPessoaSgdoce->setSqTipoPessoa($this->_getRepository('app:VwTipoPessoa')->findOneBy($criteriaTpPessoa));
        }

        $this->getEntityManager()->persist($entPessoaSgdoce);
        $entPessoaSgdoce->setNuCpfCnpjPassaporte($cpfCnpjPassaport);
        $this->getEntityManager()->flush($entPessoaSgdoce);

        // cadastra e retorna PessoaArtefato
        $entityPessoaArtefato = $this->cadastrarPessoaArtefato($entity, $entPessoaSgdoce, \Core_Configuration::getSgdocePessoaFuncaoOrigem());
        $entityPessoaArtefato->setStProcedencia($stProcedencia);
        
        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);

        return $entityPessoaArtefato;
    }

    /**
     * Metódo que realiza a persistencia de DESTINO
     * @param Object $entity
     * @param Object $dto
     * @return Object
     */
    protected function _salvaDestino ($entity, $dto)
    {
        $filter = new \Zend_Filter_Digits();

        switch ($dto->getDestinoInterno()) {
            case 'externo':
                $stProcedencia = FALSE;
                break;
            case 'interno':
                $stProcedencia = TRUE;
                break;
            default:
                $stProcedencia = NULL;
                break;
        }

        $sqPessoaDestino = $this->recuperaSqPessoaDto($dto);
//         $cpfCnpjPassaport = $this->retornaRegistro($dto);

        if ($dto->getSqTipoPessoaDestino()) {
            $sqTipoPessoa = $dto->getSqTipoPessoaDestino();
        } else {
            $sqTipoPessoa = $dto->getSqTipoPessoaDestinoIcmbio();
        }

        if ($dto->getSqPessoa_autocomplete()) {
            $noPessoaDestino = $dto->getSqPessoa_autocomplete();
        } else {
            $noPessoaDestino = $dto->getSqPessoaIcmbioDestino_autocomplete();
        }
        
        $sqPessoaEncaminhado = null;        
        if ($dto->getSqPessoaEncaminhado()) {
            $sqPessoaEncaminhado = $dto->getSqPessoaEncaminhado();
            $noPessoaEncaminhado = $dto->getSqPessoaEncaminhado_autocomplete();
        } else if( $dto->getSqPessoaEncaminhadoExterno() ) {
            $sqPessoaEncaminhado = $dto->getSqPessoaEncaminhadoExterno();
            $noPessoaEncaminhado = $dto->getSqPessoaEncaminhadoExterno_autocomplete();
        }

        if ($sqPessoaDestino != 0) {
            $data['sqPessoaCorporativo'] = $sqPessoaDestino;
            $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
            $cpfCnpjPassaport = $this->getServiceLocator()->getService('VwPessoa')->returnCpfCnpjPassaporte($dtoPessoaSearch);
            $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaport);
        }

        // verificando se existe Pessoa cadastrada no PessoaSgdoce
        $entPessoaSgdoce = $this->searchPessoaSgdoce($sqPessoaDestino);

        // Se nao existir registro na base de PessoaSgdoce, verifica se é PF ou PJ e recupera as informacoes
        // na base Corporativo e realiza o cadastro na base de PessoaSgdoce
        if (!count($entPessoaSgdoce)) {

            // Não existindo registro em PessoaSgdoce, faz cadastro na mesma
            $entPessoaSgdoce = $this->addPessoaSgdoce($sqPessoaDestino, $noPessoaDestino, $cpfCnpjPassaport);

            $entPessoaSgdoce->setNuCpfCnpjPassaporte($cpfCnpjPassaport);

            // retorna o numero do registro
            $criteriaTipoPessoa = array('sqTipoPessoa' => $sqTipoPessoa);
            $entPessoaSgdoce->setSqTipoPessoa($this->_getRepository('app:VwTipoPessoa')->findOneBy($criteriaTipoPessoa));

            $this->getEntityManager()->persist($entPessoaSgdoce);
            $this->getEntityManager()->flush($entPessoaSgdoce);
        }

        // cadastrando PessoaArtefato
        $entityPessoaArtefato = $this->cadastrarPessoaArtefato($entity, $entPessoaSgdoce, \Core_Configuration::getSgdocePessoaFuncaoDestinatario());

        if ($sqPessoaEncaminhado ) {
            // verificando se existe PessoaEncaminhado cadastrada no PessoaSgdoce
            $entPessoaEncaminhado = $this->searchPessoaSgdoce($sqPessoaEncaminhado);

            if (!count($entPessoaEncaminhado)) {

                $data['sqPessoaCorporativo'] = $sqPessoaEncaminhado ;

                $dtoPessoaSearch = \Core_Dto::factoryFromData($data, 'search');
                $cpfCnpjPassaport = $this->getServiceLocator()
                        ->getService('VwPessoa')
                        ->returnCpfCnpjPassaporte($dtoPessoaSearch);
                $cpfCnpjPassaport = $filter->filter($cpfCnpjPassaport);

                // Não existindo registro em PessoaSgdoce, faz cadastro na mesma
                $entPessoaEncaminhado = $this->addPessoaSgdoce($sqPessoaEncaminhado , $noPessoaEncaminhado, $cpfCnpjPassaport);

                // retorna o numero do registro
                $criteriaTipoPessoa = array('sqTipoPessoa' => $sqTipoPessoa);
                $entPessoaEncaminhado->setSqTipoPessoa(
                        $this->_getRepository('app:VwTipoPessoa')->findOneBy($criteriaTipoPessoa));

                $this->getEntityManager()->persist($entPessoaEncaminhado);
                $this->getEntityManager()->flush($entPessoaEncaminhado);
            }

            // setando valores
            $entityPessoaArtefato->setSqPessoaEncaminhado($entPessoaEncaminhado);
        } else {
            $entityPessoaArtefato->setSqPessoaEncaminhado(NULL);
        }
        $entityPessoaArtefato->setNoCargoEncaminhado($dto->getNoCargoEncaminhado());
        $entityPessoaArtefato->setStProcedencia($stProcedencia);

        // persistindo informacoes
        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);
    }

    /**
     * Verifica se existe registro em PessoaSgdoce
     *
     * @param int $sqPessoaCorporativo
     *
     * @return Object Entity PessoaSgdoce
     */
    public function searchPessoaSgdoce ($sqPessoaCorporativo)
    {
        // Verificando a existencia na PessoaSgdoce pelo codigo sqPessoaCorporativo
        $criteria = array('sqPessoaCorporativo' => $sqPessoaCorporativo);
        return $this->_getRepository('app:PessoaSgdoce')->findOneBy($criteria);
    }

    /**
     * Metódo que realiza a persistencia de DESTINO e ORIGEM
     * @param Object $entity
     * @param Object $entPessoaSgdoce
     * @param int $sqPessoaFuncao
     */
    public function cadastrarPessoaArtefato ($entity, $entPessoaSgdoce, $sqPessoaFuncao)
    {
        // verificandos se ja existe cadastro em PessoaArtefato
        $criteria = array('sqArtefato' => $entity->getSqArtefato(), 'sqPessoaFuncao' => $sqPessoaFuncao);
        $entityPessoaArtefato = $this->_getRepository('app:PessoaArtefato')->findOneBy($criteria);

        // verificando registro
        if (!count($entityPessoaArtefato)) {
            $criteriaPessoaFunc = array('sqPessoaFuncao' => $sqPessoaFuncao);
            $criteriaArtefato = array('sqArtefato' => $entity->getSqArtefato());

            $entityPessoaArtefato = $this->_newEntity('app:PessoaArtefato');
            $entityPessoaArtefato->setSqArtefato($this->_getRepository('app:Artefato')->findOneBy($criteriaArtefato));
            $entityPessoaArtefato->setSqPessoaSgdoce($entPessoaSgdoce);
            $entityPessoaArtefato->setSqPessoaFuncao($this->_getRepository('app:PessoaFuncao')->findOneBy(
                            $criteriaPessoaFunc));
        } else {
            $entityPessoaArtefato->setSqPessoaSgdoce($entPessoaSgdoce);
        }

        // retornando objeto
        return $entityPessoaArtefato;
    }

    /**
     * Metódo que realiza  a consulta de pessoa artefato
     * @param Object $artefato
     * @param Object $pessoa
     * @param int $sqTipoFuncao
     *
     * @return Object \PessoaArtefato
     */
    public function pessoaArtefato ($artefato, $pessoa, $sqTipoFuncao)
    {
        // retornando registros
        return $this->_getRepository('app:PessoaArtefato')->searchPessoaArtefato($artefato, $pessoa, $sqTipoFuncao);
    }

    /**
     * Adiciona PessoaCorporativo na PessoaSgdoce
     *
     * @param int $sqPessoaCorporativo
     * @param String $noPessoaCorporativo
     *
     * @return Object Entity PessoaSgdoce
     */
    public function addPessoaSgdoce ($sqPessoaCorporativo, $noPessoaCorporativo, $cpfCnpjPassaport = NULL)
    {
        // verificando se existe Pessoa cadastrada no PessoaSgdoce
        //        $entPessoaSgdoce = $this->searchPessoaSgdoce($sqPessoaCorporativo);
        // verificando se existe Pessoa cadastrada no PessoaSgdoce
        if (strlen($cpfCnpjPassaport) > 11) {
            $entPessoaCorporativo = $this->searchPessoaCnpj($cpfCnpjPassaport, $sqPessoaCorporativo);
        } else {
            // recuperando informacao da base Corporativa
            $criteria = array('sqPessoa' => $sqPessoaCorporativo);
            $entPessoaCorporativo = $this->_getRepository('app:VwPessoa')->findOneBy($criteria);
        }

        $criteriaTipoPessoa = array('sqTipoPessoa' => $entPessoaCorporativo->getSqTipoPessoa());

        // setando valores na entidade para insercao do novo registro
        $entityPessoaSgdoce = new \Sgdoce\Model\Entity\PessoaSgdoce();
        $entityPessoaSgdoce->setNoPessoa($noPessoaCorporativo);
        $entityPessoaSgdoce->setNuCpfCnpjPassaporte($cpfCnpjPassaport);
        $entityPessoaSgdoce->setSqTipoPessoa($this->_getRepository('app:VwTipoPessoa')->findOneBy($criteriaTipoPessoa));
        $entityPessoaSgdoce->setSqPessoaCorporativo($entPessoaCorporativo);

        $this->getEntityManager()->persist($entityPessoaSgdoce);
        $this->getEntityManager()->flush($entityPessoaSgdoce);

        return $entityPessoaSgdoce;
    }

    /**
     * Verifica se existe registro em PessoaSgdoce
     *
     * @param int $cnpj
     *
     * @return Object Entity PessoaSgdoce
     */
    public function searchPessoaCnpj ($cnpj = NULL, $sqPessoa = NULL)
    {
        // Verificando a existencia na PessoaSgdoce pelo codigo sqPessoaCorporativo
        return $this->_getRepository('app:PessoaSgdoce')->findPessoaJuridicaByCnpj($cnpj, $sqPessoa);
    }

    /**
     * método salvar vinculo de material de apoio dos artefatos
     * @param \Core_Dto_Search $dto
     */
    public function salvarMaterial (\Core_Dto_Search $dto)
    {
        $criteria = array('nuDigital' => $dto->getNuDigital());
        $artefato = $this->_getRepository('app:Artefato')->findOneBy($criteria);

        $sqArtefatoPai = $this->_getRepository()->find($dto->getSqArtefato());
        $sqArtefatoFilho = $this->_getRepository()->find($artefato->getSqArtefato());
        $sqTipoVinculo = $this->_getRepository('app:TipoVinculoArtefato')->find(\Core_Configuration::getSgdoceTipoVinculoArtefatoApoio());

        $entity = new \Sgdoce\Model\Entity\ArtefatoVinculo();
        $entity->setSqArtefatoPai($sqArtefatoPai);
        $entity->setSqArtefatoFilho($sqArtefatoFilho);
        $entity->setSqTipoVinculoArtefato($sqTipoVinculo);
        $entity->setDtVinculo(\Zend_Date::now());

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * método para registrar a assinatura do usuario no artefato
     * @param Artefato $artefato
     * @param Array $params
     *
     */
    public function registrarAssinatura ($artefato, $params)
    {

        //busca a pessoaSgdoce da pessoa que esta assinando
        $pessoaSgdoce = $this->searchPessoaSgdoce($params['sqPessoa']);
        $criteria = array(
            'sqPessoaSgdoce' => $pessoaSgdoce->getSqPessoaSgdoce(),
            'sqPessoaUnidadeOrgCorp' => $params['sqUnidadeOrg']
        );
        //busca a pessoaUnidadeOrg da pessoa que esta assinando
        $pessoaUnidadeOrg = $this->getServiceLocator()
                ->getService('PessoaUnidadeOrg')
                ->findOneBy($criteria);
        $sqPessoaUnidadeOrg = $pessoaUnidadeOrg->getSqPessoaUnidadeOrg();

        //busca a lista de assinantes do artefato
        $assinantes = $artefato->getSqPessoaAssinanteArtefato();

        if ($assinantes->count() == 0) {
            $novoAssinante = new \Sgdoce\Model\Entity\PessoaAssinanteArtefato();
            $novoAssinante->setSqArtefato($artefato);
            $novoAssinante->setNoCargoAssinante($pessoaUnidadeOrg->getNoCargo());
            $novoAssinante->setSqPessoaUnidadeOrg($pessoaUnidadeOrg);
            $novoAssinante->setDtAssinado(new \DateTime());

            $this->getEntityManager()->persist($novoAssinante);
            $this->getEntityManager()->flush();
            $assinante = $novoAssinante;
        }

        //veridica qual é o respectivo assinate e atualiza a data da assinatura
        foreach ($assinantes as $assinante) {
            if ($assinante->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg() == $sqPessoaUnidadeOrg) {
                $assinante->setDtAssinado(new \DateTime());
                $this->getEntityManager()->persist($assinante);
                $this->getEntityManager()->flush();
                break;
            }
        }
        return $assinante;
    }

    /**
     * verifica se ainda existe pessoas para assinar o artefato
     * @param Artefato $artefato
     * @param Array $params
     *
     */
    public function verificaArtefatoAssinado ($artefato)
    {

        $assinado = TRUE;

        //busca a lista de assinantes do artefato
        $assinantes = $artefato->getSqPessoaAssinanteArtefato();

        //verifica se falta alguem assinar o artefato
        foreach ($assinantes as $assinante) {
            if ($assinante->getDtAssinado() == NULL) {
                $assinado = FALSE;
            }
        }
        return $assinado;
    }

    /**
     * verifica se a pessoas pode assinar o artefato
     * @param Array $params
     *
     */
    public function verificaPermissaoAssinatura ($params)
    {
        //busca a pessoaSgdoce da pessoa que esta assinando
        $pessoaSgdoce = $this->searchPessoaSgdoce($params['sqPessoa']);
        if (!$pessoaSgdoce) {
            return FALSE;
        }

        $criteria = array(
            'sqPessoaSgdoce' => $pessoaSgdoce->getSqPessoaSgdoce(),
            'sqPessoaUnidadeOrgCorp' => $params['sqUnidadeOrg']
        );
        //busca a pessoaUnidadeOrg da pessoa que esta assinando
        $sqPessoaUnidadeOrg = $this->getServiceLocator()
                ->getService('PessoaUnidadeOrg')
                ->findOneBy($criteria)
                ->getSqPessoaUnidadeOrg();

        //busca a lista de assinantes do artefato
        $assinantes = $this->_getRepository()->find($params['sqArtefato'])
                ->getSqPessoaAssinanteArtefato();

        $autorizado = FALSE;
        //veridica qual é o respectivo assinate e atualiza a data da assinatura
        foreach ($assinantes as $assinante) {
            if ($assinante->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg() == $sqPessoaUnidadeOrg) {
                $autorizado = TRUE;
                break;
            }
        }
        return $autorizado;
    }

    /**
     * Faz a validação do prazo do artefato
     * @param Array $params
     */
    public function validarDataPrazo ($params)
    {
        if ($params['sqPrazo'] == 1 && $params['dtPrazo'] == '') {
            $data = new \DateTime('now');
            $data->add(new \DateInterval('P30D'));
            $params['dtPrazo'] = $data->format('d/m/Y');
        }
        return $params;
    }

    /**
     * Método que gera os artefatos de acordo com a quantidde de destinatários
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @return boolean
     */
    public function gerarVias ($artefato, $params)
    {
        $destinatarios = $this->getServiceLocator()->getService('ArtefatoMinuta')->getPessoaDestinatarioArtefato($artefato);
        $qtDestino = $destinatarios['qtdDestinatario'];

        for ($i = 1; $i < $qtDestino; $i++) {
            $artefatoClone = $this->saveArtefatoClone($artefato);
            $params['sqArtefato'] = $artefatoClone->getSqArtefato();
            $params['nuDigital'] = $this->createNumeroDigital();

            // recuperar o ultimo sequencia por unidade e tipo de documento
            $ultimNumeroArtefato = $this->recuperaProximoNumeroArtefato($params);
            $numeroSequencial = $ultimNumeroArtefato->getNuSequencial();
            $sequencial = str_pad($numeroSequencial + 1, 4, '0', STR_PAD_LEFT) . '/' . date('Y');
            $params['nuArtefato'] = $sequencial;
            $searchDto = \Core_Dto::factoryFromData($params, 'search');
            $this->transformarMinutaDocumentoEletronico($searchDto);
            //atualiza o numero sequencial para o numero utilizado
            $this->atualizarSequencial($ultimNumeroArtefato);

            //altera o historico do artefato para criado e alterado
            $params['sqStatusArtefato'] = \Core_Configuration::getSgdoceStatusProduzida();
            $this->alterarHistoricoArtefato($params);
            $params['sqStatusArtefato'] = \Core_Configuration::getSgdoceStatusAssinada();
            $this->alterarHistoricoArtefato($params);
            $params['sqOcorrencia'] = \Core_Configuration::getSgdoceSqOcorrenciaCadastrar();
            $this->alterarHistoricoArtefato($params);
        }

        return TRUE;
    }

    /**
     * Método que faz o clone do artefato
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @return Sgdoce\Model\Entity\Artefato $artefatoClone
     */
    public function saveArtefatoClone ($artefato)
    {
        //clona o artefato
        $artefatoClone = clone $artefato;
        $this->getEntityManager()->persist($artefatoClone);
        $this->getEntityManager()->flush($artefatoClone);

        $this->saveArtefatoMinutaClone($artefato, $artefatoClone);
        $this->savePessoaArtefatoClone($artefato, $artefatoClone);
        $this->savePessoaInteressadoClone($artefato, $artefatoClone);
        $this->savePessoaAssinanteArtefatoClone($artefato, $artefatoClone);

        return $artefatoClone;
    }

    /**
     * Método que clona o artefato minuta
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @param Sgdoce\Model\Entity\Artefato $artefatoClone
     * @return Sgdoce\Model\Entity\Artefato $entityArtefatoMinutaClone
     */
    private function saveArtefatoMinutaClone ($artefato, $artefatoClone)
    {
        $entityArtefatoMinuta = $this->_getRepository('app:ArtefatoMinuta')->find($artefato->getSqArtefato());
        $entityArtefatoMinutaClone = clone $entityArtefatoMinuta;
        $entityArtefatoMinutaClone->setSqArtefato($artefatoClone);
        $this->getEntityManager()->persist($entityArtefatoMinutaClone);
        $this->getEntityManager()->flush($entityArtefatoMinutaClone);

        return $entityArtefatoMinutaClone;
    }

    /**
     * Método que clona a pessoa artefato
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @param Sgdoce\Model\Entity\Artefato $artefatoClone
     * @return Sgdoce\Model\Entity\Artefato $pessoaClone
     */
    private function savePessoaArtefatoClone ($artefato, $artefatoClone)
    {
        $criteria = array('sqArtefato' => $artefato->getSqArtefato());
        $pessoaArtefato = $this->getServiceLocator()->getService('PessoaArtefato')->findBy($criteria);
        if (is_array($pessoaArtefato)) {
            foreach ($pessoaArtefato as $pessoa) {
                $pessoaClone = clone $pessoa;
                $pessoaClone->setSqArtefato($artefatoClone);
                $this->getEntityManager()->persist($pessoaClone);
                $this->getEntityManager()->flush($pessoaClone);
            }
        }

        return $pessoaClone;
    }

    /**
     * Método que clona a pessoa interessado
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @param Sgdoce\Model\Entity\Artefato $artefatoClone
     * @return Sgdoce\Model\Entity\Artefato $entityInteressadaClone
     */
    private function savePessoaInteressadoClone ($artefato, $artefatoClone)
    {
        $criteria = array('sqArtefato' => $artefato->getSqArtefato());
        $entityInteressadaArtefato = $this->_getRepository('app:PessoaInteressadaArtefato')->findBy($criteria);
        if (is_array($entityInteressadaArtefato)) {
            foreach ($entityInteressadaArtefato as $entityInteressada) {
                $entityInteressadaClone = clone $entityInteressada;
                $entityInteressadaClone->setSqArtefato($artefatoClone);
                $this->getEntityManager()->persist($entityInteressadaClone);
                $this->getEntityManager()->flush($entityInteressadaClone);
            }
        }

        return $entityInteressadaClone;
    }

    /**
     * Método que clona a pessoa assinante
     * @param Sgdoce\Model\Entity\Artefato $artefato
     * @param Sgdoce\Model\Entity\Artefato $artefatoClone
     * @return Sgdoce\Model\Entity\Artefato $entityAssinanteClone
     */
    private function savePessoaAssinanteArtefatoClone ($artefato, $artefatoClone)
    {
        $criteria = array('sqArtefato' => $artefato->getSqArtefato());
        $entityAssinanteArtefato = $this->_getRepository('app:PessoaAssinanteArtefato')->findBy($criteria);
        if (is_array($entityAssinanteArtefato)) {
            foreach ($entityAssinanteArtefato as $entityAssinante) {
                $entityAssinanteClone = clone $entityAssinante;
                $entityAssinanteClone->setSqArtefato($artefatoClone);
                $this->getEntityManager()->persist($entityAssinanteClone);
                $this->getEntityManager()->flush($entityAssinanteClone);
            }
        }

        return $entityAssinanteClone;
    }

    public function alterarHistoricoArtefato ($params)
    {
        $dtoOption = array(
            'entity' => 'Sgdoce\Model\Entity\HistoricoArtefato',
            'mapping' => array(
                'sqStatusArtefato' => 'Sgdoce\Model\Entity\StatusArtefato',
                'sqUnidadeOrg' => 'Sgdoce\Model\Entity\VwUnidadeOrg',
                'sqArtefato' => 'Sgdoce\Model\Entity\Artefato',
                'sqPessoa' => 'Sgdoce\Model\Entity\VwPessoa',
                'sqOcorrencia' => 'Sgdoce\Model\Entity\Ocorrencia'
            )
        );

        $params['dtOcorrencia'] = new \Zend_Date();

        $dtoEntity = \Core_Dto::factoryFromData($params, 'entity', $dtoOption);
        $result = $this->getServiceLocator()->getService('VisualizarCaixaMinuta')->saveHistorico($dtoEntity);
        return $result;
    }

    /**
     *
     * @param integer $sqArtefato
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function inMyDashboard ($sqArtefato)
    {
        if (!$sqArtefato) {
            throw new \InvalidArgumentException('Values must not be empty.');
        }
        $sqPessoa        = (int)\Core_Integration_Sica_User::getPersonId();
        $sqPessoaDestino = (int)\Core_Integration_Sica_User::getUserUnit();
        $dtoSearch = \Core_Dto::factoryFromData(
                        array('sqArtefato' => $sqArtefato,
                              'sqPessoa' => $sqPessoa,
                              'sqPessoaDestino' => $sqPessoaDestino
                             ), 'search');
        return $this->_getRepository('app:VwUltimoTramiteArtefato')->inMyDashboard($dtoSearch);
    }

    /**
     *
     * @param $dtoSearch
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function isInconsistent ($dtoSearch)
    {
        return $this->_getRepository('app:Artefato')->isInconsistent($dtoSearch);
    }

    /**
     * @return Entity
     */
    public function getEntityDto( $params = array(), $options = array() )
    {
        if( !empty($options) ) {
            $dto = \Core_Dto::factoryFromData($params, 'entity', $options);
            return $dto->getEntity();
        } else {
            return $this->getEntityManager()->getPartialReference($this->_entityName, 0);
        }
    }
}