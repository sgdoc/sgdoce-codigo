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
/**
 * Classe para Service de ProcessoCaverna
 *
 * @package  Minuta
 * @category Service
 * @name     ProcessoCaverna
 * @version  1.0.0
 */

class ProcessoCaverna extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:ProcessoCaverna';

    public function findProcesso($dto)
    {
        $criteria = array('sqArtefato' => $dto->getSqArtefato()->getSqArtefato()
                           ,'sqCaverna' =>$dto->getSqCaverna()->getCodigo());
        return $this->_getRepository()->findOneBy($criteria);
    }

    /**
     * método para pesquisa de grid de material de apoio
     * @param \Core_Dto_Search $dto
     */
    public function listGridCapaProcesso(\Core_Dto_Search $dto)
    {
    	$result = $this->_getRepository()->listGridTemaTratado($dto,TRUE);
    	return $result;
    }
}
