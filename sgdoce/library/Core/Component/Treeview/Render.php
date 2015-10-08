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

use Core_Component_Treeview_Items as Items;
use Core_Component_Treeview_Item as Item;

/**
 * @package    Core
 * @subpackage Component
 * @subpackage Treeview
 * @name       Render
 * @category   Component
 */
class Core_Component_Treeview_Render
{
    private $_root = null;

    /**
     * @param Core_Component_Treeview_Item $root
     */
    public function __construct (Item $root)
    {
        $this->_root = $root;
    }

    /**
     * @return string
     */
    public function toHTML ()
    {
        
\Zend_Wildfire_Plugin_FirePhp::send($this->_root,'DEBUG',\Zend_Wildfire_Plugin_FirePhp::WARN);
        
        $html = <<<HTML
<ul>
    <li>201500000000
        <ul>
            <li>
                <b>
                    <i>20150000001</i>
                </b>
                <ul>
                    <li>20150000002</li>
                </ul>
            </li>
            <li>20150000003</li>
            <li>20150000004
                <ul>
                    <li>20150000005
                        <li>20150000006
                            <ul>
                                <li>20150000007</li>
                            </ul>
                        </li>
                    </li>
                </ul>
            </li>
            <li>20150000008</li>
        </ul>
    </li>
</ul>
HTML;
        return $html;
    }

    /**
     * @return string
     */
    public function toPlainText ()
    {
        $plainText = <<<PLAIN_TEXT
└┬ 20150000000
 ├┬ 20150000001
 │└─ 20150000002
 ├─ 20150000003
 ├┬ 20150000004
 │├─ 20150000005
 │└┬ 20150000006
 │ └─ 20150000007
 └─ 20150000008
PLAIN_TEXT;
        return $plainText;
    }

    /**
     * @return string
     */
    public function toJSON ()
    {
        $plainText = <<<JSON
{
    "text": "01500000000",
    "children": [
        {
            "text": "20150000001",
            "children": [
                {
                    "text": "20150000002",
                    "children": []
                }
            ]
        },
        {
            "text": "20150000003",
            "children": []
        },
        {
            "text": "20150000004",
            "children": [
                {
                    "text": "20150000005",
                    "children": []
                },
                {
                    "text": "20150000006",
                    "children": [
                        {
                            "text": "20150000007",
                            "children": []
                        }
                    ]
                }
            ]
        },
        {
            "text": "20150000008",
            "children": []
        }
    ]
}
JSON;
        return $plainText;
    }
}
