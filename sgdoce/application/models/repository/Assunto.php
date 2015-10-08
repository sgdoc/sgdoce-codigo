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
 * Classe para Repository de Assunto
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Assunto
 * @version      1.0.0
 * @since        2012-11-20
 */
class Assunto extends \Core_Model_Repository_Base
{
    /**
     * Efetua a busca do assunto
     * @param array $params
     * @return array
     */
    public function pesquisaAssunto ($dto, $limit=10)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select('a.sqAssunto, a.txAssunto')
            ->from('app:Assunto', 'a')
            ->andWhere('a.stHomologado = TRUE'); //somente assuntos homologados podem ser apresentados

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('a.txAssunto'));

        //Consulta case-insensitive
        $query->andWhere(
            $queryBuilder->expr()
                         ->like('clear_accentuation(' . $field .')', $queryBuilder->expr()->literal($this->removeAccent('%' . $search . '%')))
        );

        $query->setMaxResults($limit);

        $res = $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->getArrayResult();

        $out = array();
        foreach ($res as $item) {
            $out[$item['sqAssunto']] = $item['txAssunto'];
        }

        return $out;
    }

    public function comboAssunto()
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                                  ->select('a.sqAssunto, a.txAssunto')
                                  ->from('app:Assunto', 'a')
                                  ->where('a.stHomologado = TRUE')
                                  ->orderBy('a.txAssunto', 'ASC');

        $out = array();
        $res = $queryBuilder->getQuery()
                            ->useResultCache(TRUE, NULL, __METHOD__)
                            ->getArrayResult();

        foreach ($res as $item) {
            $out[$item['sqAssunto']] = $item['txAssunto'];
        }
        return $out;
    }
}