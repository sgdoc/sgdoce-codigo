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
 * Classe para Repository de Pais
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Pais
 * @version	 1.0.0
 */
class Pais extends \Sica_Model_Repository
{
    const ID_BRAZIL = 1;

    public function getCombo()
    {
        $query = $this->_em
            ->createQueryBuilder()
            ->select('p.sqPais, p.noPais')
            ->from('app:Pais', 'p')
            ->where('p.sqPais != :Brasil')
            ->setParameter('Brasil',self::ID_BRAZIL)
            ->orderBy('p.noPais');

        $result = $query->getQuery()->getResult();

        $itens = array('' => 'Selecione uma opção');

        foreach ($result as $item) {
            $itens[$item['sqPais']] = $item['noPais'];
        }

        return $itens;

    }
}
