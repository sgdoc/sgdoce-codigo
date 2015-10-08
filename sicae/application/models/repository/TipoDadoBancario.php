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
 * Classe para Repository de TipoDadoBancario
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 TipoDadoBancario
 * @version	 1.0.0
 */
class TipoDadoBancario extends \Sica_Model_Repository
{

    /**
     * Recupera combo conforme sqPessoa
     * @param type $sqPessoa
     * @return type 
     */
    public function getComboForSqPessoa($sqPessoa = NULL)
    {
        $sqTipoDadoBancario = $this->_em
                ->createQueryBuilder()
                ->select('tpb.sqTipoDadoBancario')
                ->from('app:DadoBancario', 'e')
                ->innerJoin('e.sqPessoa', 'p')
                ->innerJoin('e.sqTipoDadoBancario', 'tpb')
                ->where('p.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $sqPessoa)
                ->getQuery()
                ->getResult();

        $criteria = array();
        foreach ($sqTipoDadoBancario as $value) {
            array_push($criteria, $value['sqTipoDadoBancario']);
        }

        $result = $this->_em
                ->createQueryBuilder()
                ->select('tp.sqTipoDadoBancario, tp.noTipoDadoBancario')
                ->from('app:TipoDadoBancario', 'tp');

        if ($criteria) {
            $result->andWhere($this->_em->createQueryBuilder()->expr()->notIn('tp.sqTipoDadoBancario', $criteria));
        }

        $itens = array();
        foreach ($result->getQuery()->getResult() as $item) {
            $itens[$item['sqTipoDadoBancario']] = $item['noTipoDadoBancario'];
        }

        return $itens;
    }

}