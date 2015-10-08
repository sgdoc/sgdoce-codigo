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
namespace br\gov\sial\core\util\validate;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage validate
 * @name NotEmpty
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class NotEmpty extends Validate
{
    /**
     * {@inheritdoc}
     * retorna true se a string ou array não é vazio
     *
     * @return boolean
     * */
    public function isValid($suspicious)
    {
        $result = FALSE;
        switch (gettype($suspicious)) {
            case 'array':
                $result = $this->_array($suspicious);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'object':
                $result = $this->_object($suspicious);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'integer':
            case 'double':
            case 'boolean':
            case 'resource':
                $result = TRUE;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'string':
                $result = 0 < strlen($suspicious);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }

        return $result;
    }

    /**
     * faz a verificação para array
     * 
     * @param mixed
     * @return boolean
     * */
    private function _array ($suspicious)
    {
        return 0 < count($suspicious);
    }

    /**
     * faz a verificação para objeto
     * 
     * @param object
     * @return boolean
     * */
    public function _object ($suspicious)
    {
        if ($suspicious instanceof \stdClass) {
            return $this->_stdClass($suspicious);
        }

        $refClass = new \ReflectionClass($suspicious);
        $attrs = $refClass->getProperties();

        foreach ($attrs as $property) {
            $property->setAccessible(TRUE);

            if ($this->isValid($property->getValue($suspicious))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * faz a verificação para stdClass
     * 
     * @param stdClass $object
     * @return boolean
     * */
    private function _stdClass (\stdClass $object)
    {
        $result = FALSE;
        foreach ($object as $key => $val) {
            if (TRUE == (boolean) $val) {
                $result = TRUE;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
            }
        }
        return $result;
    }
}