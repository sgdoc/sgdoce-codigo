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
namespace br\gov\sial\core\mvcb\business\exception;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage exception
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class BusinessException extends \br\gov\sial\core\exception\SIALException
{
    /**
     * Mensagem do SIAL.
     *
     * @var string
     * */
    const T_BUSINESS_STR_CLASS_NOT_FOUND = 'A classe de negócio %s não foi encontrada.';

    /**
     * Lança exception para classe de business não encontrada.
     *
     * @param string $businessName
     * @return BusinessException
     * */
    public static function businessNotImplemented ($businessName)
    {
        return new self(sprintf(self::T_BUSINESS_STR_CLASS_NOT_FOUND, $businessName));
    }
}