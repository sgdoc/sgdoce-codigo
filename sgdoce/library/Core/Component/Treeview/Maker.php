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

use Core_Component_Treeview_Item as Item;

/**
 * @package    Core
 * @subpackage Component
 * @subpackage Treeview
 * @name       Maker
 * @category   Component
 */
class Core_Component_Treeview_Maker
{
    /**
     * @return Core_Component_Treeview_Item
     */
    public function processData (array $data = array()/*, $textIndex, $valueIndex, $childrenIndex*/)
    {
        //@todo: Processa o $data e monta o $root
        $root = new Item('201500000000');

        $node1 = new Item('201500000001');
        $node2 = new Item('201500000002');
        $node3 = new Item('201500000003');
        $node4 = new Item('201500000004');
        $node5 = new Item('201500000005');
        $node6 = new Item('201500000006');
        $node7 = new Item('201500000007');
        $node8 = new Item('201500000008');

        $root->addChild($node1);
            $node1->addChild($node2);
        $root->addChild($node3);
        $root->addChild($node4);
            $node4->addChild($node5);
            $node4->addChild($node6);
                $node6->addChild($node7);
        $root->addChild($node8);

        return $root;
    }
}
