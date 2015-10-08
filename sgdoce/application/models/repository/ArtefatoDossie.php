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
 * Classe para Repository de ArtefatoDossie
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ArtefatoDossie
 * @version      1.0.0
 * @since        2013-05-17
 */
class ArtefatoDossie extends \Core_Model_Repository_Base
{

	public function deleteDossie($sqArtefato)
	{
		$queryBuilder = $this->_em->createQueryBuilder()
			->delete('app:ArtefatoDossie', 'ad')
			->andWhere('ad.sqArtefato = :sqArtefato')
			->setParameter('sqArtefato', $sqArtefato);

		$out = $queryBuilder->getQuery()->execute();
	}
}