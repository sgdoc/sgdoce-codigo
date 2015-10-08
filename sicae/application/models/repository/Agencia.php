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

namespace Sica\Model\Repository;

use Bisna\Application\Resource\Doctrine;

/**
 * SISICMBio
 *
 * Classe para Repository de Agencia
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Agencia
 * @version	 1.0.0
 */
class Agencia extends \Sica_Model_Repository
{

    /**
     * Realiza busca para autocomplete
     * @param array $params
     * @return array
     */
    public function searchAgencia($params)
    {
        $sql = "SELECT a.sq_agencia, a.co_agencia
                FROM vw_agencia a 
                INNER JOIN vw_banco b ON a.sq_banco = b.sq_banco 
                WHERE b.sq_banco = :sqBanco
                AND CAST(a.co_agencia AS TEXT) LIKE :coAgencia
                ORDER BY a.co_agencia ASC LIMIT 10";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_agencia', 'sqAgencia', 'string');
        $rsm->addScalarResult('co_agencia', 'coAgencia', 'string');

        $result = $this->_em
                ->createNativeQuery($sql, $rsm)
                ->setParameter('sqBanco', $params['extraParam'])
                ->setParameter('coAgencia', mb_strtolower($params['query'], 'UTF-8') . '%')
                ->execute();

        $itens = array();

        foreach ($result as $item) {
            $itens[$item['sqAgencia']] = '' . $item['coAgencia'] . '';
        }

        return $itens;
    }
}