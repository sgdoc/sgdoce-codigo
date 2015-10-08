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
class Core_Dto_Mapping_Entity extends Core_Dto_Mapping
{
    protected function _setInput($attr, &$value)
    {
        if (!array_key_exists($attr, $this->_map)) {
            throw new UnexpectedValueException('Valor não mapeado.');
        }

        $entityName = $this->_map[$attr];

        if (is_array($entityName)) {
            $attr       = key($entityName);
            $entityName = $entityName[$attr];
        }

        if (!is_array($value)) {
            $value      = Core_Dto::factoryFromData(array($attr => $value), 'entity', array('entity' => $entityName));
            return;
        }

        $tempData = array();
        foreach ($value as $key => $data) {
            if (is_array($data)) {
                $tempData[] = Core_Dto::factoryFromData($data, 'entity', array('entity' => $entityName));
            } else {
                $value      = Core_Dto::factoryFromData($value, 'entity', array('entity' => $entityName));
                return;
            }
        }

        $value = $tempData;
    }

    public function has($attr)
    {
        if (!array_key_exists($attr, $this->_map)) {
            return FALSE;
        }

        return TRUE;
    }
}