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

namespace Sica\Model\Repository;

use Sica\Model\Entity;

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
class TipoPessoa extends \Sica_Model_Repository
{

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
                ->from('app:TipoPessoa', 'tp')
                ->orderBy('tp.noTipoPessoa');

        if ($sqTipoPessoa) {
            $query->andWhere($query->expr()->in('tp.sqTipoPessoa', $sqTipoPessoa));
        }

        $result = $query->getQuery()->getResult();
        $itens = array('' => 'Selecione uma opção');

        foreach ($result as $item) {
            $itens[$item['sqTipoPessoa']] = $item['noTipoPessoa'];
        }

        return $itens;
    }

}