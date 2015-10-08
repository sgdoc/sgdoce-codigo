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
class Default_IndexController extends Zend_Controller_Action
{
    const INTERVAL = 60;
    /**
     * @var string
     */
    protected $_service = 'Assunto';

    public function indexAction()
    {
        $zsn = new Zend_Session_Namespace('interval');
        $zsn->interval = self::INTERVAL;

        if (! \Core_Integration_Sica_User::getUserProfileExternal()) {
            $this->_redirect('/artefato/area-trabalho/index/tipoArtefato/1/caixa/minhaCaixa');
        }
    }

    public function forbiddenAction() {}
}
