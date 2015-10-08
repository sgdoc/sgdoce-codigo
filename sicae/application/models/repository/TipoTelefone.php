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

use Sica\Model\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de TipoTelefone
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 TipoTelefone
 * @version	 1.0.0
 */
class TipoTelefone extends \Sica_Model_Repository
{

    /**
     * Recupera combo conforme sqPessoa
     * @param type $sqPessoa
     * @return type 
     */
    public function getComboForSqPessoa($sqPessoa = NULL)
    {
        $sqTipoTelefone = $this->_em
                ->createQueryBuilder()
                ->select('tpt.sqTipoTelefone')
                ->from('app:Telefone', 't')
                ->innerJoin('t.sqPessoa', 'p')
                ->innerJoin('t.sqTipoTelefone', 'tpt')
                ->where('p.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $sqPessoa)
                ->getQuery()
                ->getResult();

        $criteria = array();
        foreach ($sqTipoTelefone as $value) {
            array_push($criteria, $value['sqTipoTelefone']);
        }

        $result = $this->_em
                ->createQueryBuilder()
                ->select('tp.sqTipoTelefone, tp.noTipoTelefone')
                ->from('app:TipoTelefone', 'tp');

        if ($criteria) {
            $result->andWhere($this->_em->createQueryBuilder()->expr()->notIn('tp.sqTipoTelefone', $criteria));
        }

        $itens = array();
        foreach ($result->getQuery()->getResult() as $item) {
            $itens[$item['sqTipoTelefone']] = $item['noTipoTelefone'];
        }

        return $itens;
    }

}