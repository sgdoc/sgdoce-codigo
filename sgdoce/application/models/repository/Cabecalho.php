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
 * Classe para Repository de Cabecalho
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Cabecalho
 * @version      1.0.0
 * @since        2012-11-20
 */
class Cabecalho extends \Core_Model_Repository_Base
{
    /**
     * Método de consulta cabecalhos para preenchimento do combo
     * @return array
     */
    public function listItensCabecalho()
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('c.sqCabecalho,c.noCabecalho')
                             ->from('app:Cabecalho', 'c')
                             ->orderBy('c.noCabecalho', 'ASC');

        $res = $queryBuilder->getQuery()->getArrayResult();
        $out = array('' => 'Nenhum');
        foreach ($res as $item) {
            $out[$item['sqCabecalho']] = $item['noCabecalho'];
        }
        return $out;
    }

    /**
     * Método de consulta cabecalhos para preenchimento do combo
     * @return array
     */
    public function listGridCabecalho($dtoSearch)
    {
        $queryBuilder = $this->_em
        ->createQueryBuilder()
        ->select('c.sqCabecalho,c.noCabecalho')
        ->from('app:Cabecalho', 'c')
        ->orderBy('c.sqCabecalho', 'ASC');

        return $queryBuilder;
    }
}
