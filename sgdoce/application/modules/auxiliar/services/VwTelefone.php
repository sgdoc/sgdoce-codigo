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

/**
 * SISICMBio
 *
 * Classe para Serviço Telefone
 *
 * @package      Corporativo
 * @subpackage     Servico
 * @name         Telefone
 * @version     1.0.0
 * @since        2012-06-26
 */

namespace Auxiliar\Service;

class VwTelefone extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Variavel para receber o nome da entidade
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:VwTelefone';

    public function listGrid(\Core_Dto_Search $dto)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result = $repository->searchPageDto('listGrid', $dto);

        return $result;
    }

    /**
     * Metódo que retorna os dados do Telefone
     * @return array
     */
    public function getDadosTelefone(\Core_Dto_Search $dtoSearch)
    {
        return $this->_getRepository()->getDadosTelefone($dtoSearch);
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return array
     */
    public function findTelefone($sqPessoa)
    {
        $result = $this->_getRepository()->findOneBy(array('sqPessoa' => $sqPessoa));
        if(!$result){
            $result = $this->_newEntity('app:VwTelefone');
        }
        return $result;
    }
}
