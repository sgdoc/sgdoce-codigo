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
namespace Auxiliar\Service;
/**
 * Classe responsável pela validação das regras de negócios do Caso de Uso VincularPrazo
 *
 * @package  Auxiliar
 * @category Service
 * @name     Vincularprazo
 * @version  0.0.1
 */
class Vincularprazo extends \Core_ServiceLayer_Service_Crud
{
    /**
     * @access protected
     * @var string
     */
    protected $_entityName = 'app:IndicacaoPrazo';

    /**
     * Auxilia na criação das dependências da entidade
     * @access public
     * @param string $entityName
     */
    public function setOperationalEntity($entityName = NULL)
    {
        if ($this->_data['inPrazoObrigatorio'] == 'TRUE') {
            $this->_data['nuDiasPrazo'] = '';
            $this->_data['inDiasCorridos'] = '';
        }
        $this->_data['sqTipoDocumento'] = \Zend_Filter::filterStatic($this->_data['sqTipoDocumento'], 'null');
        if ($this->_data['sqTipoDocumento']) {
            $this->_data['sqTipoDocumento'] = $this->_createEntityManaged(
                   array('sqTipoDocumento' => $this->_data['sqTipoDocumento']), 'app:TipoDocumento');
        }
        $this->_data['sqAssunto'] = $this->_createEntityManaged(
               array('sqAssunto' => $this->_data['sqAssunto']), 'app:Assunto');
        $this->_data['nuDiasPrazo'] = \Zend_Filter::filterStatic($this->_data['nuDiasPrazo'], 'null');
        $this->_data['inDiasCorridos'] = \Zend_Filter::filterStatic($this->_data['inDiasCorridos'], 'null');
    }

    /**
     * Faz a consulta para grid.
     * @access public
     * @param array $params
     * @return QueryBuilder
     */
    public function listGrid(\Core_Dto_Search $params)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        $result     = $repository->searchPageDto('searchIndicacaoPrazo', $params);

        return $result;
    }

    /**
     * Verifica se existe vinculação do prazo para o assunto e/ou tipo de documento.
     * @access public
     * @param integer $sqAssunto
     * @param integer $sqTipoDocumento
     * @param integer $sqIndicacaoPrazo
     * @return integer
     */
    public function hasVinculacaoPrazo ($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo)
    {
        $repository = $this->getEntityManager()->getRepository($this->_entityName);
        return $repository->hasVinculacaoPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo);
    }

    /**
     * Desvincula do prazo para o assunto e/ou tipo de documento.
     * @access public
     * @param integer $sqAssunto
     * @param integer $sqTipoDocumento
     * @param integer $sqIndicacaoPrazo
     */
    public function unlinkPrazo ($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo)
    {
        $return = $this->getEntityManager()
                       ->getRepository($this->_entityName)
                       ->delVinculacaoPrazo($sqAssunto, $sqTipoDocumento, $sqIndicacaoPrazo);
    }
}
