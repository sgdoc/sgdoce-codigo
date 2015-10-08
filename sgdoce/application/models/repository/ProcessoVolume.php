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

use Doctrine\Common\Util\Debug;

/**
 * SISICMBio
 *
 * Classe para Repository de ProcessoVolume
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Artefato
 * @version      1.0.0
 * @since        2015-01-28
 */
class ProcessoVolume extends \Core_Model_Repository_Base
{

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $query = $queryBuilder->select('pv')
                ->from('app:ProcessoVolume', 'pv')
                ->where($queryBuilder->expr()->eq('pv.sqArtefato', $dto->getSqArtefato()))
                ->orderBy('pv.nuVolume', 'DESC')
                ;

        return $query;
    }


    /**
     * @return
     */
    public function getLastEncerrado( $dto )
    {
        $query = $this->_em->createQueryBuilder();

        $query->select(array("pv.sqVolume"))
            ->from('app:ProcessoVolume', 'pv')
            ->where('pv.sqArtefato = :sqArtefato')
            ->andWhere($query->expr()->isNotNull("pv.dtEncerramento"))
            ->setParameter('sqArtefato', $dto->getSqArtefato())
            ->orderBy('pv.nuVolume', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->execute();
    }

    /**
     * @return
     */
    public function getLastAberto( $dto )
    {
        $query = $this->_em->createQueryBuilder();

        $query->select(array("pv.sqVolume"))
            ->from('app:ProcessoVolume', 'pv')
            ->where('pv.sqArtefato = :sqArtefato')
            ->andWhere($query->expr()->isNull("pv.dtEncerramento"))
            ->setParameter('sqArtefato', $dto->getSqArtefato())
            ->orderBy('pv.nuVolume', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->execute();
    }

    public function notTheOnlyVolume($sqArtefato)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_volume', 'sqVolume', 'integer');
        
        $query = $this->_em->createNativeQuery('
                SELECT 
                    sq_volume 
                FROM 
                    sgdoce.processo_volume 
                WHERE 
                    sq_artefato = :sqArtefato'
                , $rsm);
        
        $query->setParameter('sqArtefato', $sqArtefato);
        
        $result = $query->getResult();
        
        if (count($result) > 1) {
            return TRUE;
        }
        return FALSE;
    }

    public function getDatasMaxMin($sqVolume)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('dt_abertura'    , 'dtAbertura'    , 'string');
        $rsm->addScalarResult('dt_encerramento', 'dtEncerramento', 'string');
        
        $query = $this->_em->createNativeQuery('
                SELECT 
                    to_char(ant.dt_encerramento, \'DD/MM/YYYY\') AS dt_encerramento, 
                    to_char(post.dt_abertura, \'DD/MM/YYYY\') AS dt_abertura
                FROM 
                    sgdoce.processo_volume pv
                    LEFT JOIN sgdoce.processo_volume ant  ON (ant.sq_artefato  = pv.sq_artefato AND ant.nu_volume  = (pv.nu_volume - 1))
                    LEFT JOIN sgdoce.processo_volume post ON (post.sq_artefato = pv.sq_artefato AND post.nu_volume = (pv.nu_volume + 1))
                WHERE 
                    pv.sq_volume = :sqVolume'
                , $rsm);
        
        $query->setParameter('sqVolume', $sqVolume);
        
        return $query->getResult();
    }
}