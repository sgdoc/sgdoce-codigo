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
namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de TipoAnexo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoAnexo
 * @version      1.0.0
 * @since        2013-05-29
 */
class TipoAnexo extends \Core_Model_Repository_Base
{
	/**
	* método que pesquisa dados da grid
	* @param array $params
	* @return \Doctrine\ORM\QueryBuilder
	*/
	public function listItemsTipoAnexo()
	{
		 $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('ta.sqTipoAnexo,ta.noTipoAnexo')
                             ->from('app:TipoAnexo', 'ta')
                             ->orderBy('ta.sqTipoAnexo', 'ASC');
        $out = array();
        $res = $queryBuilder->getQuery()->getArrayResult();
        foreach ($res as $item) {
            $out[$item['sqTipoAnexo']] = $item['noTipoAnexo'];
        }
        return $out;
	}
}