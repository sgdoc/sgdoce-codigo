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

/**
 * SISICMBio
 *
 * Classe para Repository de Municipio
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Municipio
 * @version      1.0.0
 * @since        2012-06-26
 */
class VwMunicipio extends \Core_Model_Repository_Base
{
    private $_enName = 'app:VwMunicipio';

    /**
     * Obtém dados para popular a Combo Municipio
     * @return array
     * @param int $estado
     */
    public function comboMunicipio($estado = NULL ,$fEstado = FALSE)
    {
        $fields = array(
            'm.sqMunicipio',
            'm.noMunicipio'
        );

        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select($fields)
                ->from($this->_enName, 'm');

        if($estado){
            $queryBuilder->innerJoin('m.sqEstado', 'e')
                   ->andWhere('e.sqEstado = :estado')
                   ->setParameter('estado', $estado);
        }
        $queryBuilder->orderBy('m.noMunicipio');
        if($fEstado){
            $res = $queryBuilder->getQuery()->getArrayResult();
            $out = array('' => 'Selecione uma opção');

            foreach ($res as $item) {

                $out[$item['sqMunicipio']] = $item['noMunicipio'];
            }

            return $out;
        }
        $res = $queryBuilder->getQuery()->getResult();
        return $res;
    }

    /**
     * método que pesquisa
     * @param array $params
     * @return array $out
     */
    public function searchMunicipio ($dto)
    {
        $search       = mb_strtolower($dto->getNoMunicipio(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('m.noMunicipio'));

        $query = $queryBuilder->select('m')
            ->from($this->_enName, 'm')
            ->andWhere('m.sqEstado = :sqEstado')
            ->setParameter('sqEstado', $dto->getSqEstado())
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('m.noMunicipio');

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $key => $item) {
            $out[$item['sqMunicipio']] = $item['noMunicipio'];
        }

        return $out;
    }
}