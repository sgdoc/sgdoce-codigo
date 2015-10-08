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
 * Classe para Repository de Sistema Telefone
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Telefone
 * @version      1.0.0
 * @since        2012-06-26
 */

class VwIntegracaoSistema extends \Core_Model_Repository_Base
{

    /**
     * método para pesquisa de unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function sistemaAutoComplete (\Core_Dto_Search $dto,$entidade)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select('c')
            ->from($entidade, 'c');

        if (!is_null($dto->hasQuery())) {
            $search = mb_strtolower($dto->getQuery(),'UTF-8');
            $field  = $queryBuilder->expr()
                ->lower($queryBuilder->expr()->trim('c.nome'));

            $query->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );
        }
        $query->setMaxResults(10);

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $item) {
             $out[$item['codigo']] = $item['nome'];
        }

        return $out;
    }
}