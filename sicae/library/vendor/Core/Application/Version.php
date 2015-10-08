<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * @category   Application
 * @package    Core
 * @subpackage Application
 * @name       Version
 */
class Core_Application_Version
{
    /**
     * @return 
     */
    public static function getVersionFromPath($path)
    {
        $version = '';
        #vem de um ReleaseNotes.txt?
        if (file_exists($path) && 1 === preg_match('/release.*notes/i', $path)) {
            $releaseNotes = file_get_contents($path);
            $result = array();
            preg_match('/\*version:(.*)/i', $releaseNotes, $result);
            if (isset($result[1])) {
                $version = trim($result[1]);
            }
        } else {
            trigger_error('Passe um caminho válido com o ReleaseNotes.txt', E_USER_ERROR);
        }
        return $version;
    }

}
