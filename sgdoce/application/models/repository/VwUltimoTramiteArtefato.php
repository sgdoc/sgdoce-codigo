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
 * Classe para Repository de TramiteArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwUltimoTramiteArtefato
 * @version      1.0.0
 * @since        2014-12-18
 */
class VwUltimoTramiteArtefato extends \Core_Model_Repository_Base
{

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function inMyDashboard(\Core_Dto_Search $dto)
    {
        $entityTramite = $this->find($dto->getSqArtefato());

        if ($entityTramite && !$entityTramite->getSqPessoaRecebimento()) {
            return false;
        }

        $isSqPessoa = ($entityTramite && $entityTramite->getSqPessoaRecebimento()
                         && $entityTramite->getSqPessoaRecebimento()->getSqPessoa() == $dto->getSqPessoa());

        $isSqPessoaDestino = ($entityTramite && $entityTramite->getSqPessoaDestino()
                                && $entityTramite->getSqPessoaDestino()->getSqPessoa() == $dto->getSqPessoaDestino());

        $isCanceladoOuDevolvido = ($entityTramite && in_array($entityTramite->getSqStatusTramite()->getSqStatusTramite(), array(
            \Core_Configuration::getSgdoceStatusTramiteCancelado(),
            \Core_Configuration::getSgdoceStatusTramiteDevolvido(),
        )));

        if( $entityTramite &&
            ( !$isSqPessoa || (!$isSqPessoaDestino && !$isCanceladoOuDevolvido)) )
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function isFirstTramite(\Core_Dto_Search $dto)
    {
        $entityTramite = $this->find($dto->getSqArtefato());

        if( $entityTramite->getNuTramite() == 2
            && $entityTramite->getSqStatusTramite()->getSqStatusTramite() == \Core_Configuration::getSgdoceStatusTramiteCancelado() ) {
            return true;
        }

        if ($entityTramite->getNuTramite() > 1) {
            return false;
        }

        return true;
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function isTramiteExterno (\Core_Dto_Search $dto)
    {

        $sql = "SELECT (uo.sq_pessoa is null) AS is_tramite_externo
                  FROM sgdoce.vw_ultimo_tramite_artefato uta
                  JOIN corporativo.vw_pessoa pes
                    ON pes.sq_pessoa = uta.sq_pessoa_destino
             LEFT JOIN corporativo.vw_unidade_org uo
                    ON pes.sq_pessoa = uo.sq_pessoa
                 WHERE sq_artefato = :sqArtefato";

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addScalarResult('is_tramite_externo', 'isTramiteExterno', 'boolean');

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query->setParameter('sqArtefato', $dto->getSqArtefato());

        return $query->getSingleScalarResult();

    }

}
