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
 * Classe para Repository de TipoVinculoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoVinculoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class TipoVinculoArtefato extends \Core_Model_Repository_Base
{
    /**
     * método que pesquisa dados da grid
     * @param array $params
     * @return array
     */
    public function listItemsTipoVinculoArtefato($tipo = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder()->select('tva.sqTipoVinculoArtefato,tva.noTipoVinculoArtefato')
            ->from('app:TipoVinculoArtefato', 'tva')->orderBy('tva.noTipoVinculoArtefato', 'ASC');
        /**
         * Subistituir por copia e original apos carga de dado
         */
        if ($tipo == 'vinculo') {
            $queryBuilder->where('tva.sqTipoVinculoArtefato in(2,3)');
        }
        $out = array();
        $res = $queryBuilder->getQuery()->getArrayResult();
        foreach ($res as $item) {
            $out[$item['sqTipoVinculoArtefato']] = $item['noTipoVinculoArtefato'];
        }
        return $out;
   }
}
