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

use Doctrine\ORM\Query\Expr\Join;

/**
 * SISICMBio
 *
 * Classe para Repository de EtiquetasUso
 *
 * @package      Model
 * @subpackage   Repository
 * @name         EtiquetasUso
 * @since        2014-10-23
 */
class EtiquetasUso extends \Core_Model_Repository_Base
{

    /**
     * Verifica se uma digital esta em uso, apenas.
     * OBS: metodo não considera digitais não liberadas
     *
     * @param \Core_Dto_Search $search
     * @return boolean
     */
    public function verificaDigitalEmUso(\Core_Dto_Search $search )
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('IDENTITY(ens.sqLoteEtiqueta) AS sqLoteEtiqueta')->distinct()
            ->from('app:EtiquetasUso', 'eu')
            ->innerJoin('eu.sqLoteEtiqueta', 'ens', Join::WITH, "eu.nuEtiqueta = :nuEtiqueta")
            ->innerJoin('ens.sqLoteEtiqueta', 'le')
            ->where($qb->expr()->eq('le.sqUnidadeOrg', ':sqUnidadeOrg'))
            ->andWhere($qb->expr()->between(':nuSequencialDigital', 'le.nuInicial', 'le.nuFinal'))
            ->andWhere($qb->expr()->eq('le.nuAno',':nuAno'))
            ->andWhere($qb->expr()->eq('le.sqTipoEtiqueta',':sqTipoEtiqueta'))

            ->setParameter('nuEtiqueta', $search->getNuEtiqueta())
            ->setParameter('sqUnidadeOrg'  , $search->getSqUnidadeOrg())
            ->setParameter('nuSequencialDigital', $search->getNuSequencialDigital())
            ->setParameter('nuAno'         , $search->getNuAno())
            ->setParameter('sqTipoEtiqueta', $search->getSqTipoEtiqueta());

        return count($qb->getQuery()->getArrayResult()) > 0;
    }

}
