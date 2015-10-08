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
 * Classe para Repository de TipoPrioridade
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoPrioridade
 * @version      1.0.0
 * @since        2012-11-20
 */
class TipoPrioridade extends \Core_Model_Repository_Base
{
    /**
     * Constante para receber o valor zero
     * @var    integer
     * @name   ZER
     */
    const ZER = 0;

    /**
     * método que pesquisa parametros da grid
     * @param string $params
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid ($params)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('tp.sqTipoPrioridade, tp.txTipoPrioridade, p.noPrioridade, p.sqPrioridade')
                             ->from('app:TipoPrioridade', 'tp')
                             ->innerJoin('tp.sqPrioridade', 'p')
                             ->orderBy('p.sqPrioridade', 'ASC');

        if ($params->getTxTipoPrioridade()) {
            $query = mb_strtolower($params->getTxTipoPrioridade(), 'UTF-8');# Consulta case-insensitive
            $queryBuilder->andWhere('LOWER(tp.txTipoPrioridade) like :descricao')
                         ->setParameter('descricao', '%' . $query . '%');
        }

        if ($params->getSqPrioridade()) {
            $queryBuilder->andWhere('tp.sqPrioridade = :idPrioridade')
                         ->setParameter('idPrioridade', $params->getSqPrioridade() );
        }

        return $queryBuilder;
    }

    /**
     * método que pesquisa se tem tipo de prioridade
     * @param string $txTipoPrioridade
     * @param integer $sqTipoPrioridade
     * @param integer $sqPrioridade
     * @return boolean
     */
    public function hasTipoPrioridade ($txTipoPrioridade, $sqTipoPrioridade, $sqPrioridade)
    {
        $query = mb_strtolower($txTipoPrioridade, 'UTF-8');# Consulta case-insensitive
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('tp')
                     ->from('app:TipoPrioridade', 'tp')
                     ->where('LOWER(tp.txTipoPrioridade) = :txTipoPrioridade')
                     ->setParameter('txTipoPrioridade', $query);

        if($sqTipoPrioridade != NULL) {
            $queryBuilder->andwhere('tp.sqTipoPrioridade <> :sqTipoPrioridade')
                         ->setParameter('sqTipoPrioridade', $sqTipoPrioridade);
        }

        if($sqPrioridade != NULL) {
            $queryBuilder->andwhere('tp.sqPrioridade = :sqPrioridade')
                         ->setParameter('sqPrioridade', $sqPrioridade);
        }

        $res = $queryBuilder->getQuery()->execute();
        return (count($res) > self::ZER);
    }

    /**
     * método que remove sequencial
     * @param integer $sequence
     * @return boolean
     */
    public function deActivate ($sequence)
    {
        $message = \Core_Messaging_Manager::getGateway('User');
        $entity = $this->find($sequence);
        try {
            $this->_em->remove($entity);
            $this->_em->flush();
            $message->addSuccessMessage('MN045');
        }
        catch (\PDOException $e) {
            $message->addAlertMessage('MN067');
        }
        $message->dispatchPackets();
        return TRUE;
    }


    public function descricaoPrioridadePorPrioridade ($sqPrioridade, $whithSelect = true)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('tp.sqTipoPrioridade, tp.txTipoPrioridade')
                ->from('app:TipoPrioridade', 'tp')
                ->where('tp.sqPrioridade = :prioridade')
                ->setParameter('prioridade', $sqPrioridade)
                ->orderBy('tp.txTipoPrioridade');

        $result = $queryBuilder->getQuery()
                               ->useResultCache(TRUE, NULL, __METHOD__)
                               ->getResult();

        if ($whithSelect) {
            $out = array('' => 'Selecione uma opção');
        }

        foreach ($result as $item) {
            $out[$item['sqTipoPrioridade']] = $item['txTipoPrioridade'];
        }

        return $out;
    }


    /**
     * Obtém dados do tratamento
     * @param \Core_Dto_Search $search
     * @return array
     */
    public function listTipoPrioridade (\Core_Dto_Search $search = NULL)
    {
        $queryBuilder = $this->_em
        ->createQueryBuilder()
        ->select('tp')
        ->from('app:TipoPrioridade', 'tp')
        ->orderBy('tp.sqTipoPrioridade', 'ASC');

        $out = array('' => 'Selecione');
        $res = $queryBuilder->getQuery()
                            ->useResultCache(TRUE, NULL, __METHOD__)
                            ->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqTipoPrioridade']] = $item['txTipoPrioridade'];
        }

        return $out;
    }
}