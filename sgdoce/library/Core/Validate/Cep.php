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
 * @category   Validate
 * @package    Core
 * @subpackage Validate
 * @name       Cep
 */
class Core_Validate_Cep extends Zend_Validate_PostCode
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->_locale = 'pt_BR';

        $this->setFormat(Zend_Locale::getTranslation(
            'BR',
            'postaltoterritory',
            $this->_locale
        ));
    }

    public function setLocale($locale = null)
    {
        throw new Core_Validate_Exception('Chamada deste método não permitida');
    }
}