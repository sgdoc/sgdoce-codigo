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

namespace Artefato\Service;
/**
 * Classe para Service de Cabecalho
 *
 * @package  Minuta
 * @category Service
 * @name     Cabecalho
 * @version  1.0.0
 */

class Cabecalho extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:Cabecalho';
    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function listItensCabecalho()
    {
        return $this->_getRepository()->listItensCabecalho();
    }

    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function listGridCabecalho($dtoSearch)
    {
        return $this->_getRepository()->searchPageDto('listGridCabecalho', $dtoSearch);
    }


}
