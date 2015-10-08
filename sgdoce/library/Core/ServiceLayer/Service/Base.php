<?php

/**
 * Copyright 2012 do ICMBio
 *
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
use Doctrine\ORM\AbstractQuery;

/**
 * Concrete Service
 *
 * @category   Service Layer
 * @package    Core
 * @subpackage ServiceLayer
 * @subpackage Service
 * @name       Base
 */
class Core_ServiceLayer_Service_Base extends Core_ServiceLayer_Service_Abstract
{

    public function find($id = null)
    {
        if (null === $id) {
            throw new Core_Exception_ServiceLayer('É necessário informar um identificador para a entidade solicitada.');
        }
        return $this->getEntityManager()->find($this->_getEntityName(), $id);
    }

    public function findAll()
    {
        return $this->_getRepository()->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->_getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria)
    {
        return $this->_getRepository()->findOneBy($criteria);
    }

    public function listGrid(\Core_Dto_Search $dto)
    {
        return $this->_getRepository($this->_entityName)->searchPageDto('listGrid', $dto);
    }

    /**
     * Metodo generico para combo
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array The objects.
     * @return array
     */
    public function getComboDefault(array $criteria = array(), array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        return $this->_getRepository()->getComboDefault($criteria, $orderBy, $limit, $offset);
    }

}