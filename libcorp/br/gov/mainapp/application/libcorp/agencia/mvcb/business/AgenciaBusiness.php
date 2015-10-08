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
namespace br\gov\mainapp\application\libcorp\agencia\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\agencia\valueObject\AgenciaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name AgenciaBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.agencia.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class AgenciaBusiness extends ParentBusiness
{
    /**
     * Insere os dados de Agencia
     *
     * @example AgenciaBusiness::save
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $agenciaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $agenciaVO = AgenciaValueObject::factory();
     *     $agenciaVO->setCoAgencia('1');
     *
     *     # efetua pesquisa
     *     $agenciaBusiness = AgenciaBusiness::factory();
     *     $agenciaBusiness->save($agenciaVO);
     * ?>
     * @endcode
     *
     * @param AgenciaValueObject $voAgencia
     * @return AgenciaValueObject
     * @throws BusinessException
     */
    public function saveAgencia (AgenciaValueObject $voAgencia)
    {
        try {
            $this->getModelPersist('libcorp')->save($voAgencia);
            return $voAgencia;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Exclui os dados de Agencia
     *
     * @example AgenciaBusiness::deleteAgencia
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $agenciaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $agenciaVO = AgenciaValueObject::factory();
     *     $agenciaVO->setSqAgencia(1);
     *
     *     # efetua exclusao
     *     $agenciaBusiness = AgenciaBusiness::factory();
     *     $agenciaBusiness->deleteAgencia($agenciaVO);
     * ?>
     * @endcode
     *
     * @param AgenciaValueObject $voAgencia
     * @throws BusinessException
     */
    public function deleteAgencia (AgenciaValueObject $voAgencia)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voAgencia);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os dados de Agencia
     *
     * @example AgenciaBusiness::updateAgencia
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $agenciaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $agenciaVO = AgenciaValueObject::factory();
     *     $agenciaVO->setSqAgencia(1);
     *
     *     # efetua exclusao
     *     $agenciaBusiness = AgenciaBusiness::factory();
     *     $agenciaBusiness->updateAgencia($agenciaVO);
     * ?>
     * @endcode
     *
     * @param AgenciaValueObject $voAgencia
     * @throws BusinessException
     */
    public function updateAgencia (AgenciaValueObject $voAgencia)
    {
        try {
            $voAgenciaTmp = $this->find($voAgencia->getSqAgencia());
            $voAgenciaTmp->loadData($this->keepUpdateData($voAgencia));
            $this->getModelPersist('libcorp')->update($voAgenciaTmp);
            return $voAgenciaTmp;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}