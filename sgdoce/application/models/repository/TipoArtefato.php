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
 * Classe para Repository de TipoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class TipoArtefato extends \Core_Model_Repository_Base
{
    /**
     * método que pesquisa dados da grid
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listItemsTipoArtefato ($tipo = NULL)
    {
        $tipoDocumento = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        $tipoProcesso = \Core_Configuration::getSgdoceTipoArtefatoProcesso();
        $tipoDossie = \Core_Configuration::getSgdoceTipoArtefatoDossie();
        $tipoAmbos = \Core_Configuration::getSgdoceTipoArtefatoAmbos();

        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('ta.sqTipoArtefato,ta.noTipoArtefato')
                             ->from('app:TipoArtefato', 'ta')
                             ->orderBy('ta.noTipoArtefato', 'ASC');

        if ($tipo == 'documento') {
            $queryBuilder->where($queryBuilder->expr()
                    ->in('ta.sqTipoArtefato', array($tipoDocumento,$tipoProcesso))
                );

        } else if ($tipo == 'material') {
            $queryBuilder->where($queryBuilder->expr()
                    ->in('ta.sqTipoArtefato', array($tipoDocumento,$tipoDossie))
                );
        }

        $out = array();
        $res = $queryBuilder->getQuery()->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqTipoArtefato']] = $item['noTipoArtefato'];
        }

        return $out;
    }

    public function listItemsVinculoArtefatoAction (array $tipos = NULL)
    {
        $tipoDocumento = \Core_Configuration::getSgdoceTipoArtefatoDocumento();
        $tipoProcesso = \Core_Configuration::getSgdoceTipoArtefatoProcesso();

        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('ta.sqTipoArtefato,ta.noTipoArtefato')
                             ->from('app:TipoArtefato', 'ta')
                             ->orderBy('ta.noTipoArtefato', 'ASC');

        $queryBuilder->andWhere('ta.sqTipoArtefato in(:tipoDocumento,:tipoProcesso)')
                     ->setParameters(array(
                            'tipoDocumento' => $tipoDocumento,
                            'tipoProcesso'  => $tipoProcesso,
                        ));

        $out = array();
        $res = $queryBuilder->getQuery()->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqTipoArtefato']] = $item['noTipoArtefato'];
        }

        return $out;
    }
}