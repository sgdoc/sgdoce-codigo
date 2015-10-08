<?php
/*
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
/**
 * Classe para Controller de TipoUnidadeOrg
 *
 * @package  Auxiliar
 * @category Controller
 * @name TipoUnidadeOrg
 * @version	 1.0.0
 */
class Auxiliar_TipoUnidadeOrgController extends \Core_Controller_Action_CrudDto
{
    /**
     * @var string
     */
    protected $_service = 'TipoUnidadeOrg';

    /**
     * Retorna os tipos de unidades organizacionais cadastrados em formato json
     * @return string
     */
    public function searchTipoUnidadeOrgAction()
    {
        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result =  $this->getService()->searchTipoUnidadeOrg($dtoSearch);
        $this->_helper->json($result);
    }

    /**
     * Retorna os tipos de unidades organizacionais cadastrados em formato json
     * @return string
     */
    public function searchUnidadeOrgAction()
    {
        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result =  $this->getService()->searchUnidadeOrg($dtoSearch);
        $this->_helper->json($result);
    }

    /**
     * Retorna os dados da pessoa.
     * @return json
     */
    public function searchPessoaAction()
    {
        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result =  $this->getService()->searchPessoa($dtoSearch);
        $this->_helper->json($result);
    }
}
