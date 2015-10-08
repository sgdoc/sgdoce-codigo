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

namespace br\gov\sial\core\util\document;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage document
 * @name Property
 * @author michael fernandes <michael.rodrigues@icmbio.gov.br>
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
class Property extends SIALAbstract
{
    /**
     * @var string
     */
    private $_name = 'Documento';

    /**
     * retorna o nome da propriedade
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * seta o nome da propriedade
     * @param string $name
     * @return Property
     */
    public function setName ($name)
    {
        $this->_name = $name;
        return $this;
    }
}