<?php
/*
 * Copyright 2011 ICMBio
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
namespace br\gov\mainapp\application\libcorp\integracaoPessoaInfoconv\mvcb\business;

use br\gov\sial\core\lang\Date,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\integracaoPessoaInfoconv\valueObject\IntegracaoPessoaInfoconvValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name IntegracaoPessoaInfoconvBusiness
  * @package br.gov.mainapp.application.libcorp.integracaoPessoaInfoconv.mvcb
  * @subpackage business
  * @author carloss
  * @version $Id$
  * */
class IntegracaoPessoaInfoconvBusiness extends ParentBusiness
{
    /**
     * @var string
     */
    const INVALID_INFORMATION = 'Ocorreram inconsistências ao informar o registro.';
    
    /**
     * @var string
     */
    const REQUIRED_SQ_PESSOA = 'Na atualização é obrigatório informar o ID da Pessoa a ser alterada.';

    /**
     * Insere os dados de Pessoa Fisica ou Juridica
     * - Dados Obrigatórios : sqPessoa
     * - Dados Validadados  : se ( dtIntegracao )
     *                        então ( não haverá txJustificativa e sqPessoaAutora )
     *
     * @example IntegracaoPessoaInfoconvBusiness::saveIntegracaoPessoaInfoconv
     * @code
     * <?php
     *     # cria filtro usado pela integracaoPessoaInfoconv
     *     $integracaoPessoaInfoconvVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     $integracaoPessoaInfoconvVO = IntegracaoPessoaInfoconvValueObject::factory();
     *     $integracaoPessoaInfoconvVO->setSqPessoa('012345');
     *
     *     # efetua pesquisa
     *     $integracaoPIBusiness = IntegracaoPessoaInfoconvBusiness::factory();
     *     $integracaoPIBusiness->saveOrUpdateIntegracaoPessoaInfoconv( $integracaoPessoaInfoconvVO );
     * ?>
     * @endcode
     *
     * @param IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv
     * @return IntegracaoPessoaInfoconvValueObject
     * @throws BusinessException
     */
    public function save (
            IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv )
    {
        try {

            # Efetua validacao para salvar dados
            $this->_validateIntegracaoPessoaInfoconv( $voIntegracaoPessoaInfoconv );

            $sqPessoa = $this->_getPessoaByIntegracaoPessoaInfoconv( $voIntegracaoPessoaInfoconv );
            if ( empty( $sqPessoa ) ) {
                $this->getModelPersist('libcorp')->save( $voIntegracaoPessoaInfoconv );
            } else {
                $this->getModelPersist('libcorp')->update( $voIntegracaoPessoaInfoconv );
            }
            return $voIntegracaoPessoaInfoconv;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * recupera registros da integracaoPessoaInfoconv apartir de uma pessoa
     *
     * @param IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv
     * @return \br\gov\mainapp\application\infoconv\pessoa\valueObject\PessoaValueObject
     */
    private function _getPessoaByIntegracaoPessoaInfoconv ( IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv )
    {
        $vo = IntegracaoPessoaInfoconvValueObject::factory();
        $vo->setSqPessoa( $voIntegracaoPessoaInfoconv->getSqPessoa() );

        $rs = $this->getModelPersist('libcorp')
                ->findByParam( $vo )->getValueObject();

        return $rs->getSqPessoa();
    }

    /**
     * Efetua a validação
     * @param IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv
     * @throws BusinessException
     */
    private function _validateIntegracaoPessoaInfoconv ( 
            IntegracaoPessoaInfoconvValueObject $voIntegracaoPessoaInfoconv )
    {
        try {
            if( trim($voIntegracaoPessoaInfoconv->getSqPessoa()) ) {

                BusinessException::throwsExceptionIfParamIsNull(
                        trim($voIntegracaoPessoaInfoconv->getSqPessoa()),
                        self::REQUIRED_SQ_PESSOA);
            }

            if($voIntegracaoPessoaInfoconv->getDtIntegracao()) {

                # Efetua sanitizacao de datas
                $dateFromVo = $voIntegracaoPessoaInfoconv->getDtIntegracao();
                $voIntegracaoPessoaInfoconv->setDtIntegracao(
                        Date::factory($dateFromVo, 'd/m/Y')->output() );
                //
                $voIntegracaoPessoaInfoconv->setTxJustificativa("");
            } else if( trim( $voIntegracaoPessoaInfoconv->getTxJustificativa()) ) {

                $voIntegracaoPessoaInfoconv->setDtIntegracao(null);
            }
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}