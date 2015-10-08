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
 * Componente de auxílio para formulários em HTML
 *
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @subpackage HtmlForm
 * @name       ComboYear
 * @category   View Helper
 */
class Core_View_Helper_HtmlForm_ComboYear extends Core_View_Helper_HtmlForm_ComboRange
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->setMin(1900)
            ->setMax(date('Y'));
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $attr
     */
    public function comboYear($name = null, $value = null, array $attr = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->comboRange($name, $value, $attr);
    }

    /**
     * @inheritdoc
     */
    public function setMin($min)
    {
        if ($min < 1900) {
            throw new InvalidArgumentException('Ano inválido.');
        }

        $this->_min = (int) $min;
        return $this;
    }
}
