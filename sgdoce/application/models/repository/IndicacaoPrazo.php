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
 * Classe para Repository de IndicacaoPrazo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         IndicacaoPrazo
 * @version      1.0.0
 * @since        2012-11-20
 */
class IndicacaoPrazo extends \Core_Model_Repository_Base
{
    /**
     * Efetua a consulta para popular a grid.
     * @param array $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function searchIndicacaoPrazo ($params)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('ip', 'tp', 'a')
                     ->from('app:IndicacaoPrazo', 'ip')
                     ->leftJoin('ip.sqTipoDocumento', 'tp')
                     ->innerJoin('ip.sqAssunto', 'a');

        if($params->getSqTipoDocumento() && $params->getSqTipoDocumento_autocomplete()) {
            $sqTipoDocumento = \Zend_Filter::filterStatic($params->getSqTipoDocumento(), 'null');

            $queryBuilder->andWhere('ip.sqTipoDocumento = :sqTipoDocumento')
                         ->setParameter('sqTipoDocumento', $sqTipoDocumento);
        }

        if ($params->getSqAssunto() && $params->getSqAssunto_autocomplete()) {
            $sqAssunto = \Zend_Filter::filterStatic($params->getSqAssunto(), 'null');

            $queryBuilder->andWhere('ip.sqAssunto = :sqAssunto')
                          ->setParameter('sqAssunto', $sqAssunto);
        }

        return $queryBuilder;
    }

    /**
     * Retorna a quantidade de registros vinculados para o assunto e/ou tipo de documento.
     * @param integer $sqAssunto
     * @param integer $sqTipoDocumento
     * @param integer $sqIndicacaoPrazo
     * @return integer
     */
    protected function getVinculazaoPrazo($sqAssunto = NULL, $sqTipoDocumento = NULL, $sqIndicacaoPrazo = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('ip')
                     ->from('app:IndicacaoPrazo', 'ip')
                     ->where('ip.sqAssunto = :sqAssunto')
                     ->setParameter('sqAssunto', $sqAssunto);

        if ($sqTipoDocumento) {
            $queryBuilder->andWhere('ip.sqTipoDocumento = :sqTipoDocumento')
                         ->setParameter('sqTipoDocumento', $sqTipoDocumento);
        } else {
            $queryBuilder->andWhere('ip.sqTipoDocumento is null');
        }

        if ($sqIndicacaoPrazo) {
            $queryBuilder->andWhere('ip.sqIndicacaoPrazo <> :sqIndicacaoPrazo')
                         ->setParameter('sqIndicacaoPrazo', $sqIndicacaoPrazo);
        }

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * Exclui vinculação do prazo para o assunto e/ou tipo de documento.
     * @param integer $sqAssunto
     * @param integer $sqTipoDocumento
     * @param integer $sqIndicacaoPrazo
     */
    public function delVinculacaoPrazo($sqAssunto = NULL, $sqTipoDocumento = NULL, $sqIndicacaoPrazo = NULL)
    {
        $return = $this->getVinculazaoPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo);
        foreach ($return as $object) {
            $this->getEntityManager()->remove($object);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * Verifica se existe vinculação do prazo para o assunto e/ou tipo de documento.
     * @param integer $sqAssunto
     * @param integer $sqTipoDocumento
     * @param integer $sqIndicacaoPrazo
     * @return integer
     */
    public function hasVinculacaoPrazo($sqAssunto = NULL, $sqTipoDocumento = NULL, $sqIndicacaoPrazo = NULL)
    {
        $return = count($this->getVinculazaoPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo));
        return $return;
    }
}