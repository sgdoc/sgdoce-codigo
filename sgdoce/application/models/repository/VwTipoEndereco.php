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

use Bisna\Application\Resource\Doctrine;

/**
 * SISICMBio
 *
 * Classe para Repository de TipoEndereco
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 VwTipoEndereco
 * @version	 1.0.0
 */
class VwTipoEndereco extends \Sgdoce\Model\Repository\Base
{

    /**
     * Recupera combo conforme sqPessoa
     * @param type $sqPessoa
     * @return type
     */
    public function getComboForSqPessoa($sqPessoa = NULL)
    {
        $sqTipoEndereco = $this->_em
                ->createQueryBuilder()
                ->select('tpe.sqTipoEndereco')
                ->from('app:VwEndereco', 'e')
                ->innerJoin('e.sqPessoa', 'p')
                ->innerJoin('e.sqTipoEndereco', 'tpe')
                ->where('p.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $sqPessoa)
                ->getQuery()
                ->getResult();

        $criteria = array();
        foreach ($sqTipoEndereco as $value) {
            array_push($criteria, $value['sqTipoEndereco']);
        }

        $result = $this->_em
                ->createQueryBuilder()
                ->select('tp.sqTipoEndereco, tp.noTipoEndereco')
                ->from('app:VwTipoEndereco', 'tp');

        if ($criteria) {
            $result->andWhere($this->_em->createQueryBuilder()->expr()->notIn('tp.sqTipoEndereco', $criteria));
        }

        $itens = array();
        foreach ($result->getQuery()->getArrayResult() as $item) {
            $itens[$item['sqTipoEndereco']] = $item['noTipoEndereco'];
        }

        return $itens;
    }

}
