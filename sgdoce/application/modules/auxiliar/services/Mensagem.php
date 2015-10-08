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
 * Classe para Service de Mensagem
 *
 * @category Service
 * @package  Auxiliar
 * @name         Mensagem
 * @version  1.0.0
 */
class Mensagem extends \Core_ServiceLayer_Service_Crud
{
    /**
     * @var string
     */
     protected $_entityName = 'app:Mensagem';

     /**
      * Obtém a mensagem geral cadastrada ou uma mensagem geral em branco
      * @return Mensagem Mensagem
      */
     public function getMensagemGeral()
     {
        $result     = $this->_getRepository()->getMensagemGeral();
        if ($result) {
            return $result;
        } else {
             return $this->getNewEntity();
        }
     }

     /**
      * Prepara uma entidade para ser salva
      * @param string $entityName Nome da entidade
      */
     public function setOperationalEntity($entityName = NULL)
     {
        $entityName = \Core_Util_Class::resolveNameEntity($this->options['entityName']);

        $this->_data['stMensagemAtiva'] = TRUE;

        // $sqAssunto e sqTipoDocumento = NULL == mensagem geral
        $sqAssunto = NULL;
        $sqTipoDocumento = NULL;

        if ( !isset($this->_data['msgGeral'])) {
            $sqAssunto = $this->_createEntityManaged(
                array(
                    'sqAssunto' => $this->_data['sqAssunto']
                ),
                'app:Assunto');
            $sqTipoDocumento = $this->_createEntityManaged(
                array(
                    'sqTipoDocumento' => $this->_data['sqTipoDocumento']
                ),
                'app:TipoDocumento'
                );
        }
        $this->_data['sqAssunto'] = $sqAssunto;
        $this->_data['sqTipoDocumento'] = $sqTipoDocumento;
        unset($this->_data['sqAssunto_autocomplete']);
     }

     /**
      * Método que retorna a lista de objetos da grid
      * @param  array $params Dados da requisição
      * @return array         Resultados
      */
    public function listGrid(\Core_Dto_Search $params)
    {
        $result = $this->_getRepository()->searchPageDto('pesquisaMensagem', $params);

        return $result;
    }

    /**
     * Procura se já existe uma mensagem cadastrada com o mesmo tipo de documento e assunto
     * @param  array $params Dados da requisição
     * @return array         Mensagens encontradas
     */
    public function findMessage($params)
    {
        $result     = $this->_getRepository()->findMessage($params);

        return $result;
    }

    /**
     * Verifica se já existe uma mensagem com o mesmo tipo de documento, mesmo assunto e já ativa
     * @param  array $params Dados da requisição
     * @return array         Mensagens encontradas
     */
    public function findMessageAtiva($params)
    {
        $result     = $this->_getRepository()->findMessageAtiva($params);
        return $result;
    }

    /**
     * Desativa todas as mensagems de um determinado tipo de documento e assunto.
     * @param  array $params Dados da requisição
     * @return array         [description]
     */
    public function deactivateMessages($params)
    {
        $result     = $this->_getRepository()->deactivateMessages($this->_data);
        return $result;
    }

    /**
     * Ativa uma mensagem
     * @param  array $params Dados da requisição
     * @return string         Sucesso
     */
    public function ativarMensagem($params)
    {
        $entity = $this->find($params['id']);
        $this->_data = array('sqAssunto' => $entity->getSqAssunto(),'sqTipoDocumento'=>$entity->getSqTipoDocumento());
        $this->deactivateMessages($params);
        $entity->setStMensagemAtiva(TRUE);

        $emgr = $this->getEntityManager();
        $emgr->persist($entity);
        $emgr->flush();
        return 'mensagem ativada';
    }

    /**
     * Desativa uma mensagem
     * @param  array $params Dados da requisição
     * @return string         Sucesso
     */
    public function desativarMensagem($params)
    {
        $sequence = $params['id'];
        $entity = $this->find($sequence);
        $entity->setStMensagemAtiva(FALSE);

        $emgr = $this->getEntityManager();
        $emgr->persist($entity);
        $emgr->flush();
       return 'mensagem desativada';
    }

    /**
     * Altera o status de ativo de uma mensagem
     * @param  array $params Dados da requisição
     * @return string         Mensagem
     */
    public function switchStatus($params)
    {
        if ($params['stMensagemAtiva'] == 1) {
            return $this->ativarMensagem($params);
        } else {
            return $this->desativarMensagem($params);
        }
    }

    /**
     * Executa as validações e testes antes de salvar uma mensagem
     * @param string $service Nome do serviço
     */
    public function preSave ($service)
    {
        if (isset($this->_data['desativarAnteriores']) && $this->_data['desativarAnteriores'] == '1') {
            $this->deactivateMessages($this->_data);
        }

        if (!isset($this->_data['msgGeral'])) {
            $sqTipoDocumento = $this->_data['sqTipoDocumento']->getSqTipoDocumento();
            if ($sqTipoDocumento == 0 || empty($sqTipoDocumento)) {
                throw new \Core_Exception_ServiceLayer("Tipo de Documento não selecionado.");
            }
        }
    }

}
