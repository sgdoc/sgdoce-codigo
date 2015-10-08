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
 * Classe Service VwUnidadeOrg
 *
 * @package      Principal
 * @subpackage   Services
 * @name         VwUnidadeOrg
 * @version      1.0.0
 * @since         2012-08-17
 */

namespace Auxiliar\Service;

class VwUnidadeOrg extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:VwUnidadeOrg';

    /**
     * Metódo que retorna os dados da Unidade
     * @return array
     */
    public function getDadosUnidade(\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository()->getDadosUnidade($dtoSearch);
    }

    /**
     * Metódo que retorna os dados da Unidade
     * @return array
     */
    public function getUnidadeOrigem(\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository()->getUnidadeOrigem($dtoSearch);
    }

    /**
     * Metódo que retorna os dados da Unidade
     * @return array
     */
    public function searchUnidadesOrganizacionais($arrParans)
    {
        return $this->_getRepository()->searchUnidadesOrganizacionais($arrParans);
    }

    /**
     * (non-PHPdoc)
     * @see Core_ServiceLayer_Service_Base::listGrid()
     */
    public function listGrid(\Core_Dto_Search $dto)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result = $repository->searchPageDto('listGrid', $dto);

        return $result;
    }

    public function hasNUP(\Core_Dto_Search $dto)
    {
        $entity = $this->_getRepository()->find($dto->getSqUnidadeOrg());
        return (!!$entity->getNuNup());
    }

    public function isSede (\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->isSede($dto);
    }

}