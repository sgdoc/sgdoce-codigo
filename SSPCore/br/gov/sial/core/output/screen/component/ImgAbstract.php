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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name ImgAbstract
 * */
abstract class ImgAbstract extends ComponentAbstract implements IBuild
{
    /**
     * Imagem padrão para testes
     * @var string
     */
    const T_IMGABSTRACT_DEFAULT_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIwAAACMCAYAAACuwEE+AAADtklEQVR4nO3Yb1PaWBiG8X7/j3KChSIFBFpFp2xx3CqotDO7LKDAWkjyFe6+SGi36M70fqH86fXiN+Mgepg8V0hOXqVpKuBXvdr0B8BuIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAaWHQom1t1lW0eVokIICiHozR9jxY/el+jhS1vF/D2lzkjL/HfJYqz+WV1voqAQIpXqp7oafVWyE+tvhx0KZqG/2mWVqw3VS/8/sGR+q+OD8H2o3weWzHX77iB7vdzU+2Y5+7nQ1PUs2YH1t8MOBbOy1LD9+umBJTNdtw4USm2dv3/908Di+wtVQlAIVV3NEqXJXP16NtTD87GmN+90EIJCpavxMlV890m1EBSiui7v42def/LEN9V22qNgYk17DRVCWZ3hXMOz4k8D+/q5mZ3RpQ8aLbP/M+qUsteObvUQzzU4zr4B3nYHuqhHCqGgRm+6NsxnWj/Z9HH9zYKJ7y9Vj4Iq3ZGW6XJtYInm/Vp+OehqEmcDnpznl4XqlWZJquThi06KPy4nhda1Zo8G+Xzrb/7Y/jbBJJp+eqsQIh0eNdVsNlRdDb5YU+vDZ00Gv3KGx5p08yGGok7/Xrzw+ttvz4IJT6v8qbvJ6h6ipt7aPUQ5v4eIp1c6iv77d+caL19u/c0f270KJtZ9v6N2+0SNfJcSyk2dtE/1cTBb25quXxJSpclcN62CQgiKDls6blUU5buU/ixRGt/pMr9vafaGusnvZw4//qPFS6y/8eO7d8H8OLPXPd7ePjGwNHsO0jurq7R6DlJbPQeJNbmoZsNs9DSNUyX/DvLtcVmd4eKZ19/0sd3LYLANCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgYVgYCEYWAgGFoKBhWBgIRhYCAYWgoGFYGAhGFgIBhaCgeUbSGMbBSALyykAAAAASUVORK5CYII=';

    /**
     * Mensagem padrão para um estilo não suportado
     */
    const T_IMGABSTRACT_INVALID_CSS_CLASS = 'A classe CSS informada é inválida. São aceitas: .img-rounded, .img-circle e .img-polaroid.';

    /**
     * Agregador do componente img
     * @var string
     */
    protected $_img;

    /**
     * @return ImgAbstract
     */
    public function build ()
    {
        return $this;
    }

    /**
     * @param stdClass $config
     * @return ComponentAbstract
     */
    public static function factory ($config , $type = 'html')
    {
        $namespace = self::NSComponent('img', $type);

        return new $namespace($config);
    }

    /**
     * @return string
     */
    public function render ()
    {
        return $this->_img->render();
    }

}