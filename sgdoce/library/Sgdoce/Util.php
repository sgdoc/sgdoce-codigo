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
 * @category    Version
 * @package     Core
 * @name        Version
 */
class Sgdoce_Util
{
    /**
     * Quebra a string da digital em numero e ano
     *
     * @param string $nuDigitalComAno 99999999999 exemplo 20140000007
     * @return array ['nuDigital' => 9999999, 'nuAno' => 9999] exemplo ['nuDigital' => 0000007, 'nuAno' => 2014]
     */
    public static function normalizeDigital($nuDigitalComAno)
    {
        return array(
            'nuDigital' => (integer) substr($nuDigitalComAno,-7),
            'nuAno'     => (integer) substr($nuDigitalComAno,0,4)
        );
    }

    public static function montaDigital($nuDigital, $nuAno)
    {
        //forma o numero da etiqueta com 7 digitos
        $nuEtiqueta = str_pad($nuDigital, 7, '0', STR_PAD_LEFT);

        //adiciona o ano e forma o numero com 11 digitos
        return str_pad($nuAno.$nuEtiqueta, 11,'0',STR_PAD_LEFT);
    }
}