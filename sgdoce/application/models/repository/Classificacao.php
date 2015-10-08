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
use Doctrine\Common\Util\Debug;

/**
 * SISICMBio
 *
 * Classe para Repository de Classificacao de Artefato / Caixa de Arquivo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Classificacao
 * @version      1.0.0
 * @since        2015-01-30
 */
class Classificacao extends \Core_Model_Repository_Base
{

    /**
     * método que pesquisa Classificações para Caixa de arquivo
     * @param array $params
     * @param integer $nuLimit
     * @return array
     */
    public function searchClassificacaoParaCaixa ($params, $nuLimit = 10)
    {
        return $this->_getSqlAutocompleteClassificacao($params, $nuLimit);
    }

    /**
     * método que pesquisa Classificações para Arterfato a ser arquivado
     * @param array $params
     * @param integer $nuLimit
     * @return array
     */
    public function searchClassificacaoParaArtefato ($params, $nuLimit = 10)
    {
        return $this->_getSqlAutocompleteClassificacao($params, $nuLimit, false);
    }

    private function _getSqlAutocompleteClassificacao ($params, $nuLimit, $forBox = true)
    {
        $search       = mb_strtolower($params['query'],'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $txClassificacao = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('c.txClassificacao'));
        $nuClassificacao = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('c.nuClassificacao'));

        $query = $queryBuilder->select('c')
                ->from('app:Classificacao', 'c');

        if ($forBox) {//caixa só pode ser classificada com classificações sem pai
            $query->where( $queryBuilder->expr()
                        ->orX('c.sqClassificacaoPai IS NULL',
                              $queryBuilder->expr()
                                  ->eq('c.sqClassificacaoPai','c.sqClassificacao') ));
        }else{//artefato só pode ser classificado com sub classificações
            $query->where($queryBuilder->expr()
                        ->orX('c.sqClassificacaoPai IS NOT NULL',
                              $queryBuilder->expr()
                                  ->neq('c.sqClassificacaoPai','c.sqClassificacao') ));
        }

        $query->andWhere($queryBuilder->expr()
                            ->like(
                                'clear_accentuation(' . $txClassificacao .')',
                                $queryBuilder->expr()
                                    ->literal($this->removeAccent('%' . $search . '%'))))
                ->orWhere($queryBuilder->expr()
                            ->like(
                                'clear_accentuation(' . $nuClassificacao .')',
                                $queryBuilder->expr()
                                    ->literal($this->removeAccent('%' . $search . '%'))))
                ->setMaxResults($nuLimit)
                ->orderBy('c.txClassificacao');

        $res = $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->getArrayResult();

        $out = array();

        foreach ($res as $item) {
             $out[$item['sqClassificacao']] = $item['nuClassificacao'] . ' - ' . $item['txClassificacao'];
        }

        return $out;
    }
}