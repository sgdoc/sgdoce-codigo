<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Vinculo Funcional
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwVinculoFuncional
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwVinculoFuncional extends \Core_Model_Repository_Base
{
    protected $_entityName = 'app:VwVinculoFuncional';

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchPessoa (\Core_Dto_Abstract $dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('p.noPessoa'));

        $query = $queryBuilder->select('vprof, p')
            ->from('app:VwVinculoFuncional', 'vprof')
            ->innerJoin('vprof.sqPessoa', 'p')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );

        if ($dto->hasExtraParam()) {
            $query->andWhere('vprof.sqUnidadeLotacao = :sqUnidadeLotacao')
                ->setParameter('sqUnidadeLotacao', $dto->getExtraParam());
        }

        $query->orderBy('p.noPessoa');

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        foreach ($res as $item) {
             $out[$item['sqPessoa']['sqPessoa']] = $item['sqPessoa']['noPessoa'];
        }

        return $out;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function searchFuncionarioIcmbio (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('p.sqTipoPessoa')
                     ->from('app:VwPessoa', 'p')
                     ->where($queryBuilder->expr()->lower('p.noPessoa')." = '".strtolower($dto->getNoPessoa())."'");
        $res = $queryBuilder->getQuery()->execute();

        if(count($res)){
            return $res[0];
        } else {
            return $res;
        }
    }
}