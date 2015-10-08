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
namespace br\gov\sial\core\persist;
use br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * SIAL
 *
 * Interface de funcionalidades da camada de persistência
 *
 * @package br.gov.sial.core
 * @subpackage persist
 * @name Persistable
 * @author J. Augusto <augustowebd@gmail.com>
 * */
interface Persistable
{
    /**
     * @var string
     * */
    const WE_HAVE_A_PROBLEM_ON_GET_DATA = 'Não possível recuperar os dados solictado';

    /**
     * @var string
     * */
    const WE_HAVE_A_PROBLEM_ON_SAVE_DATA = 'Não possível persistir os dados informados';

    /**
     * @var string
     * */
    const THERE_IS_NO_PRIMARY_ON_THE_TABLE = 'Nenhuma chave primaria encontrada na entidade';

    /**
     * persiste dados no repositorio
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * */
    public function save (ValueObjectAbstract $valueObject);

    /**
     * altera dados no repositorio
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * */
    public function update (ValueObjectAbstract $valueObject);

    /**
     * deleta dados no repositorio
     *
     * @param ValueObjectAbstract $valueObject
     * @return br\gov\sial\core\persist\Persist
     * */
    public function delete (ValueObjectAbstract $valueObject);

    /**
     * recupera registro com base em seu ID
     *
     * @param integer $key
     * @return br\gov\sial\core\persist\Persist
     * */
    public function find ($key);

    /**
     * retorna Query para entidade inforamada
     *
     * @param Entity $entity
     * @return Query
     * */
    public function getQuery ($entity);

    /**
     * Retorna uma entidade referente ao ValueObject informado, opcionalmente, uma relacao de colunas
     * podera ser informada, limintando a relacao original de Entity.
     *
     * <ul>
     *    <li>Persistable::getEntity(ValueObjectAbstract $valueObject [, array(string, colunm1, string column2)])</li>
     *    <li>Persistable::getEntity(array(string 'alias' => ValueObjectAbstract $valueObject [, array(string, colunm1, string column2)])</li>
     * </ul>
     *
     * @param ValueObjectAbstract $entity
     * @param string[] $columns
     * @return Entity
     * */
    public function getEntity ($entity, array $columns = array());
}