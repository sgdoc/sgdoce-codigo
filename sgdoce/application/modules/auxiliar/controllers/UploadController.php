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

/**
 * Classe para Controller de Upload de Arquivos
 *
 * @package  Artefato
 * @category Controller
 * @name     Upload
 * @version  1.0.0
 */
class Auxiliar_UploadController extends \Zend_Controller_Action
{
    /**
     * @return void
     */
    public function tmpAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $result = $this->_helper->upload->send();
            echo \Zend_Json_Encoder::encode($result);
        } catch (\Exception $exp) {
            $error = array(
                'jsonrpc' => '2.0',
                'id' => 'id',
                'error' => array(
                    'code' => $exp->getCode(),
                    'message' => $exp->getMessage()
                )
            );
            echo \Zend_Json_Encoder::encode($error);
        }
    }
}