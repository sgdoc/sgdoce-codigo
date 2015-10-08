<?php

namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository Tipo Documento
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwTipoDocumento
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwTipoDocumento extends \Core_Model_Repository_Base
{

    private $_enName = 'app:VwTipoDocumento';

    /**
     * Retorna lista de documentos da tabela do Corporativo
     *
     * @return array $out
     */
    public function listTipoDocumento()
    {
        $queryBuilder = $this->_em
            ->createQueryBuilder()
            ->select(array('td'))
            ->from($this->_enName, 'td')
            ->orderBy('td.noTipoDocumento', 'ASC');

        $out = array('' => 'Selecione uma opção');
        $res = $queryBuilder->getQuery()->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqTipoDocumento']] = $item['noTipoDocumento'];
        }

        return $out;
    }

    /**
     * Obtém os dados para combo tipo documento pessoa
     * @return array
     */
    public function getComboForSqPessoa($sqPessoa = null)
    {
        $sqTipoDocumento = $this->_em->createQueryBuilder()
            ->select('tpd.sqTipoDocumento')
            ->from('app:VwDocumento', 'd')
            ->innerJoin('d.sqAtributoTipoDocumento', 'atd')
            ->innerJoin('d.sqPessoa', 'p')
            ->innerJoin('atd.sqTipoDocumento', 'tpd')
            ->where('p.sqPessoa = :sqPessoa')
            ->andWhere('tpd.sqTipoDocumento IN (1, 2, 4, 5, 6)')
            ->setParameter('sqPessoa', $sqPessoa)
            ->getQuery()
            ->getResult();

        $criteria = array();
        foreach ($sqTipoDocumento as $value) {
            array_push($criteria, $value['sqTipoDocumento']);
        }

        $result = $this->_em->createQueryBuilder()
            ->select('tp.sqTipoDocumento, tp.noTipoDocumento')
            ->from('app:VwTipoDocumento', 'tp')
            ->andWhere('tp.sqTipoDocumento IN (1, 2, 4, 5, 6)');

        if($criteria) {
            $result->andWhere(
                $this->_em->createQueryBuilder()
                    ->expr()
                    ->notIn('tp.sqTipoDocumento', $criteria)
            );
        }

        $itens = array();
        foreach ($result->getQuery()->getArrayResult() as $item) {
            $itens[$item['sqTipoDocumento']] = $item['noTipoDocumento'];
        }

        return $itens;
    }

    public function getComboSgdoce()
    {
        $result = $this->_em->createQueryBuilder()
            ->select('tpd')
            ->from('app:VwTipoDocumento', 'tpd')
            ->where('tpd.sqTipoDocumento IN (1, 2, 4, 5, 6)');

        $itens = array();
        foreach ($result->getQuery()->getArrayResult() as $item) {
            $itens[$item['sqTipoDocumento']] = $item['noTipoDocumento'];
        }

        return $itens;
    }
}
