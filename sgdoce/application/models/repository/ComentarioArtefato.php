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
 * Classe para Repository de ComentarioArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ComentarioArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class ComentarioArtefato extends \Core_Model_Repository_Base
{
    /**
     * Realiza busca para grid
     *
     * @param \Core_Dto_Search $search
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGrid ($search)
    {
        try {

            $rsm = new \Doctrine\ORM\Query\ResultSetMapping();

            $rsm->addScalarResult('total_record'          , 'totalRecord'         , 'integer');
            $rsm->addScalarResult('sq_comentario_artefato', 'sqComentarioArtefato', 'integer');
            $rsm->addScalarResult('sq_pessoa'             , 'sqPessoa'            , 'integer');
            $rsm->addScalarResult('sq_artefato'           , 'sqArtefato'          , 'integer');
            $rsm->addScalarResult('sq_unidade_org'        , 'sqUnidadeOrg'        , 'integer');
            $rsm->addScalarResult('no_pessoa'             , 'noPessoa'            , 'string');
            $rsm->addScalarResult('tx_comentario'         , 'txComentario'        , 'string');
            $rsm->addScalarResult('no_unidade_org'        , 'noUnidadeOrg'        , 'string');
            $rsm->addScalarResult('dt_comentario'         , 'dtComentario'        , 'zenddate');
            $rsm->addScalarResult('dt_tramite'            , 'dtTramite'           , 'zenddate');

            $sql = "SELECT COUNT(sq_comentario_artefato) OVER() as total_record,
                           ca.sq_comentario_artefato,
                           pes.sq_pessoa,
                           pes.no_pessoa,
                           art.sq_artefato,
                           ca.tx_comentario,
                           ca.dt_comentario,
                           uo.sq_pessoa as sq_unidade_org,
                           uo.no_pessoa as no_unidade_org,
                           uta.dt_tramite
                      FROM comentario_artefato ca
                      JOIN vw_pessoa pes
                        ON ca.sq_pessoa = pes.sq_pessoa
                      JOIN artefato art
                        ON ca.sq_artefato = art.sq_artefato
                      JOIN vw_unidade_org uo
                        ON ca.sq_unidade = uo.sq_pessoa
                      JOIN tramite_artefato uta
                        ON uta.sq_artefato = ca.sq_artefato AND uta.st_ultimo_tramite
                    WHERE ca.sq_artefato = :sqArtefato
                    ORDER BY ca.dt_comentario DESC";

            $query = $this->_em->createNativeQuery($sql, $rsm);
            $query->setParameter('sqArtefato', $search->getSqArtefato());

            return $query;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function save (\Sgdoce\Model\Entity\ComentarioArtefato $entity)
    {
        $data = array(
            'sq_artefato'   => $entity->getSqArtefato()->getSqArtefato(),
            'sq_pessoa'     => $entity->getSqPessoa()->getSqPessoa(),
            'dt_comentario' => $entity->getDtComentario()->toString('yyyy-MM-dd HH:mm:ss'),
            'tx_Comentario' => $entity->getTxComentario(),
            'sq_unidade' => $entity->getSqUnidadeOrg()->getSqUnidadeOrg()
        );

        $connection = $this->_em->getConnection();
        $connection->insert('comentario_artefato', $data);
    }

    public function update (\Sgdoce\Model\Entity\ComentarioArtefato $entity)
    {
        $data = array(
            $entity->getSqArtefato()->getSqArtefato(),
            $entity->getSqPessoa()->getSqPessoa(),
            $entity->getDtComentario()->toString('yyyy-MM-dd HH:mm:ss'),
            $entity->getTxComentario(),
            $entity->getSqUnidadeOrg()->getSqUnidadeOrg(),
            $entity->getSqComentarioArtefato(),
        );

        $types = array(
            \PDO::PARAM_INT,
            \PDO::PARAM_INT,
            \PDO::PARAM_INT,
            \PDO::PARAM_STR,
            \PDO::PARAM_STR,
            \PDO::PARAM_STR,
        );

        $connection = $this->_em->getConnection();
        $connection->executeUpdate('
            UPDATE comentario_artefato
               SET sq_artefato = ?,
                   sq_pessoa = ?,
                   dt_comentario = ?,
                   tx_Comentario = ?,
                   sq_unidade = ?
             WHERE sq_comentario_artefato = ?'
            , $data
            , $types
        );
    }
}