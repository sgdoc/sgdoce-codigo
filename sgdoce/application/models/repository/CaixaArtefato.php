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
 * Classe para Repository de Caixa Artefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         CaixaArtefato
 * @version      1.0.0
 * @since        2015-01-30
 */
class CaixaArtefato extends \Core_Model_Repository_Base
{
    /**
     *
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function listGrid(\Core_Dto_Search $dto)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_caixa',          'sqCaixa',         'integer');
        $rsm->addScalarResult('sq_artefato',       'sqArtefato',      'integer');
        $rsm->addScalarResult('sq_tipo_artefato',  'sqTipoArtefato',  'integer');
        $rsm->addScalarResult('nu_artefato',       'nuArtefato',      'string');
        $rsm->addScalarResult('no_tipo_documento', 'noTipoDocumento', 'string');
        $rsm->addScalarResult('tx_classificacao',  'txClassificacao', 'string');
        $rsm->addScalarResult('nu_classificacao',  'nuClassificacao', 'string');
        $rsm->addScalarResult('no_pessoa_origem',  'noPessoaOrigem',  'string');
        $rsm->addScalarResult('tx_assunto',        'txAssunto',       'string');

        $strQuery = sprintf(
                "SELECT ca.sq_caixa
                        ,art.sq_artefato
                        ,CASE WHEN (taa.sq_tipo_artefato = %d) THEN art.nu_artefato ELSE art.nu_digital::TEXT END AS nu_artefato
                        ,td.no_tipo_documento
                        ,taa.sq_tipo_artefato
                        ,cl.nu_classificacao || ' - ' ||cl.tx_classificacao as tx_classificacao

                        ,ps.no_pessoa AS no_pessoa_origem
                        ,ass.tx_assunto
                   FROM caixa_artefato ca
             INNER JOIN caixa cx                    using(sq_caixa)
             INNER JOIN artefato art                 using(sq_artefato)
             INNER JOIN tipo_documento td            using(sq_tipo_documento)
             INNER JOIN tipo_artefato_assunto taa    using(sq_tipo_artefato_assunto)
             INNER JOIN assunto ass                  using(sq_assunto)
             INNER JOIN artefato_classificacao ac    using(sq_artefato)
             INNER JOIN classificacao cl             ON ac.sq_classificacao = cl.sq_classificacao
             INNER JOIN pessoa_artefato pa           using(sq_artefato)
             INNER JOIN pessoa_sgdoce ps             using(sq_pessoa_sgdoce)
                  WHERE ca.sq_caixa = %d
                    AND pa.sq_pessoa_funcao = %d"

                ,\Core_Configuration::getSgdoceTipoArtefatoProcesso()
                ,$dto->getSqCaixa()
                ,\Core_Configuration::getSgdocePessoaFuncaoOrigem()
            );

        $nativeQuery = $this->_em
             ->createNativeQuery($strQuery, $rsm);

        return $nativeQuery;

    }
}