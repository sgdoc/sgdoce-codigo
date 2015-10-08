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
 * Classe para Repository Pessoa
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwPessoaExtensao
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwPessoaExtensao extends \Core_Model_Repository_Base
{
    /**
     * Váriavel Pessoa
     * @var string
     * @name app:VwPessoa
     * @access private
     */
    private $_enName = 'app:VwPessoa';

    /**
     * método que busca pessoa pelo Id
     * @param array $params
     * @return Query
     */
    public function buscarPessoaPorId($idUserLogon)
    {
        return $this->_em->getReference($this->_enName, $idUserLogon);
    }

    protected function _subDocumento()
    {
        return $this->_em->createQueryBuilder()
                ->select('MAX(vatd.sqAtributoTipoDocumento)')
                ->from('app:VwAtributoTipoDocumento', 'vatd')
                ->andWhere('vatd.sqTipoDocumento = :sqTipoDocumento')
                ->setParameter('sqTipoDocumento', \Core_Configuration::getSgdoceCorpTipoDocumentoPassaporte())
                ->andWhere('vatd.sqAtributoDocumento = :sqAtributoDocumento')
                ->setParameter('sqAtributoDocumento', \Core_Configuration::getCorpAtributoDocumentoNumero())
                ->getQuery()->getSingleScalarResult();
    }

    public function getDocumento()
    {
        $resulSub = $this->_subDocumento();
        $subSql = $this->_em->createQueryBuilder();
        $subSql->select('vwd')
               ->from('app:VwDocumento', 'vwd')
               ->where('vwd.sqAtributoTipoDocumento = :sqAtributoTipoDocumento')
               ->setParameter('sqAtributoTipoDocumento', $resulSub);
        $xgh = $subSql->getQuery()->execute();
        return array('sqAtributoTipoDocumento' => $xgh[0]->getSqAtributoTipoDocumento()->getSqAtributoTipoDocumento());
    }

    /**
     * método que adiciona a clausula where
     * @param query
     * @return query
     */
    public function addWhere(\Doctrine\ORM\QueryBuilder &$query, \Core_Dto_Search $search, $nuCpfCnpjPassaporte)
    {
        $sqPessoa = $search->getSqPessoaCorporativo() ? : $search->getSqPessoa();

        if($sqPessoa) {
            $query->andWhere('p.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $sqPessoa);
        }

        switch ($search->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $passaporte = $this->getDocumento();
                $query->leftJoin('p.sqPessoaFisica', 'pf');
                //passar cpf ou passaporte , passa parametro
                if ($nuCpfCnpjPassaporte != '') {
                    if ($search->getSqNacionalidade() == '1') {
                        $query->andWhere('pf.nuCpf = :nuCpf')
                            ->setParameter('nuCpf', $nuCpfCnpjPassaporte);
                    } else {
                        $query->leftJoin("p.sqPessoaDocumento", "d", "WITH",
                                         "d.sqAtributoTipoDocumento = :sqAtributoTipoDocumento")
                            ->setParameter("sqAtributoTipoDocumento", $passaporte['sqAtributoTipoDocumento'])
                            ->andWhere('pf.sqNacionalidade <> :sqNacionalidade')
                            ->setParameter('sqNacionalidade', '1')
                            ->andWhere('d.txValor = :txValor')
                            ->setParameter('txValor', $nuCpfCnpjPassaporte);
                    }
                }
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica():
                $query->leftJoin('p.sqPessoaJuridica', 'pj');
                if ($nuCpfCnpjPassaporte != '') {
                    $query->andWhere('pj.nuCnpj = :nuCnpj')
                        ->setParameter('nuCnpj', $nuCpfCnpjPassaporte);
                }
                break;
            case \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico():
                $query->leftJoin('p.sqUnidadeOrgInterna', 'ui');
                $query->leftJoin('p.sqRppn', 'rpn');
                break;
            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos():
                $query->leftJoin('p.sqUnidadeOrgExterna', 'ue');
                break;
        }
    }

    /**
     * método que retorna os dados da pessoa rodape
     * @param dto Search
     * @return array
     */
    public function getPessoaDadosRodape(\Core_Dto_Search $search)
    {
        $nuCpfCnpjPassaporte = '';
        $query = $this->_em->createQueryBuilder();
        switch ($search->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                $query->select('p,pf,d');
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $query->select('p,pj');
                break;
            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro() :
                $query->select('p,e');
                break;
            case \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico() :
            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos() :
                $query->select('p');
                break;
            default :
                $query->select('p,pf,pj,e');
        }

        $query->from($this->_enName, 'p');

        $this->addWhere($query, $search, $nuCpfCnpjPassaporte);

        return $query->getQuery()->execute();
    }
}