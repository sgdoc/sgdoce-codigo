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
namespace br\gov\sial\core\mvcb\view\helper;
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\lang\TFile,
    br\gov\sial\core\util\file\James,
    br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage helper
 * @name Downloader
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Downloader extends SIALAbstract
{
    /**
     * Helper que efetua o download.
     * @param br\gov\sial\core\lang\TFile
     * @return String
     * @todo Retirar os headers setados de forma manual
    */
    public function downloader (ValueObjectAbstract $voFile)
    {
        $file     = James::factory($voFile)->fileDownloadRecover();
        header('Content-Type: ' . $file->getType());
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . $file->getName());
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Content-length: ' . $file->getSize());
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        print $file->getContent();
    }
}