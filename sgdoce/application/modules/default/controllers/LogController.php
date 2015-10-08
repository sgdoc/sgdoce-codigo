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
class Default_LogController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $model = new \Core_Doctrine_DBAL_Event_Listeners_Logger();
        $logs = $model->getTrilhaAuditoria(
            array(
                'paramsColumn' => array(
                    array('column' => 'sq_sistema', 'condition' => '=', 'value' => \Core_Integration_Sica_User::getUserSystem())
                )
            )
        );
        $this->view->logs = $logs;

    }
    public function detalheAction()
    {
        $params = $this->_request->getParams();
        if (!empty($params['id'])) {
            $model = new \Core_Doctrine_DBAL_Event_Listeners_Logger();
            $log = $model->getTrilhaAuditoria(
                array(
                    'paramsColumn' => array(
                        array('column' => 'sq_sistema', 'condition' => '=', 'value' => \Core_Integration_Sica_User::getUserSystem()),
                        array('column' => 'sq_auditoria', 'condition' => '=', 'value' => $params['id'])
                    )
                )
            );
            try {

                if (!empty($log)) {
                    $xml = new SimpleXMLElement($log[0]['xmTrilha']);
                    $xmlCampos = array();
                    foreach ($xml->tabela->coluna as $campo) {
                        $coluna = (string)$campo->nome;
                        $xmlCampos[$coluna] = (string)$campo->valor;
                    }
                    ksort($xmlCampos);
                    $this->view->trilha = $log[0];
                    $this->view->colunas = $xmlCampos;
                    $this->view->xml = $xml;
                    $this->view->tabela = $this->getDadosTabela($xml);
                }
            } catch (Exception $e) {
                $this->view->exception = TRUE;
                $this->view->mensagem = $e->getMessage();
                $this->view->stringXml = new SimpleXMLElement($log[0]['xmTrilha']);
            }
        } else {
            $this->_redirect('/log');
        }
    }

    public function getDadosTabela($xml)
    {
        $model = new \Core_Doctrine_DBAL_Event_Listeners_Logger();
        $chave = array();

        foreach ($xml->tabela->coluna as $campo) {
            if (strpos($campo->nome, 'sq_') !== false) {
                $chave[] = $campo;
            }
        }

        return $model->getDadosTabela($xml->tabela->nome, $chave);
    }

    public function abracadabraAction()
    {
        $message = \Core_Messaging_Manager::getGateway('User');
        try{

            $token = $this->getRequest()->getParam('token',NULL);
            $step  = $this->getRequest()->getParam('step','MigrationImageRequested');

            if (md5($token) != 'c73722b6f4c9637a47fb87f667582745') {
                throw new Exception('Token inválido.');
            }

            if (! $step ) {
                throw new Exception('Step não definido');
            }

            $lockFilename = '.~robot_' . $step . '.lock';
            $lockFullFilename = APPLICATION_PATH .'/../data/' . $lockFilename;

            if (file_exists($lockFullFilename)){
                if (unlink($lockFullFilename)) {
                    $message->addSuccessMessage($lockFilename . ' Removido.');
                }else{
                    throw new Exception('Erro ao excluir o loc: ' . $lockFullFilename);
                }
            }else{
                $message->addAlertMessage('File lock not found: ' . $lockFullFilename);
            }

        } catch (Exception $ex) {
            $message->addErrorMessage($ex->getMessage());
        }

        $message->dispatchPackets();
        $this->_redirect('log');
    }
}
