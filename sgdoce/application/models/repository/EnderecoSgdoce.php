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

use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\Mapping\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de EnderecoSgdoce
 *
 * @package      Model
 * @subpackage   Repository
 * @name         EnderecoSgdoce
 * @version      1.0.0
 * @since        2013-02-08
 */
class EnderecoSgdoce extends \Core_Model_Repository_Base
{
    public function getEnderecoFromCorporativo($entity, $sqPessoaSgdoce)
    {
        $query = null;

        try {
            $query = $this->getEntityManager()->createQueryBuilder()
                ->select('es')
                ->from($this->_entityName, 'es')
                ->where('es.sqPessoaSgdoce = :sqPessoaSgdoce')
                ->andWhere('es.sqTipoEndereco = :sqTipoEndereco')
                ->andWhere('es.sqMunicipio = :sqMunicipio')
                ->andWhere('es.coCep = :coCep')
                ->andWhere('es.noBairro = :noBairro')
                ->andWhere('es.txEndereco = :txEndereco')
                ->andWhere('es.nuEndereco = :nuEndereco')
                ->setParameters(array(
                    'sqPessoaSgdoce' => $sqPessoaSgdoce,
                    'sqTipoEndereco' => $entity->getSqTipoEndereco()->getSqTipoEndereco(),
                    'sqMunicipio'    => $entity->getSqMunicipio()->getSqMunicipio(),
                    'coCep'          => $entity->getSqCep(),
                    'noBairro'       => $entity->getNoBairro(),
                    'txEndereco'     => $entity->getTxEndereco(),
                    'nuEndereco'     => $entity->getNuEndereco(),
                ))
                ->orderBy('es.sqEnderecoSgdoce', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(\Doctrine\ORM\NoResultException $e) {}

        return $query;
    }

    public function updateEndereco(\Core_Dto_Abstract $dto, $params = null)
    {
        $return = null;

        try {
            $_qb = $this->_em->createQueryBuilder();
            $_qb->update('app:EnderecoSgdoce', 'es')
                ->set('es.sqTipoEndereco', $_qb->expr()->literal($dto->getSqTipoEndereco()))
                ->set('es.sqMunicipio', $_qb->expr()->literal($dto->getSqMunicipio()))
                ->set('es.coCep', $_qb->expr()->literal($dto->getCoCep()))
                ->set('es.noBairro', $_qb->expr()->literal($dto->getNoBairro()))
                ->set('es.txEndereco', $_qb->expr()->literal($dto->getTxEndereco()))
                ->set('es.nuEndereco', $_qb->expr()->literal($dto->getNuEndereco()))
                ->set('es.txComplemento', $_qb->expr()->literal($dto->getTxComplemento()))
                ->set('es.noContato', $_qb->expr()->literal($params->getNoContato()))
                ->where('es.sqEnderecoSgdoce = :sqEnderecoSgdoce')
                ->setParameter('sqEnderecoSgdoce', $dto->getSqEnderecoSgdoce())
                ->getQuery()
                ->execute();

            $return = true;
        } catch(Exception $e) {}

        return $return;
    }

    public function findByArray($pessoaSgdoce)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
        ->select('p.sqPessoaSgdoce,
                  p.noPessoa,
                  e.sqEnderecoSgdoce,
                  te.sqTipoEndereco,
                  te.noTipoEndereco,
                  e.coCep,
                  e.txEndereco,
                  e.nuEndereco,
                  es.sqEstado,
                  es.noEstado,
                  m.sqMunicipio,
                  m.noMunicipio')
        ->from('app:PessoaSgdoce', 'p')
        ->innerJoin('p.sqPessoaEndereco', 'e')
        ->leftJoin('e.sqTipoEndereco', 'te')
        ->leftJoin('e.sqMunicipio', 'm')
        ->leftJoin('m.sqEstado', 'es')
        ->andWhere('e.sqPessoaSgdoce = :sqPessoaSgdoce')
        ->setParameter('sqPessoaSgdoce', $pessoaSgdoce->getSqPessoaSgdoce());

        return $query->getQuery()->getArrayResult();
    }

    public function deleteByPessoaSgdoce ($sqPessoaSgdoce)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb->delete($this->_entityName, 't')
                        ->where($qb->expr()->eq('t.sqPessoaSgdoce', $sqPessoaSgdoce))
                        ->getQuery()
                        ->execute();
    }
}
