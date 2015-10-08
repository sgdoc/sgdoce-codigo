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

use Doctrine\DBAL\Types\BooleanType;

/**
 * SISICMBio
 *
 * Classe para Repository de TipoDocumento
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoDocumento
 * @version      1.0.0
 * @since        2012-11-20
 */
class TipoDocumento extends \Core_Model_Repository_Base
{
    /**
     * metodo de pesquisa para preencher combo de tipo de documentos
     * @return array
     */
    public function listItemsTipoDocumento()
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('td.sqTipoDocumento, td.noTipoDocumento')
                             ->from('app:TipoDocumento', 'td')
                             ->andWhere('td.stAtivo = TRUE')
                             ->orderBy('td.noTipoDocumento', 'ASC');

        $out = array();
        $res = $queryBuilder->getQuery()
                            ->useResultCache(TRUE, NULL, __METHOD__)
                            ->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqTipoDocumento']] = $item['noTipoDocumento'];
        }
        return $out;
    }

    /**
     * método que pesquisa parametros da grid
     * @param string $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid($params)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('td')
                             ->from('app:TipoDocumento', 'td');

        if ($params->getNoTipoDocumento()) {
            $query = mb_strtolower($params->getNoTipoDocumento(), 'UTF-8');# Consulta case-insensitive
            $queryBuilder->where('LOWER(td.noTipoDocumento) like :tipodoc')
                         ->setParameter('tipodoc', '%' . $query . '%');
        }
        return $queryBuilder;
    }

    /**
     * método que pesquisa se tem tipo de documento
     * @param string $noTipoDocumento
     * @param integer $sqTipoDocumento
     * @return boolean
     */
    public function hasTipoDocumento($noTipoDocumento, $sqTipoDocumento=null)
    {
        $query = mb_strtolower($noTipoDocumento, 'UTF-8');# Consulta case-insensitive
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('td')
                     ->from('app:TipoDocumento', 'td')
                     ->where('LOWER(td.noTipoDocumento) = :noTipoDocumento')
                     ->setParameter('noTipoDocumento', $query);

        if($sqTipoDocumento != NULL) {
            $queryBuilder->andwhere('td.sqTipoDocumento <> :sqTipoDocumento')
                         ->setParameter('sqTipoDocumento', $sqTipoDocumento);
        }

        $res = $queryBuilder->getQuery()->getArrayResult();
        return (count($res) > 0);
    }

    /**
     * método que pesquisa tipo de documento para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function searchTipoDocumento ($term, $limit=10)
    {
        $search       = mb_strtolower($term, 'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('td.noTipoDocumento'));

        $query = $queryBuilder->select('td')
            ->from('app:TipoDocumento', 'td')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->andWhere('td.stAtivo = TRUE')
            ->setMaxResults($limit);

            // dump( $search );

        $res = $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->getArrayResult();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqTipoDocumento']] = $item['noTipoDocumento'];
        }
        return $out;
    }

    /**
     * método que remove sequencial
     * @param integer $sequence
     * @return boolean
     */
    public function deActivate ($sequence)
    {
       $message = \Core_Messaging_Manager::getGateway('User');
       $entity = $this->find($sequence);
       try {
            $this->_em->remove($entity);
            $this->_em->flush();
            $message->addSuccessMessage('MN045');
       }
       catch (\PDOException $exp) {
            $message->addAlertMessage('MN057');

            $queryBuilder = $this->_em
                                 ->createQueryBuilder()
                                 ->update('app:TipoDocumento', 't')
                                 ->set('t.stAtivo', 'FALSE')
                                 ->where('t.sqTipoDocumento = :sqTipoDocumento')
                                 ->setParameter('sqTipoDocumento', $sequence);

            $message->addSuccessMessage('MN055');
            $res = $queryBuilder->getQuery()->execute();
       }
        $message->dispatchPackets();
        return TRUE;
    }
}