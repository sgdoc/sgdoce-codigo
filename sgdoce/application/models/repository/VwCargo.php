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
 * Classe para Repository Cargo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwCargo
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwCargo extends \Core_Model_Repository_Base
{
    /**
     * Variável para receber a entidade VwCargo
     * @var    string
     * @access protected
     * @name   $_enName
     */
    private $_enName = 'app:VwCargo';

    /**
     * Obtem dados para montar a Combo Cargo
     * @param boolean $withSelect default true
     * @return array
     */
    public function comboCargo ($withSelect=true)
    {
        $fields = array('c.sqCargo', 'c.noCargo');

        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select($fields)
                ->from($this->_enName, 'c')
                ->orderBy('c.noCargo');

        $result = $queryBuilder->getQuery()
                               ->useResultCache(TRUE, NULL, __METHOD__)
                               ->getArrayResult();

        $out = array();
        if ($withSelect) {
            $out[''] = 'Selecione uma opção';
        }

        foreach ($result as $item) {
            $out[$item['sqCargo']] = $item['noCargo'];
        }

        return $out;
    }

    /**
     * Obtém estados
     * @param integer $sqCargo
     * @return array
     */
    public function BuscarCargo ($sqCargo = NULL)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('c')
                ->from($this->_enName, 'c')
                ->orderBy('c.noCargo', 'asc');

        return $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->execute();
    }
}