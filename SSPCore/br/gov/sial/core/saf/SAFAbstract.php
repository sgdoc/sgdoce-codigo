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
namespace br\gov\sial\core\saf;
use br\gov\sial\core\saf\ISAF,
    br\gov\sial\core\Renderizable,
    br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage saf
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class SAFAbstract extends SIALAbstract implements ISAF, Renderizable
{
    /**
     * @var string
     * */
    const T_SAF_DEFAULT_TYPE = 'html';

    /**
     * @var string
     * */
    const T_SAF_INVALID_ELEMENT = 'elemento indisponível';

    /**
     * @var string
     * */
    const T_SAF_INVALID_AREA = 'a área informada é inválida. aceito apenas: "head" ou "body"';

    /**
     * cria objeto application form
     *
     * @param string $appType
     * @return SIALApplicationFormAbstract
     * */
    public static function factory ($appType = self::T_SAF_DEFAULT_TYPE)
    {
        $class = $appType ?: get_called_class();

        if (self::T_SAF_DEFAULT_TYPE == $class) {
            $class = __NAMESPACE__  . self::NAMESPACE_SEPARATOR . 'SAFHTML';
        }

        return new $class;
    }
}