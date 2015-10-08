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

/**
 * SISICMBio
 *
 * Classe para Repository de Estado
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Estado
 * @version      1.0.0
 * @since        2012-06-26
 */

class Estado extends \Sica_Model_Repository
{
    /**
     * Obtem dados para montar a Combo Estado
     * @param int $pais
     * @param string $estado
     * @return array
     */
    public function comboEstado($pais = NULL, $estado = NULL)
    {
        $fields = array(
            'e.sqEstado'
        );

        if ($estado) {
            $fields[] = 'e.sgEstado';
        } else {
            $fields[] = 'e.noEstado';
        }

        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select($fields)
                ->from('app:Estado', 'e')
                ->orderBy('e.noEstado');

        if ($pais) {
            $queryBuilder->innerJoin('e.sqPais', 'p')
                    ->where('p.sqPais = :pais')
                    ->setParameter('pais', $pais);
        }

        $result = $queryBuilder->getQuery()->getResult();

        $message = \Zend_Registry::get('Zend_Translate');
        $out = array('' => $message->_('label-select'));

        foreach ($result as $item) {
            $out[$item['sqEstado']] = $item[$estado ? 'sgEstado' : 'noEstado'];
        }

        return $out;
    }

}