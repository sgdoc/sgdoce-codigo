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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\Img as Image,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\ImgAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name Img
 * */
class Img extends ImgAbstract implements IBuild
{
    /**
     * Classe css para círculo
     * @var string
     */
    const T_IMG_CIRCLE = 'img-circle';

    /**
     * Classe css para quadrado
     * @var string
     */
    const T_IMG_SQUARE = 'img-square';

    /**
     * Classe css para quadrado com bordas (polaroid)
     * @var string
     */
    const T_IMG_POLAROID = 'img-polaroid';

    /**
     * Construtor de componente img
     * @param \stdClass $param
     */
    public function __construct ($param)
    {
        $this->isValid($param->class);

        $this->_img = new Image($param->src, $this->safeToggle($param, 'alt'));
        $this->_img->toggle($param->src, self::T_IMGABSTRACT_DEFAULT_IMAGE);
        $this->_img->addClass($param, 'class');
    }

    /**
     * Verifica se o tipo de classe css informada é válida
     */
    private function isValid ($css)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull($css != self::T_IMG_CIRCLE ||
                                                               $css != self::T_IMG_SQUARE ||
                                                               $css != self::T_IMG_POLAROID,
                self::T_IMGABSTRACT_INVALID_CSS_CLASS);
    }

    /**
     * @return \br\gov\sial\core\output\screen\html\Img
     */
    public function build ()
    {
        return $this;
    }
}