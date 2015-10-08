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
 * Classe para Service de VinculoFuncional
 *
 * @package  Minuta
 * @category Service
 * @name     VinculoFuncional
 * @version  1.0.0
 */

class VinculoFuncional extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * @var string
     */
    protected $_entityName = 'app:VwVinculoFuncional';

    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function findbyPessoaVinculo($dtoSearch)
    {
        $entity = $this->_getRepository('app:VwVinculoFuncional')
        ->findBy(array('sqPessoa' => $dtoSearch->getSqPessoa()));
        return $entity ? $entity[0] : NULL;
    }
}
