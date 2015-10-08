<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\mvcb\business;
use br\gov\sial\core\mvcb\model\ModelAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\mvcb\controller\ControllerAbstract,
    br\gov\sial\core\mvcb\business\exception\BusinessException;

/**
 * SIAL
 *
 * Superclasse da Camada de negócio.
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage business
 * @name BusinessCrudAbstract
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
abstract class BusinessCrudAbstract extends BusinessAbstract
{
    /**
     * Persiste as informações no repositório.
     * @example BusinessCrudAbstract::save
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $business->save($valueObject);
     *     ...
     * ?>
     * @endcode
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @return BusinessCrudAbstract
     */
    public function save (ValueObjectAbstract $valueObject)
    {
        return $this->getModel()
                    ->save($valueObject);
    }

    /**
     * Apaga as informações no repositório.
     * @example BusinessCrudAbstract::delete
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $business->delete($valueObject);
     *     ...
     * ?>
     * @endcode
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @return BusinessCrudAbstract
     */
    public function delete (ValueObjectAbstract $valueObject)
    {
        return $this->getModel()
                    ->delete($valueObject);
    }

    /**
     * Busca parametrizada
     * @example BusinessCrudAbstract::findByParam
     * @code
     * <?php
     *     ...
     *     $valueObject = FoobarValueObject::factory();
     *     $valueObject->setFoobar('foobar');
     *     $business->findByParam($valueObject, 10, 2);
     *     ...
     * ?>
     * @endcode
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @param integer $limit
     * @param integer $offSet
     * @return \br\gov\sial\core\valueObject\ValueObjectAbstract[]
     */
    public function findByParam (ValueObjectAbstract $valueObject, $limit = 10, $offSet = 0)
    {
        return $this->getModel()
                    ->findByParam($valueObject, $limit, $offSet)
                    ->getAllValueObject();
    }

    /**
     * Atualiza as informações no repositório de dados.
     * @example BusinessCrudAbstract::update
     * @code
     * <?php
     *     ...
     *     $valueObject = $business->find(3);
     *     $valueObject->setFoobar('foobar');
     *     $business->update($valueObject);
     *     ...
     * ?>
     * @endcode
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @return BusinessCrudAbstract
     */
    public function update (ValueObjectAbstract $valueObject)
    {
        return $this->getModel()
                    ->update($valueObject);
    }
}