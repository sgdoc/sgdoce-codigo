<?php
/*
 * Copyright 2011 ICMBio
 *
 * Este arquivo é parte do programa SIAL Framework
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
namespace test\br\gov\sial\core;
use \br\gov\sial\core\Version;
use \PHPUnit_Framework_TestCase as PHPUnit;

/**
 * @author J. Augusto <augustowebd@gmail.com>
 *
 * @covers \br\gov\sial\core\Version<extended>
 * */
class VersionTest extends PHPUnit
{
    private $_version;

    public function setUp ()
    {
        parent::setUp();
        $this->_version = new Version();
    }

    /**
     * @test
     */
    public function getVersion ()
    {
        $refer = new \ReflectionObject($this->_version);
        $this->assertEquals($refer->getConstant('SIAL_VERSION'), $this->_version->get());
    }
}