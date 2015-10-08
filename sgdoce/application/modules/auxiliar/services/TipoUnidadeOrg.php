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
namespace Auxiliar\Service;
/**
 * Classe para Service de Assunto
 *
 * @package  Auxiliar
 * @category     Service
 * @name         Assunto
 * @version  1.0.0
 */
class TipoUnidadeOrg extends \Core_ServiceLayer_Service_Crud
{
    /**
     * @var string
     */
    protected $_entityName = 'app:VwTipoUnidadeOrg';

    /**
     * Retorna os tipos de unidades organizacionais cadastrados em formato json
     * @return json
     */
    public function searchTipoUnidadeOrg(\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository()->searchTipoUnidadeOrg($dto);
    }

    /**
     * Retorna unidades organizacionais cadastrados em formato json
     * @return json
     */
    public function searchUnidadeOrg(\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository()->searchUnidadeOrg($dto);
    }

    /**
     * Retorna pessoa formato json
     * @return string
     */
    public function searchPessoa(\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository('app:VwProfissional')->searchPessoa($dto);
    }

    /**
     * Retorna unidade por tipo cadastrados em formato json
     * @return string
     */
    public function searchUnidadeOrgPorTipo(\Core_Dto_Abstract $dto)
    {
        return $this->_getRepository()->searchUnidadeOrgPorTipo($dto);
    }

}
