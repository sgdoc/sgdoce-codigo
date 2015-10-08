<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\mvcb\view;
use br\gov\sial\core\saf\SAFHTML,
    br\gov\sial\core\mvcb\view\Slot,
    br\gov\sial\core\mvcb\view\exception\ViewException;

/**
 * SIAL
 *
 * view em Html
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage view
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Html extends ViewAbstract
{
    /**
     * @var string
     * */
    const T_TYPE = 'html';

    /**
     * extensao padrao do arquivo viewScript
     *
     * @var string
     * */
    const T_EXTENSION = '.phtml';

    /**
     * @var string
     * */
    const T_VIEWABSTRACT_STR_SCRIPT_UNAVAILABLE = 'Html::%s informado não está disponível';

    /**
     * @param string[] $config
     * */
    public function __construct (array $config = array())
    {
        parent::__construct(self::T_TYPE, $config);
    }

    /**
     * imprime o conteudo indicado
     *
     * @param string $key
     * @return void
     * @throws IndexOutOfBoundsException
     * */
    public function output ($key, $format = NULL)
    {
        $content = $this->get($key);

        if (NULL != $format) {
            $content = sprintf($format, $content);
        }

        echo $content;
    }

    /**
     * cria instancia do SIAL Application Form (SAF)
     *
     * @return ViewAbstract
     * */
    public function saf ()
    {
        $this->_saf = new SAFHTML;

        return $this;
    }

    /**
     * inicializa Slot de Html
     * */
    public function initSlot ()
    {
        $this->_slot = new Slot($this);
    }

    /**
     * renderiza o template informado por $script
     *
     * @param string $template
     * @return string
     * @throws ViewException
     * @todo implementar verificacao de permissao de manipulacao no viewScript (leitura)
     * */
    public function render ($script)
    {
        if (TRUE == self::$forceMime) {
            $this->mime();
        }

        # throws ViewException
        $filename = $this->_getScriptFullPath($script);

        ViewException::throwsExceptionIfParamIsNull(!empty($filename), sprintf(self::T_VIEWABSTRACT_STR_SCRIPT_UNAVAILABLE, $script), 0);

        ob_start();
            require $filename;
            $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /*
     * @param string $script
     * */
    private function _getScriptFullPath ($script)
    {
        $script = ucfirst($script);
        $type   = pathinfo($script, PATHINFO_EXTENSION);

        if(self::T_EXTENSION != $type) {
            $script .= self::T_EXTENSION;
        }

        $filename = NULL;

        foreach($this->getScriptPaths() as $path) {

            $filename = $path . DIRECTORY_SEPARATOR . $script;

            if (is_file($filename)) {
                return $filename;
            }
        }

        return NULL;
    }
}