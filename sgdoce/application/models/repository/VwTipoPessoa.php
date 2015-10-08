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

namespace Sgdoce\Model\Repository;

use Sgdoce\Model\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de Tipo Pessoa
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 TipoPessoa
 * @version	 1.0.0
 */
class VwTipoPessoa extends \Sgdoce\Model\Repository\Base
{

    /**
     * Variável que recebe o nome da entidade
     * @access protected
     * @var string
     * @name $enName
     */
    protected $enName = 'app:VwTipoPessoa';

    /**
     * Monta combo com campos  do Tipo Pessoa
     * @param int $sqTipoSinalizacao
     * @return array
     */
    public function getCombo(array $sqTipoPessoa = array())
    {
        $query = $this->_em
                ->createQueryBuilder()
                ->select('tp.sqTipoPessoa, tp.noTipoPessoa')
                ->from('app:VwTipoPessoa', 'tp')
                ->orderBy('tp.noTipoPessoa');

        if ($sqTipoPessoa) {
            $query->andWhere($query->expr()->in('tp.sqTipoPessoa', $sqTipoPessoa));
        }

        $result = $query->getQuery()->getArrayResult();
        $itens = array('' => 'Selecione uma opção');

        foreach ($result as $item) {
            $itens[$item['sqTipoPessoa']] = $item['noTipoPessoa'];
        }

        return $itens;
    }

    /**
     * Obtém tipo pessoa
     * @return array
     */
    public function getTipoPessoa()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('e')
        ->from($this->enName, 'e')
        ->orderBy('e.sqTipoPessoa', 'asc');

        return $query->getQuery()->getResult();
    }

    /**
     * Obtém dados para combo tipo pessoa
     * @return array
     */
    public function comboTipoPessoa()
    {
        $data = $this->getTipoPessoa();
        $out = array('' => 'Selecione uma opção');
        foreach ($data as $item) {
            $out[$item->getSqTipoPessoa()] = $item->getNoTipoPessoa();
        }
        return $out;
    }

}