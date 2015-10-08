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
 * Classe para Repository de Caixa Historico
 *
 * @package      Model
 * @subpackage   Repository
 * @name         CaixaHistorico
 * @version      1.0.0
 * @since        2015-02-09
 */
class CaixaHistorico extends \Core_Model_Repository_Base
{

    public function getLastHistorico($sqArtefato)
    {
        $sql = 'SELECT ch.sq_tipo_historico_arquivo
                  FROM sgdoce.caixa_historico ch
                  JOIN ( SELECT sq_artefato,
                                MAX(dt_operacao) dt_operacao
                           FROM sgdoce.caixa_historico
                          GROUP BY 1
                        ) ch_max ON (ch.sq_artefato, ch.dt_operacao) = (ch_max.sq_artefato, ch_max.dt_operacao)
                  WHERE ch.sq_artefato = :sqArtefato';

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_tipo_historico_arquivo',    'sqTipoHistoricoArquivo',   'integer');

        $nq = $this->_em->createNativeQuery($sql, $rsm);
        $nq->setParameter('sqArtefato', $sqArtefato);

        $result = $nq->getOneOrNullResult();
        return ($result)? $result['sqTipoHistoricoArquivo'] : NULL;
    }
}