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

namespace Sgdoce\Model\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Chefia
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VwChefia extends \Core_Model_Repository_Base
{

    public function getComboDas()
    {
        $unidadeDestinada = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from($this->_entityName, 'c')
            ->innerJoin('c.sqDestinacaoFgDas', 'dfd')
            ->where('dfd.sqUnidadeOrgDestinada = :sqUnidadeOrgDestinada')
            ->setParameter('sqUnidadeOrgDestinada', $this->getSicaUnidadeOrg() ? : null)
            ->getQuery()
            ->getResult();

        if(count($unidadeDestinada)) {
            $arrResult = array();
            
            try {
                
                foreach( $unidadeDestinada as $unidade ) {                   
                    
                    if($unidade->getSqProfissionalTitular()->getSqPessoa()) {
                        $chefe = $unidade->getSqProfissionalTitular();

                        $arrResult[$chefe->getSqPessoa()] = $chefe->getNoPessoa();
                    }

                    if($unidade->getSqProfissionalSubstituto()) {
                        $substituto = $unidade->getSqProfissionalSubstituto();

                        $arrResult[$substituto->getSqPessoa()] = $substituto->getNoPessoa();
                    }
                    
                }

                return $arrResult;
            } catch (\Exception $e) {
                dumpd($e);
                return $arrResult;
            }

        }

        return false;
    }

    public function isResponsavelSetor(\Core_Dto_Abstract $dto)
    {
        $_qb   = $this->getEntityManager()->createQueryBuilder();
        $query = $_qb->select('c')
            ->from($this->_entityName, 'c')
            ->join('c.sqDestinacaoFgDas', 'dfd')
            ->join('dfd.sqUnidadeOrgDestinada', 'uod')
            ->join('c.sqProfissionalTitular', 'pt')
            ->join('pt.sqProfissional', 'p')
            ->join('c.sqProfissionalSubstituto', 'pt2')
            ->join('pt2.sqProfissional', 'ps')
            ->where(
                $_qb->expr()->orX()
                    ->add($_qb->expr()->eq('p.sqPessoa', ':sqPessoa'))
                    ->add($_qb->expr()->eq('ps.sqPessoa', ':sqPessoa'))
            )
            ->andWhere('uod.sqPessoa = :sqUnidadeOrg')
            ->setParameter('sqUnidadeOrg', $this->getSicaUnidadeOrg())
            ->setParameter('sqPessoa', $dto->getSqResponsavel())
            ->getQuery()
            ->getResult();

        return $query;
    }

    private function getSicaUnidadeOrg()
    {
    	$sicaUser = \Core_Integration_Sica_User::get();

    	if(!isset($sicaUser->sqUnidadeOrg)) {
    		return false;
    	}

        return $sicaUser->sqUnidadeOrg;
    }
}