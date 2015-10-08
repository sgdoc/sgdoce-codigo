<?php
/*
 * Copyright 2011 ICMBio
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
namespace br\gov\mainapp\application\libcorp\cepLogradouro\persist\database;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\cepBairro\valueObject\CepBairroValueObject,
    br\gov\mainapp\application\libcorp\cepLocalidade\valueObject\CepLocalidadeValueObject,
    br\gov\mainapp\application\libcorp\cepLogradouro\valueObject\CepLogradouroValueObject;

/**
  * SISICMBio
  *
  * @name CepLogradouroPersist
  * @package br.gov.icmbio.sisicmbio.application.libcorp.cepLogradouro.persist
  * @subpackage database
  * @author Fabio Lima <fabioolima@gmail.com>
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class CepLogradouroPersist extends ParentPersist
{
}