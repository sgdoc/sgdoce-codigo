<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

class Sica_Model_Repository extends Core_Model_Repository_Base
{
    public function toggleStatus($id, $status)
    {
        if ($status) {
            return $this->inactive($id);
        } else {
            return $this->active($id);
        }
    }

    public function active($id)
    {
        return $this->_setStatus($id, 1);
    }

    public function inactive($id)
    {
        return $this->_setStatus($id, 0);
    }

    protected function _setStatus($id, $status)
    {
        $entity = $this->_em->getRepository($this->_entityName)->find($id);
        if(method_exists($entity,'setStRegistroAtivo')){
            $entity->setStRegistroAtivo($status);
        } elseif( method_exists($entity,'setStAtivo')){
            $entity->setStAtivo($status);
        }

        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    /**
     * Monta combo com campos do Tipo Pessoa
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array The objects.
     * @return array
     */
    public function getComboDefault(array $criteria = array(), array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $itens = array();
        $result = $this->_em->getRepository($this->getClassName())->findBy($criteria, $orderBy, $limit, $offset);

        $className = $this->getClassName();
        $metadata = $this->_em->getClassMetadata(get_class(new $className()));
        $fieldNames = $metadata->getFieldNames();

        $getSqCodigo = 'get' . ucfirst($fieldNames[0]);
        $getNoDescricao = 'get' . ucfirst($fieldNames[1]);

        foreach ($result as $item) {
            $itens[$item->{$getSqCodigo}()] = $item->{$getNoDescricao}();
        }

        return $itens;
    }
    
    public function translate($text)
    {
        $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        $to = 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC';

        return str_replace($this->mb_str_split($from), $this->mb_str_split($to), $text);
    }
    
    private function mb_str_split($text) 
    {
        return preg_split('~~u', $text, null, PREG_SPLIT_NO_EMPTY);
    }
}
