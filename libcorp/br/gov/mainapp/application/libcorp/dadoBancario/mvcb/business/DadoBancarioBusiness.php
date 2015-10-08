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
namespace br\gov\mainapp\application\libcorp\dadoBancario\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\dadoBancario\valueObject\DadoBancarioValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name DadoBancarioBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.dadoBancario.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */

class DadoBancarioBusiness extends ParentBusiness
{
    /**
     * Salva os Dados Bancarios
     *
     * @example DadoBancarioBusiness::saveDadosBancarios
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $dadosBancariosVO   = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $dadosBancariosVO = AgenciaValueObject::factory();
     *     $dadosBancariosVO->setSqPessoa(1);
     *
     *     # efetua inclusao
     *     $dadosBancariosBusiness = DadoBancarioBusiness::factory();
     *     $dadosBancariosBusiness->saveDadosBancarios($dadosBancariosVO);
     * ?>
     * @endcode
     *
     * @param DadoBancarioValueObject $voDadoBancario
     * @return DadoBancarioValueObject
     * @throws BusinessException
     */
    public function saveDadoBancario (DadoBancarioValueObject $voDadoBancario)
    {
        try {
            $this->getModelPersist('libcorp')->save($voDadoBancario);
            return $voDadoBancario;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Exclui os Dados Bancarios
     *
     * @example DadoBancarioBusiness::deleteDadosBancarios
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $dadosBancariosVO   = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $dadosBancariosVO = AgenciaValueObject::factory();
     *     $dadosBancariosVO->setSqDadoBancario(1);
     *
     *     # efetua exclusao
     *     $dadosBancariosBusiness = DadoBancarioBusiness::factory();
     *     $dadosBancariosBusiness->deleteDadosBancarios($dadosBancariosVO);
     * ?>
     * @endcode
     *
     * @param DadoBancarioValueObject $voDadoBancario
     * @throws BusinessException
     */
    public function deleteDadoBancario (DadoBancarioValueObject $voDadoBancario)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voDadoBancario);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os Dados Bancarios
     *
     * @example DadoBancarioBusiness::updateDadosBancarios
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $dadosBancariosVO   = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $dadosBancariosVO = AgenciaValueObject::factory();
     *     $dadosBancariosVO->setSqDadoBancario(1);
     *
     *     # efetua atualizacao
     *     $dadosBancariosBusiness = DadoBancarioBusiness::factory();
     *     $dadosBancariosBusiness->updateDadosBancarios($dadosBancariosVO);
     * ?>
     * @endcode
     *
     * @param DadoBancarioValueObject $voDadoBancario
     * @return DadoBancarioValueObject
     * @throws BusinessException
     */
    public function updateDadoBancario (DadoBancarioValueObject $voDadoBancario)
    {
        try {
            $voTmp = self::factory(NULL, 'libcorp')->find($voDadoBancario->getSqDadoBancario());
            BusinessException::throwsExceptionIfParamIsNull($voTmp->getSqDadoBancario(), 'O identificador do dado bancário não foi informado');

            $voDadoBancario->copySaveObjectData($voTmp);

            $this->getModelPersist('libcorp')
                 ->update($voDadoBancario);

            return $voDadoBancario;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}