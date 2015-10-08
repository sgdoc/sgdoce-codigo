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
 * Classe para Repository de SequencialArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         SequencialArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class SequencialArtefato extends \Core_Model_Repository_Base
{
    /**
     * método que faz pesquisa para atocomplete
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function searchSequnidorg ($params)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('sa', 'tp')
                     ->from('app:SequencialArtefato', 'sa')
                     ->innerJoin('sa.sqTipoDocumento', 'tp');

        return $queryBuilder;
    }

    /**
     * método que pesquisa dados da grid
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid ($params)
    {
        $queryBuilder = $this->getEntityManager()
             ->createQueryBuilder()
             ->select('a.nuAno'
                     ,'a.sqSequencialArtefato'
                     ,'td.noTipoDocumento'
                     ,'u.sqUnidadeOrg'
                     ,'u.noUnidadeOrg'
                     ,'a.nuSequencial'
                     ,'td.sqTipoDocumento'
                     ,'td.stAtivo'
            )
             ->from('app:TipoDocumento', 'td')
             ->leftJoin('td.sqSequencialArtefato','a', 'WITH', 'a.nuAno = :nuAno AND a.sqUnidadeOrg = :sqUnidadeOrg')
             ->leftJoin('a.sqUnidadeOrg', 'u')
             ->orderBy('td.noTipoDocumento');

        if ($params->getNoPessoa()) {
            $queryBuilder->setParameter('sqUnidadeOrg', $params->getNoPessoa());
        } else {
            $queryBuilder->setParameter('sqUnidadeOrg', NULL);
        }

        if ($params->getNuAno()) {
            $queryBuilder->setParameter('nuAno', $params->getNuAno());
        } else {
            $queryBuilder->setParameter('nuAno', NULL);
        }


        return $queryBuilder;
    }

    public function hasSequencialProcesso($nuSequencial)
    {
        $data = new \Zend_Date(\Zend_Date::now());
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('a')
            ->from('app:Artefato','a')
            ->innerJoin('a.sqArtefatoProcesso','ap')
            ->where($queryBuilder->expr()->substring('a.nuArtefato',6,10). ' = :nuSequencial')
            ->setParameter('nuSequencial',($nuSequencial).$data->get('Y'));

        $result = $queryBuilder->getQuery()->execute();

        if (count($result) > 0){
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * @param integer $sqUnidadeOrg
     */
    public function getNextSequencialProcesso( $sqUnidadeOrg )
    {        
        $data = new \Zend_Date(\Zend_Date::now());
        
        $criteriaSequencialArtefato = array(
            'sqUnidadeOrg'  => $sqUnidadeOrg,            
            'nuAno'         => $data->get('Y'),
            'sqTipoArtefato'=> \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
        );
        
        $sequencialArtefato = $this->findOneBy($criteriaSequencialArtefato);
        
        if( !count($sequencialArtefato)
            || $sequencialArtefato->getNuSequencial() == 0 ) {
            $vwUnidadeOrg = $this->getEntityManager()->find('app:VwUnidadeOrg', $sqUnidadeOrg);
            throw new \Exception("Não existe sequencial para numeração de processo na unidade: " . $vwUnidadeOrg->getSgUnidadeOrg());
        }
                        
        return $sequencialArtefato;
    }
    
    /**
     * @param integer $sqUnidadeOrg
     */
    public function setSequencialProcesso( $sqUnidadeOrg )
    {
        $data = new \Zend_Date(\Zend_Date::now());
        
        $criteria = array(
            'sqUnidadeOrg'  => $sqUnidadeOrg,
            'nuAno'         => $data->get('Y'),
            'sqTipoArtefato'=> \Core_Configuration::getSgdoceTipoArtefatoProcesso(),
        );
        
        $sequencialArtefato = $this->findOneBy($criteria);
        $sequencialArtefato->setNuSequencial($sequencialArtefato->getNuSequencial() + 1);
        
        $this->getEntityManager()->persist($sequencialArtefato);
        $this->getEntityManager()->flush();
        
    }

}
