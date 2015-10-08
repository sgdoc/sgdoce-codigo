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
 * Classe para Repository de Carimbo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Carimbo
 * @version      1.0.0
 * @since        2012-11-20
 */
class Carimbo extends \Core_Model_Repository_Base
{
    /**
     * Função para pesquisa de carimbo
     * @param  array $params Dados da requisição
     * @return mixed         Query Builder
     */
    public function pesquisaCarimbo ($params)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select(array('c', 'ta'))
                             ->from('app:Carimbo', 'c')
                             ->innerJoin('c.sqTipoArtefato','ta');

        if($params->getNoCarimbo()) {
            $queryBuilder->where('c.noCarimbo like :nome')
                         ->setParameter('nome', '%' . $params->getNoCarimbo() . '%');
        }

        return $queryBuilder;
    }

    /**
     * Retorna carimbos no formato chave=> valor para popular combo
     * @return array Itens para geração de combo
     */
    public function listItems()
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('c.sqCarimbo, c.noCarimbo')
                             ->from('app:Carimbo', 'c')
                             ->orderBy('c.noCarimbo', 'ASC');
        $out = array();
        $res = $queryBuilder->getQuery()->getArrayResult();
        foreach ($res as $item) {
            $out[$item['noCarimbo']] = $item['noCarimbo'];
        }
        return $out;
    }
}