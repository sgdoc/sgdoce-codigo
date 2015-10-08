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
 * Classe para Repository de ArtefatoProcesso
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ArtefatoProcesso
 * @version      1.0.0
 * @since        2012-11-20
 */
class ArtefatoProcesso extends \Core_Model_Repository_Base
{
    /**
     * @param array $arrParams
     */
    public function setFromArray( $arrParams )
    {
    	$arrOptionsDto = array(
			'entity' => 'Sgdoce\Model\Entity\ArtefatoProcesso',
			'mapping' => array(
				'sqEstado'    => 'Sgdoce\Model\Entity\VwEstado',
				'sqMunicipio' => 'Sgdoce\Model\Entity\VwMunicipio',
				'sqArtefato'  => 'Sgdoce\Model\Entity\Artefato'
			)
		);

    	return \Core_Dto::factoryFromData($arrParams, 'entity', $arrOptionsDto);
    }

    /**
     * Consulta de processos.
     *
     * @return QueryBuilder
     */
    public function listGridProcesso( $dto )
    {
        $listCondition = array(
            'getInteressado' => array(
                "ilike" => array(
                    "OR" => array(
                        "trim(pse.no_pessoa)",
                        "trim(vue.sg_unidade_org || ' - ' || pse.no_pessoa)",
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getNuArtefato' => array(
                "regex" => array(
                    "AND" => array(
                        'art.nu_artefato'
                    ),
                    'tlp'=>array(
                        '%s',
                    )
                )
            ),
            'getSqAssunto' => array(
                "=" => array(
                    "AND" => 'ass.sq_assunto'
                )
            ),
            'getTxAssuntoComplementar' => array(
                "ilike" => array(
                    "AND" => 'art.tx_assunto_complementar'
                )
            ),
            'getOrigem' => array(
                "ilike" => array(
                    'OR' => array(
                        'trim(pfo.no_pessoa)',
                        "trim(pfo.no_pessoa)"
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getNuDigital' => array(
                "ilike" => array(
                    "AND" => 'cast(ard.nu_digital as text)'
                )
            ),
        );

        $listPeriodo = array(
            'dtCadastro' => 'art.dt_cadastro',
            'dtAutuacao' => 'art.dt_artefato',
            'dtPrazo'    => 'art.dt_prazo'
        );
        $sqPeriodo = $dto->getSqPeriodo();
        $periodoColumn = null;

        if( isset($listPeriodo[$sqPeriodo]) ){
            $periodoColumn = $listPeriodo[$sqPeriodo];
        }

        $where = $this->getEntityManager()
                      ->getRepository('app:Artefato')
                      ->getCriteriaText($listCondition, $dto, $periodoColumn);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total_record'    , 'totalRecord'         , 'integer');
        $rsm->addScalarResult('sq_artefato'     , 'sqArtefato'          , 'integer');
        $rsm->addScalarResult('nu_artefato'     , 'nuArtefato'          , 'string');
        $rsm->addScalarResult('tx_assunto'      , 'txAssunto'           , 'string');
        $rsm->addScalarResult('origem'          , 'noPessoaOrigem'      , 'string');
        $rsm->addScalarResult('interessados'    , 'noPessoaInteressados', 'string');
        $rsm->addScalarResult('tx_movimentacao' , 'txMovimentacao'      , 'string');

        $sql = "SELECT
                       COUNT(art.sq_artefato) OVER() AS total_record,
                       art.sq_artefato,
                       formata_numero_artefato(art.nu_artefato, atp.co_ambito_processo) AS nu_artefato,
                       ass.sq_assunto,
                       ass.tx_assunto,
                       art.tx_assunto_complementar,
                       string_agg(pse.no_pessoa, ', ') as interessados,
                       pfo.sq_pessoa_corporativo as origem_id,
                       pfo.no_pessoa as origem,
                       art.dt_artefato,
                       art.dt_prazo,
                       art.dt_cadastro,
                       ard.nu_digital,
                       sgdoce.ultima_movimentacao_artefato(art.sq_artefato) as tx_movimentacao,
                       vuo.sg_unidade_org
                  FROM sgdoce.artefato art
                  JOIN sgdoce.artefato_processo atp
                    ON art.sq_artefato = atp.sq_artefato
                  JOIN sgdoce.tipo_artefato_assunto taa
                    ON art.sq_tipo_artefato_assunto = taa.sq_tipo_artefato_assunto
                  JOIN sgdoce.assunto ass
                    ON taa.sq_assunto = ass.sq_assunto
                  JOIN sgdoce.pessoa_artefato pao
                    ON art.sq_artefato = pao.sq_artefato AND pao.sq_pessoa_funcao = " . \Core_Configuration::getSgdocePessoaFuncaoOrigem() . "
                  JOIN sgdoce.pessoa_sgdoce pfo
                    ON pao.sq_pessoa_sgdoce = pfo.sq_pessoa_sgdoce
             LEFT JOIN corporativo.vw_unidade_org vuo
                    ON pfo.sq_pessoa_corporativo = vuo.sq_pessoa
             LEFT JOIN sgdoce.pessoa_interessada_artefato pai
                    ON art.sq_artefato = pai.sq_artefato
             LEFT JOIN sgdoce.pessoa_sgdoce pse
                    ON pai.sq_pessoa_sgdoce = pse.sq_pessoa_sgdoce
             LEFT JOIN corporativo.vw_unidade_org vue
                    ON pse.sq_pessoa_corporativo = vue.sq_pessoa
             LEFT JOIN sgdoce.artefato_vinculo arv
                    ON art.sq_artefato = arv.sq_artefato_pai AND arv.sq_tipo_vinculo_artefato = " . \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao() . "
             LEFT JOIN sgdoce.artefato ard
                    ON ard.sq_artefato = arv.sq_artefato_filho
                %s
              GROUP BY art.sq_artefato,
                       art.nu_artefato,
                       atp.co_ambito_processo,
                       ass.sq_assunto,
                       ass.tx_assunto,
                       art.tx_assunto_complementar,
                       pfo.sq_pessoa_corporativo,
                       pfo.no_pessoa,
                       art.dt_artefato,
                       art.dt_prazo,
                       art.dt_cadastro,
                       ard.nu_digital,
                       tx_movimentacao,
                       vuo.sg_unidade_org";


        if( $where != "" ) {
            $where = "WHERE " . $where;
        } else {
            $where = "WHERE 1 <> 1";
        }

        $sql = sprintf($sql, $where);
        
        return $this->_em->createNativeQuery($sql, $rsm);
    }
}