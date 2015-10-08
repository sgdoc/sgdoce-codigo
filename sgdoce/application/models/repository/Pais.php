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

/**
 * SISICMBio
 *
 * Classe para Repository de Pais
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Pais
 * @version      1.0.0
 * @since        2012-11-20
 */
class Pais extends \Core_Model_Repository_Base
{
    /**
     * Obtem dados para montar a Combo País
     * @param int $pais
     * @param string $estado
     * @return array
     */
    public function comboPais($somenteEstrangeiro = true)
    {
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->select(array('p'))
            ->from('app:Pais', 'p')
            ->orderBy('p.noPais');

        if($somenteEstrangeiro) {
            $queryBuilder->where('p.sqPais <> :sqPais')
                ->setParameter('sqPais', '1');
        }

        $result = $queryBuilder->getQuery()->getArrayResult();

        $out = array('' => 'Selecione...');
        foreach ($result as $item) {
            $out[$item['sqPais']] = $item['noPais'];
        }

        return $out;
    }
}