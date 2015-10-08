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
 * @package     Core
 * @subpackage  View
 * @subpackage  Helper
 * @name         TemplateType
 * @category    View Helper
 */
class Core_View_Helper_TemplateType extends Zend_View_Helper_Abstract
{
    public function templateType($array = NULL,$posição = NULL, $atributes  = NULL)
    {
        if(isset($array[$posição])) {
            foreach($array[$posição] as $value):?>
            <div class="controls">
            <label class="checkbox inline">
                
                
                <input type="checkbox" class="<?php echo $atributes['class'];?>" id="sqPadraoModeloDocumentoCam"
                       name="sqPadraoModeloDocumentoCam[]"
                       value="<?php echo $value['sqPadraoModeloDocumentoCam']?>"
                       <?php echo ($value['checked']) ? 'checked="checked"' : ''?>>
                <?php echo $value['noCampo']?>
                <span style="display: none;" class="help-block legendHelp<?php echo $value['sqPadraoModeloDocumentoCam']?>"></span>
            </label><br>
            </div>
            <?php endforeach;
        }
    }
}