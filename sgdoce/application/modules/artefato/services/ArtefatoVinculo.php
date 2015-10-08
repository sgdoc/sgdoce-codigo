<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Artefato\Service;
use \Sgdoce\Model\Entity\Artefato as EntityArtefato;

/**
 * Classe para Service de Artefato Processo
 *
 * @package  Artefato
 * @category Service
 * @name      ArtefatoProcesso
 * @version  1.0.0
 */
class ArtefatoVinculo extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:ArtefatoVinculo';

    # usado para auxilo na montagem da arvore
    protected $_pais = array();
    protected $_paisFilhos = array();
    
    # verificando métodos que iram tramitar para a pessoa que solicitação a ação.
    protected $_isTramiteSolicitante = false;

    private $_serviceHistoricoArtefato = null;

    public function listGridVinculacaoPeca($dto)
    {
        return $this->_getRepository()->searchPageDto('listGridVinculacaoPeca', $dto);
    }

    public function listGridVinculacaoReferencia($dto)
    {
        return $this->_getRepository()->searchPageDto('listGridVinculacaoReferencia', $dto);
    }

    public function findVinculoArtefato($dto, array $notInVinculo = array())
    {
        return $this->_getRepository()->findVinculoArtefato($dto, $notInVinculo);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    	return $this->_getRepository()->findBy($criteria);
    }

    public function searchReferencia(\Core_Dto_Search $dto)
    {
         return $this->_getRepository()->searchReferencia($dto);
    }

    /**
     * @param integer $sqArtefatoFilho
     * @param integer $sqArtefatoPai
     * @return boolean
     * */
    public function deleteArtefatoVinculo ($sqArtefatoFilho, $sqArtefatoPai)
    {      
        $dtoDadosTramite = null;
        
        if( $this->_isTramiteSolicitante ) {            
            $dto = \Core_Dto::factoryFromData(array(
                'sqArtefato' => $sqArtefatoPai
            ), 'search');
            $rsSolicitacao = $this->getServiceLocator()
                                   ->getService('Solicitacao')
                                   ->getSolicitacaoAberta($dto);
            if( $rsSolicitacao ) {
                $rsSolicitacao = current($rsSolicitacao);            
                $dtoDadosTramite = \Core_Dto::factoryFromData($rsSolicitacao, 'search');
            }
        }
        
        $criteria = array(
            'sqArtefatoFilho' => $sqArtefatoFilho,
            'sqArtefatoPai'   => $sqArtefatoPai
        );

    	$entity        = $this->_getRepository()->findOneBy($criteria);
        $sqTipoVinculo = $entity->getSqTipoVinculoArtefato()->getSqTipoVinculoArtefato();

        parent::delete($entity->getSqArtefatoVinculo());

        $entity->getSqArtefatoPai()->setSqArtefatoPai(NULL);
        $entity->getSqArtefatoFilho()->setSqArtefatoFilho(NULL);

        $this->getEntityManager()->persist($entity->getSqArtefatoPai());
        $this->getEntityManager()->persist($entity->getSqArtefatoFilho());

        $this->_processTramite($entity->getSqArtefatoFilho(), $dtoDadosTramite);
        
        $this->finish();
        
        $this->_getRepository()->setOrderIn($entity->getSqArtefatoPai()->getSqArtefato());

        /* registra a desvinculacao do artefato */
        $this->_historicoVinculoDelete($criteria, $sqTipoVinculo);

        return TRUE;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado
     * @return boolean
     */
    public function findArtefatoVinculo(\Core_Dto_Search $dtoSearch)
    {
    	$criteria = array('sqArtefatoPai' => $dtoSearch->getSqArtefato()
    					  ,'sqTipoVinculoArtefato' => 3,'dtRemocaoVinculo' => NULL);
    	$return  = $this->_getRepository()->findBy($criteria);
    	if(count($return) == 1 && ($dtoSearch->getInOriginal() == '1')){
    		return FALSE;
    	}
    	return TRUE;
    }

    /**
     * Metódo que verifica se o modelo está cadastrado
     * @return boolean
     */
    public function findVinculo(\Core_Dto_Entity $dtoVinculo)
    {
    	$criteria = array('sqArtefatoPai' => $dtoVinculo->getSqArtefatoPai()->getSqArtefato()
    					  ,'sqArtefatoFilho' => $dtoVinculo->getSqArtefatoFilho()->getSqArtefato()
    			          ,'inOriginal' => 'FALSE'
    					  ,'sqTipoVinculoArtefato' => $dtoVinculo->getSqTipoVinculoArtefato()->getSqTipoVinculoArtefato()
    					 ,'dtRemocaoVinculo' => NULL);
    	$return  = $this->_getRepository()->findBy($criteria);
    	if(count($return) > 0){
			return TRUE;
    	}
    	return FALSE;
    }

    public function saveArtefatoVinculo ($dto)
    {
        /** @var \Sgdoce\Model\Entity\ArtefatoVinculo $artefatoVinculo */
        $artefatoVinculo = $this->_newEntity('app:ArtefatoVinculo');

        $artefatoVinculo->setSqArtefatoPai(
            $this->_getRepository('app:Artefato')->find($dto->getSqArtefatoPai()->getSqArtefato())
        );

        $artefatoVinculo->setSqArtefatoFilho(
            $this->_getRepository('app:Artefato')->find($dto->getSqArtefatoFilho()->getSqArtefato())
        );

        $artefatoVinculo->setDtVinculo(\Zend_Date::now());

        $artefatoVinculo->setInOriginal($dto->getInOriginal());

        $artefatoVinculo->setSqTipoVinculoArtefato(
            $this->_getRepository('app:TipoVinculoArtefato')
                 ->find($dto->getSqTipoVinculoArtefato()->getSqTipoVinculoArtefato())
        );
        
        $artefatoVinculo->setNuOrdem($dto->getNuOrdem());

        $this->getEntityManager()->persist($artefatoVinculo);

        $this->getEntityManager()->flush();

        return $artefatoVinculo;
    }

    public function verificaVinculoArfato(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->verificaVinculoArfato($dto);
    }

    public function verificaVinculoArfatoPai($dto)
    {
        return $this->_getRepository()->verificaVinculoArfatoPai($dto);
    }

    public function vinculoListGrid ($params, $withTotalRecord=TRUE)
    {
        # vincula a pesquisa ao usuario local e sua unidade
        self::_injectCredencial($params);
        
        # filtra o tipo de retorno conforme o tipo do documento base
        # Atentar para o tipo do documento base
        #          BASE -> Recupera
        # Regra-1: DOC  +  DOC
        # Regra-2: PROC +  PROC
        # Regra-3: PROC +  DOC
        $tipoArtefatoFiltro = array(
            # DOC + DOC
            \Core_Configuration::getSgdoceTipoArtefatoDocumento() => array(
                \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
            ),

            # PROC + PROC && PROC + DOC
            \Core_Configuration::getSgdoceTipoArtefatoProcesso() => array(
                \Core_Configuration::getSgdoceTipoArtefatoDocumento(),
                \Core_Configuration::getSgdoceTipoArtefatoProcesso()
            )
        );

        # atentar para o tipo de documento base informado
        # pela regra, só sao aceitos:
        # documento e processo
        $params['tipoArtefatoAceito'] = $tipoArtefatoFiltro[
        $this->_getRepository('app:Artefato')
            ->find((integer) $params['sqArtefatoParent'])
            ->getSqTipoArtefatoAssunto()
            ->getSqTipoArtefato()
            ->getSqTipoArtefato()
        ];

        $params['tipoArtefatoAceito'] = implode(',', $params['tipoArtefatoAceito']);

        $filter = \Core_Dto::factoryFromData($params, 'search');

        return
        $this->_getRepository()
             ->searchPageDto('vinculoListGrid', $filter, $withTotalRecord);
    }

    public function anexar ($params)
    {
        $this->rule_11746($params['parent'], $params['child']);

        $this->_vincular(\Core_Configuration::getSgdoceTipoVinculoArtefatoAnexacao(), $params);

        $this->_historicoVinculoAnexar(
            array(
                'sqArtefatoPai'   => $params['parent']->getSqArtefato(),
                'sqArtefatoFilho' => $params['child']->getSqArtefato(),
            )
        );
    }

    public function apensar ($params)
    {
        $this->rule_11746($params['parent'], $params['child']);

        $this->_vincular(\Core_Configuration::getSgdoceTipoVinculoArtefatoApensacao(), $params);

        $this->_historicoVinculoApensar(
            array(
                'sqArtefatoPai'   => $params['parent']->getSqArtefato(),
                'sqArtefatoFilho' => $params['child']->getSqArtefato(),
            )
        );
    }

    public function inserirPeca ($params, $tipoVinculo = null)
    {
        try {

            if( is_null($tipoVinculo) ) {
                $tipoVinculo = \Core_Configuration::getSgdoceTipoVinculoArtefatoInsercao();
            }

            $TParent   = $params['parent']->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
            $TProcesso = \Core_Configuration::getSgdoceTipoArtefatoProcesso();

            $TCild      = $params['child']->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
            $TDocumento = \Core_Configuration::getSgdoceTipoArtefatoDocumento();

            # inserir peca soh pode ser entre:
            #    1º    ->    2º
            #
            if (! ($TParent == $TProcesso) && ($TCild == $TDocumento)) {
                throw new \Exception('** criar código para msg que determine tipos incopativeis ** ');
            }

            $this->_vincular($tipoVinculo, $params);

            $this->_historicoVinculoInserirPeca(
                array(
                    'sqArtefatoPai'   => $params['parent']->getSqArtefato(),
                    'sqArtefatoFilho' => $params['child']->getSqArtefato(),
                ), $tipoVinculo
            );
        } catch (\Exception $exc) {
            dumpd( $exc);
        }
    }

    public function desanexar ($params)
    {
        try{
            # tramitar para solicitante
            $this->_isTramiteSolicitante = true;
            
            # valida o perfil do usuario
            $this->rule_12100();

            /* desvincula a anexacao existente entre documentos informados em params */
            $this->_desvincular($params);

            # historico realizado em deleteArtefatoVinculo

        } catch (\Exception $exp) {
            throw $exp;
        }
    }

    public function desapensar ($params)
    {
        try{
            # tramitar para solicitante
            $this->_isTramiteSolicitante = true;
            
            /**
             * se a data do vinculo for maior que a data do ultimo tramite do 
             * pai ou se não estiver na área de trabalho do usuário PODE desfazer, caso contrario NÃO PODE
             */
            $this->_rule_desapensar($params['parent'], $params['child']);


            /* desvincula a apensação existente entre documentos informados em params */
            $this->_desvincular($params);

            # historico realizado em deleteArtefatoVinculo

        } catch (\Exception $exp) {
            throw $exp;
        }
    }

    public function removerPeca ($params)
    {
        try {
            # tramitar para solicitante
            $this->_isTramiteSolicitante = true;
            
            $this->rule_12168($params['parent'], $params['child']);
            /**
             * se a data do vinculo for maior que a data do ultimo tramite do pai
             * PODE desfazer, caso contrario NÃO PODE
             */
            $this->rule_($params['parent'], $params['child']);

            $this->_desvincular($params);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param integer $sqArtefato
     */
    public function mostarArvore ($sqArtefato)
    {
        $data = $this->_getRepository()->arvoreVinculo($sqArtefato)->getResult();
        return $this->balance($data);
    }

    /**
     * @param integer $sqProcesso
     */
    public function mostarArvoreMigracao($dto)
    {
        try {
            $data = $this->_getRepository()->arvoreVinculoMigracao($dto)->getResult();
            return $this->balance($data);
        } catch( \Exception $e ) {
            $msgId = '';
            switch( $e->getCode() ){
                case 21000:
                    if( strpos('Cardinality violation', $e->getMessage()) ) {
                        $msgId = 'MN180';
                    } else {
                        $msgId = 'MN181';
                    }
                    break;

                default:
                    $msgId = 'MN181';
            }

            $this->getMessaging()->addErrorMessage(\Core_Registry::getMessage()->translate($msgId), 'User');
            $this->getMessaging()->dispatchPackets();
        }
        return false;
    }

    private function _vincular ($type, $params)
    {
        $this->typeMustBeArtefato($params['parent']);
        $this->typeMustBeArtefato($params['child']);

        $connection = $this->getEntityManager()->getConnection();

        $connection->beginTransaction();

        $dto = \Core_Dto::factoryFromData(array(
            'sqArtefatoPai' => $params['parent']->getSqArtefato()
        ), 'search');
        
        $maxNuOrdem = $this->_getRepository('app:ArtefatoVinculo')
                        ->getMaxNuOrderByParent($dto);
        
        $nuOrdem = ++$maxNuOrdem;
        
        $arrData = array(
            'sqArtefatoPai'         => $params['parent']->getSqArtefato(),
            'sqArtefatoFilho'       => $params['child']->getSqArtefato(),
            'sqTipoVinculoArtefato' => $type,
        );
        
        $dtoSave = self::facadeCreateDto($arrData, 'entity');
        $dtoSave->setNuOrdem($nuOrdem);
        
        try {

            # verifica se o vinculo já existe
            $assert = $this->verificaVinculoArfato(self::facadeCreateDto($arrData, 'search'));

            if (count($assert)) {
                throw new \Exception('Este artefato já fora anexado');
            }

            # registra o viculo
            $this->saveArtefatoVinculo($dtoSave);

            $this->getEntityManager()->flush();

            $connection->commit();

        } catch (\Exception $e) {

            $connection->rollback();

            throw $e;
        }
    }

    /**
     * @param Entity[]
     * */
    private function _desvincular ($params)
    {
        $this->typeMustBeArtefato($params['parent']);
        $this->typeMustBeArtefato($params['child']);

        $this->deleteArtefatoVinculo(
            $params['child']->getSqArtefato(),
            $params['parent']->getSqArtefato()
        );

        $this->getEntityManager()->flush();
    }

    /**
     *
     * Tramite documentos desvinculados pelo SGI para sua caixa para que ele dê destino
     * ao documento que ele desvinculou
     *
     * @param \Sgdoce\Model\Entity\Artefato $entityArtefato
     * @return \Artefato\Service\ArtefatoVinculo
     */
    private function _processTramite(EntityArtefato $entityArtefato, \Core_Dto_Search $dtoDadosTramite = null)
    {
//        if (! \Core_Registry::get('isUserSgi')) {
//            return $this;
//        }

        $sqArtefato = $entityArtefato->getSqArtefato();

        $entityUltimoTramite = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($sqArtefato);

        $sqPessoaLogada  = (integer) \Core_Integration_Sica_User::getPersonId();
        $sqUnidadeLogada = (integer) \Core_Integration_Sica_User::getUserUnit();
        $serviceTramite  = $this->getServiceLocator()->getService('TramiteArtefato');

        // Verifica se foi aberto uma solicitação para o artefato para realizar a operação.
        if( $this->_isTramiteSolicitante
            && !is_null($dtoDadosTramite) ) {
            $sqPessoaLogada = $dtoDadosTramite->getSqPessoa();
            $sqUnidadeLogada = $dtoDadosTramite->getSqUnidadeOrg();
        }
        
        $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
        /**
         * Se não tem ultimo Tramite é porque o artefato ainda não foi corrigido.
         * Verificar, mesmo assim, se tem tramite pois a view de ultimo tramite faz join
         * que pode não retornar registro caso documento não tenha sido corrido
         */
        if (!$entityUltimoTramite) {
            $dataTramite = array(
                'sqArtefato'             => $sqArtefato,
                'sqPessoaDestino'        => $sqUnidadeLogada,
                'sqPessoaDestinoInterno' => $sqPessoaLogada,
                'sqUnidadeOrgTramite'    => $sqUnidadeLogada,
                'sqStatusTramite'        => \Core_Configuration::getSgdoceStatusTramiteTramitado(),
                'dtTramite'              => \Zend_Date::now(),
                'sqPessoaTramite'        => $sqPessoaLogada,
                'inImpresso'             => TRUE,
                'nuTramite'              => $serviceTramite->getNextTramiteNumber($dtoSearchArtefato)
            );

            $entityDtoTramite = $serviceTramite->montaEntidateTramite($dataTramite);
            $entityTramite = $serviceTramite->save($entityDtoTramite);
        }else{
            $sqPessoaRecebimento = ($entityUltimoTramite->getSqPessoaRecebimento()) ? $entityUltimoTramite->getSqPessoaRecebimento()->getSqPessoa() : NULL;
            $sqPessoaDestino     = ($entityUltimoTramite->getSqPessoaDestino()) ? $entityUltimoTramite->getSqPessoaDestino()->getSqPessoa() : NULL;

            /**
             * caso o ultimo tramite do artefato não for da pessoa logada
             * deve-se registrar um tramite para pessoa logada (SGI) para que este
             * possa encaminhar para quem solicitou a desanexação
             */
            if ($sqPessoaRecebimento != $sqPessoaLogada || $sqPessoaDestino != $sqUnidadeLogada ) {

                $dtoSearchArtefato = \Core_Dto::factoryFromData(array('sqArtefato' => $sqArtefato), 'search');
                $dataTramite = array(
                    'sqArtefato'             => $sqArtefato,
                    'sqPessoaDestino'        => $sqUnidadeLogada,
                    'sqPessoaDestinoInterno' => $sqPessoaLogada,
                    'sqUnidadeOrgTramite'    => $sqUnidadeLogada,
                    'sqStatusTramite'        => \Core_Configuration::getSgdoceStatusTramiteTramitado(),
                    'dtTramite'              => \Zend_Date::now(),
                    'sqPessoaTramite'        => $sqPessoaLogada,
                    'inImpresso'             => TRUE,
                    'nuTramite'              => $serviceTramite->getNextTramiteNumber($dtoSearchArtefato)
                );

                $entityDtoTramite = $serviceTramite->montaEntidateTramite($dataTramite);
                $entityTramite = $serviceTramite->save($entityDtoTramite);
            }
        }
        return $this;
    }




    /*
     * injeta as crendencias do usuario que esta reallizando a operacao
     *
     * @param array
     * */
    private static function _injectCredencial (&$data)
    {        
        $data['sqPessoa']            = \Core_Integration_Sica_User::getPersonId();
        $data['sqUnidadeOrg']        = \Core_Integration_Sica_User::getUserUnit();
    }

    /*
     * @param \Sgdoce\Model\Entity\Artefato
     * @throws Exception
     * */
    public function typeMustBeArtefato ($elm)
    {
        if (! ($elm instanceof \Sgdoce\Model\Entity\Artefato)) {
            throw new \Exception('MN132');
        }
    }

    /*
     * Um artefato só poderá ser apensado / anexado a outro do mesmo tipo.
     * Ex: Só poderão ser anexados/apensados documentos a documentos,
     * processos a processos e dossiês a dossiês.
     *
     * @param \Sgdoce\Model\Entity\Artefato $parent
     * @param \Sgdoce\Model\Entity\Artefato $child
     * @throw Exception
     * */
    public function rule_11746 (EntityArtefato $parent, EntityArtefato $child)
    {
        if(
            $parent->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato() !=
            $child->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato()
        ) {
            throw new \Exception('MN107');
        }
    }

    /*
     * Apenas o perfil de administrador do sistema (SGI) poderá executar a ação,
     * mediante solicitação justificada da chefia do setor interessado.
     * */
    public function rule_12100 ()
    {
        $filter = \Core_Dto::factoryFromData(
            (array) \Core_Integration_Sica_User::get(), 'search'
        );

        if (! $this->_getRepository('app:VwUsuario')->isUserSgi($filter)) {
            throw new \Exception('MN156', 9999); // falta de permissao
        }
    }

    public function rule_12168 (EntityArtefato $parent, EntityArtefato $child)
    {
        $entityArtefatoVinculo = $this->findOneBy(array(
            'sqArtefatoPai' => $parent->getSqArtefato(),
            'sqArtefatoFilho' => $child->getSqArtefato()
        ));

        if (! $entityArtefatoVinculo instanceof \Sgdoce\Model\Entity\ArtefatoVinculo) {
            throw new \Core_Exception_ServiceLayer("Não foi possível recuperar a informação do vinculo. Tente novamente mais tarde");
        }

        if (!$entityArtefatoVinculo->getSqTipoVinculoArtefato()->getInPermiteDesvinculacao()) {
            throw new \Core_Exception_ServiceLayer_Verification("Este Vinculo não pode ser desfeito");
        }
    }

    public function rule_ (EntityArtefato $parent, EntityArtefato $child)
    {
        $entityArtefatoVinculo = $this->findOneBy(array(
            'sqArtefatoPai' => $parent->getSqArtefato(),
            'sqArtefatoFilho' => $child->getSqArtefato()
        ));

        if (! $entityArtefatoVinculo instanceof \Sgdoce\Model\Entity\ArtefatoVinculo) {
            throw new \Core_Exception_ServiceLayer("Não foi possível recuperar a informação do vinculo. Tente novamente mais tarde");
        }

        $entityUltimoTramiteParent = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($parent->getSqArtefato());

        $dtTramiteParent = $entityUltimoTramiteParent->getDtTramite();
        $dtVinculo       = $entityArtefatoVinculo->getDtVinculo();

        /* Returns -1 if earlier, 0 if equal and 1 if later.
         *
         * se dtTramiteParent > dtVinculo não pode desfazer
         */
        if ($dtTramiteParent->compare($dtVinculo) !== -1 && !\Zend_Registry::get('isUserSgi')) {
            throw new \Core_Exception_ServiceLayer_Verification("A data do vinculo é anterior à data do último trâmite. Este vinculo só pode ser desfeito pelo SGI");
        }
    }
    
    public function _rule_desapensar(EntityArtefato $parent, EntityArtefato $child)
    {
        $entityArtefatoVinculo = $this->findOneBy(array(
            'sqArtefatoPai' => $parent->getSqArtefato(),
            'sqArtefatoFilho' => $child->getSqArtefato()
        ));

        if (! $entityArtefatoVinculo instanceof \Sgdoce\Model\Entity\ArtefatoVinculo) {
            throw new \Core_Exception_ServiceLayer("Não foi possível recuperar a informação do vinculo. Tente novamente mais tarde");
        }

        $entityUltimoTramiteParent = $this->_getRepository('app:VwUltimoTramiteArtefato')->find($parent->getSqArtefato());

        $dtTramiteParent = $entityUltimoTramiteParent->getDtTramite();
        $dtVinculo       = $entityArtefatoVinculo->getDtVinculo();
        // Se o artefato pai estiver na área de trabalho do usuário, poderá prosseguir com a operação.
        $isInMyDashboard = $this->getServiceLocator()
                                ->getService('Artefato')
                                ->inMyDashboard($parent->getSqArtefato());
                
        if( !$isInMyDashboard ){            
            /* Returns -1 if earlier, 0 if equal and 1 if later.
             *
             * se dtTramiteParent > dtVinculo não pode desfazer
             */
            if ($dtTramiteParent->compare($dtVinculo) !== -1 && !\Zend_Registry::get('isUserSgi')) {
                throw new \Core_Exception_ServiceLayer_Verification("A data do vinculo é anterior à data do último trâmite. Este vinculo só pode ser desfeito pelo SGI");
            }    
        }    
    }

    public static function facadeCreateDto ($params, $typeDto)
    {
        $options = array(
            'entity' => 'Sgdoce\Model\Entity\ArtefatoVinculo',
            'mapping' => array(
                'sqArtefatoPai'         => array('sqArtefato' => 'Sgdoce\Model\Entity\Artefato'),
                'sqArtefatoFilho'       => array('sqArtefato' => 'Sgdoce\Model\Entity\Artefato'),
                'sqTipoVinculoArtefato' => 'Sgdoce\Model\Entity\TipoVinculoArtefato'
            )
        );

        $arrData = array();
        $arrData['sqArtefatoPai']         = $params['sqArtefatoPai'];
        $arrData['sqArtefatoFilho']       = $params['sqArtefatoFilho'];
        $arrData['sqTipoVinculoArtefato'] = $params['sqTipoVinculoArtefato'];
        $arrData['inOriginal']            = TRUE;
        $arrData['dtVinculo']             = \Zend_Date::now();

        return \Core_Dto::factoryFromData($arrData, $typeDto, $options);
    }

    /**
     * @param array[] $nodes  <indica a fonte de dados>
     * @parma array $node  <indica o elemento que sera buscado>
     * @return array[]
     * */
    function balance ($nodes)
    {
        foreach( $nodes as $item ){

            if (is_null($item['sqArtefatoFilho'])) {
                $item['sqArtefatoFilho'] = $item['sqArtefatoPai'];
                $item['sqArtefatoPai']   = null;
            }

            if (is_null($item['sqArtefatoPai'])) {
                $this->_pais[] = $item;
            }

            if (! is_null($item['sqArtefatoPai']) && isset( $this->_paisFilhos[$item['sqArtefatoPai']])) {

                $this->_paisFilhos[$item['sqArtefatoPai']][$item['sqArtefatoFilho']] = $item;

            } elseif(! is_null($item['sqArtefatoPai']) && !isset( $this->_paisFilhos[$item['sqArtefatoPai']])) {

                $this->_paisFilhos[$item['sqArtefatoPai']] = array();

                $this->_paisFilhos[$item['sqArtefatoPai']][$item['sqArtefatoFilho']] = $item;

            }
        }

        reset($nodes);
        $vector = array();
        $parent = current($nodes);
        $vector[$parent['sqArtefatoPai']] = $parent;

        foreach ($this->_pais as $item) {
            $vector[$item['sqArtefatoFilho']]['filhos'] = $this->getChildren($item['sqArtefatoFilho']);
        }

       return $vector;
    }

    /**
     * @param integer $idPai
     */
    function getChildren ($idPai)
    {
        if (isset($this->_paisFilhos[$idPai])) {

            $arrFilhos = $this->_paisFilhos[$idPai];

            if (count($arrFilhos)) {

                foreach ($arrFilhos as $key => $item) {
                    $arrFilhos[$item['sqArtefatoFilho']]['filhos'] = $this->getChildren($item['sqArtefatoFilho']);
                }
            }

            return $arrFilhos;

        } else {
            return array();
        }
    }


    public function searchDocumentsFirstPiece(\Core_Dto_Search $dto)
    {
        $sqArtefato = $dto->getExtraParam();
        
        $rsPesArtOrigem = $this->_getRepository('app:PessoaArtefato')->findBy(array(
            'sqArtefato' => $sqArtefato,
            'sqPessoaFuncao' => \Core_Configuration::getSgdocePessoaFuncaoOrigem()
        ));
        
        $entPesArtOrigem = current($rsPesArtOrigem);
        $isExterno      = !$entPesArtOrigem->getStProcedencia();
        
        $configs = \Core_Registry::get('configs');
        $dataEntradaProducao = $configs['dataEntradaProducao'];
                
        $entArtefato = $this->_getRepository('app:Artefato')->find($sqArtefato);
        
        $dtCadastro = $entArtefato->getDtCadastro();
        $dtEntradaProducao = new \Zend_Date($dataEntradaProducao);
        
        $isLegado = false;
        
        if( $dtEntradaProducao->compare($dtCadastro) ){
            $isLegado = true;
        }
                
        $data = $this->_getRepository()->searchDocumentsToFirstPiece($dto, $isExterno, $isLegado);

        $sqTipoArtefato = $dto->getSqTipoArtefato();

        $field = "nuDigital";
        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {
            $field = "nuArtefato";
        }

        $result = array();
        foreach ($data as $value) {
            $result[$value['sqArtefato']] = $value[$field];
        }
        return $result;
    }


    private function _historicoVinculoAnexar ($vinculo)
    {
        $sqOcorencia = \Core_Configuration::getSgdoceSqOcorrenciaIncluirVinculacao();

        $msgHistorico = $this->_getMessageVinculacaoHistoricoVinculo($vinculo, 'anexado');

        # registra movimentacao no pai
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'  => $vinculo['sqArtefatoPai'],
                'vinculo'   => $vinculo['sqArtefatoFilho'],
                'ocorrencia'=> $sqOcorencia,
                'mensagem'  => $msgHistorico['parent']
            )
        );

        # registra movimentacao no filho
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'  => $vinculo['sqArtefatoFilho'],
                'vinculo'   => $vinculo['sqArtefatoPai'],
                'ocorrencia'=> $sqOcorencia,
                'mensagem'  => $msgHistorico['child']
            )
        );
    }

    private function _historicoVinculoApensar ($vinculo)
    {
        $sqOcorencia = \Core_Configuration::getSgdoceSqOcorrenciaIncluirVinculacao();
        $msgHistorico = $this->_getMessageVinculacaoHistoricoVinculo($vinculo, 'apensado');

        # registra movimentacao no pai
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoPai'],
                'vinculo'    => $vinculo['sqArtefatoFilho'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['parent']
            )
        );

        # registra movimentacao no filho
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoFilho'],
                'vinculo'    => $vinculo['sqArtefatoPai'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['child']
            )
        );
    }

    private function _historicoVinculoInserirPeca ($vinculo, $tipoVinculo)
    {
        $acao = 'adicionado';

        if( $tipoVinculo == \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao() ) {
            $acao = \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao();
        }

        $sqOcorencia  = \Core_Configuration::getSgdoceSqOcorrenciaIncluirVinculacao();
        $msgHistorico = $this->_getMessageVinculacaoHistoricoVinculo($vinculo, $acao);

        # registra movimentacao no pai
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoPai'],
                'vinculo'    => $vinculo['sqArtefatoFilho'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['parent']
            )
        );

        # registra movimentacao no filho
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoFilho'],
                'vinculo'    => $vinculo['sqArtefatoPai'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['child']
            )
        );
    }

    private function _historicoVinculoDelete ($vinculo, $action)
    {
        $sqOcorencia  = \Core_Configuration::getSgdoceSqOcorrenciaExcluirVinculacao();
        $msgHistorico = $this->_getMessageVinculacaoHistoricoVinculo($vinculo, $action);

        # registra movimentacao no pai
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoPai'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['parent']
            )
        );

        # registra movimentacao no filho
        $this->_historicoVinculoRegistrar(
            array(
                'artefato'   => $vinculo['sqArtefatoFilho'],
                'ocorrencia' => $sqOcorencia,
                'mensagem'   => $msgHistorico['child']
            )
        );
    }

    private function _historicoVinculoRegistrar ($params)
    {
        try{
            $this->_getHistoricoArtefatoService()
                 ->registrar($params['artefato'],$params['ocorrencia'],$params['mensagem']);
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    private function _getMessageVinculacaoHistoricoVinculo($arrSqArtefato, $action)
    {

//        O 'tpdoc' 'docfilho' foi 'action' // historico pai
//        Este 'tpdoc' foi 'action' 'do' documento 'docpai' // historico filho
//
//        O %s %s foi %s
//        Este %s foi %s %s %s %s

        $dataParent = $this->_getDataMessageHistoricoVinculo($arrSqArtefato['sqArtefatoPai']);
        $dataChild  = $this->_getDataMessageHistoricoVinculo($arrSqArtefato['sqArtefatoFilho']);

        $currentDate = \Zend_Date::now()->toString('d/MM/YYYY H:m:s');

        if (is_numeric($action)) {
            $action = $this->_getActionMessage($action);
            $preposicao = 'do';
        }else{
            $preposicao = 'ao';
        }

        $msg['parent']  = $this->_getHistoricoArtefatoService()
                           ->getMessage('MH013',$dataChild['tpArtefato'],$dataChild['nuArtefato'],
                                                $action, $currentDate);
        $msg['child']   = $this->_getHistoricoArtefatoService()
                           ->getMessage('MH014',$dataChild['tpArtefato'], $action,
                                                $preposicao, $dataParent['tpArtefato'],
                                                $dataParent['nuArtefato'], $currentDate);

        return $msg;
    }

    private function _getDataMessageHistoricoVinculo($sqArtefato)
    {
        $serviceProcesso = $this->getServiceLocator() ->getService('Processo');
        $repoArtefato    = $this->_getRepository('app:Artefato');
        $entityArtefato  = $repoArtefato->find($sqArtefato);
        $sqTipoArtefato  = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
        $noTipoArtefato  = strtolower($entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getNoTipoArtefato());

        if ($sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
            $nuArtefato = $serviceProcesso->formataProcessoAmbitoFederal($entityArtefato);
        }else{
            $nuArtefato = $entityArtefato->getNuDigital()->getNuEtiqueta(TRUE);
        }

        return array(
            'nuArtefato' => $nuArtefato,
            'tpArtefato' => $noTipoArtefato
        );
    }

    private function _getHistoricoArtefatoService()
    {
        if(null === $this->_serviceHistoricoArtefato){
            $this->_serviceHistoricoArtefato = $this->getServiceLocator() ->getService('HistoricoArtefato');
        }

        return $this->_serviceHistoricoArtefato;
    }

    private function _getActionMessage($sqTipoVinculo)
    {
        $arrTipoVinculo = array(
            \Core_Configuration::getSgdoceTipoVinculoArtefatoApensacao() => 'desapensado',
            \Core_Configuration::getSgdoceTipoVinculoArtefatoAnexacao()  => 'desanexado',
            \Core_Configuration::getSgdoceTipoVinculoArtefatoInsercao()  => 'removido',
            \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()  => 'adicionado como primeira peça'
        );

        if (!isset($arrTipoVinculo[$sqTipoVinculo])) {
            trigger_error('Nenhuma entrada para ação de desvinculação encontrada', E_USER_ERROR);
        }

        return $arrTipoVinculo[$sqTipoVinculo];
    }

    public function getFirstPiece( \Core_Dto_Search $dto )
    {
        $data = $this->_getRepository('app:ArtefatoVinculo')->findBy(array(
            'sqArtefatoPai' => $dto->getSqArtefato(),
            'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
        ));
        return current($data);
    }

    public function hasVinculoSigiloso ($sqArtefato)
    {
        return $this->_getRepository()->hasVinculoSigiloso ($sqArtefato);
    }
    
    /**
     * 
     * @param integer $sqArtefatoVinculo
     * @param string $op
     */
    public function ordenar( $sqArtefatoVinculo, $op )
    {
        $entityCurrent      = $this->find($sqArtefatoVinculo);
        $sqArtefatoParent   = $entityCurrent->getSqArtefatoPai()->getSqArtefato();
        $nuOrdemCurrent     = $entityCurrent->getNuOrdem();
        $nuOrdemPrevius     = null;
        
        if( $op == 'up' ) {
            $nuOrdemPrevius = $nuOrdemCurrent - 1;
        } else if( $op == 'down' ) {
            $nuOrdemPrevius = $nuOrdemCurrent + 1;
        } else {
            return false;
        }        
        
        $entityChange = $this->findBy(array(
            'sqArtefatoPai' => $sqArtefatoParent,
            'nuOrdem' => $nuOrdemPrevius
        ));
        $entityChange = current($entityChange);
        
        $entityChange->setNuOrdem($nuOrdemCurrent);
        $entityCurrent->setNuOrdem($nuOrdemPrevius);
        
        $this->getEntityManager()->persist($entityChange);
        $this->getEntityManager()->persist($entityCurrent);
        
        return $this->getEntityManager()->flush();
    }
}