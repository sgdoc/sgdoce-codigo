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
require_once dirname(__FILE__) . '/SecureImage/securimage.php';

class Sica_Captcha_Adapter
{
    private $_captcha = NULL;

    /**
     * Construtor da Captcha
     */
    public function __construct ()
    {
        $this->_captcha = new Securimage();
        $this->_captcha->code_length = 7;
    }

    public function render()
    {
        $this->_captcha->show();
    }

    public function audioPath ($path)
    {
        $this->_captcha->audio_path = $path;
        return $this;
    }

    public function audioFormat ($format)
    {
        $this->_captcha->audio_format = $format;
        return $this;
    }

    public function playAudio ()
    {
        $this->_captcha->outputAudioFile();
    }

    /**
     * Verifica se o codigo informado e o gerado pelo captcha
     * @param string $code
     */
    public function checkCode ($code)
    {
        return $this->_captcha->check($code);
    }

    public static function factory()
    {
        return new self();
    }




}