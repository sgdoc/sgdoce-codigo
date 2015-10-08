<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

namespace Sica\Model\Repository;

use Bisna\Application\Resource\Doctrine,
    Principal\Service\Pessoa as srvPessoa;

/**
 * SISICMBio
 *
 * Classe para Repository de Pessoa
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Pessoa
 * @version	 1.0.0
 */
class Pessoa extends \Sica_Model_Repository
{

    /**
     * Realiza a pesquisa da grid
     * @param \Core_Dto_Abstract $dto
     */
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        switch ($dto->getSqTipoPessoa()) {
            case srvPessoa::SQ_TIPO_PESSOA_FISICA:
                $query = $this->_em
                        ->createQueryBuilder()
                        ->select('p.sqPessoa, p.noPessoa, p.stRegistroAtivo, '
                                . 'pf.nuCpf, pf.dtNascimento, pf.sgSexo, tp.sqTipoPessoa, m.sqMunicipio')
                        ->from('app:Pessoa', 'p')
                        ->innerJoin('p.sqPessoaFisica', 'pf')
                        ->innerJoin('pf.sqTipoPessoa', 'tp')
                        ->leftJoin('pf.sqMunicipio', 'm');
                break;

            case srvPessoa::SQ_TIPO_PESSOA_JURIDICA:
                $query = $this->_em
                        ->createQueryBuilder()
                        ->select('p.sqPessoa, p.noPessoa, p.stRegistroAtivo, pj.nuCnpj, pj.noFantasia, tp.sqTipoPessoa')
                        ->from('app:Pessoa', 'p')
                        ->innerJoin('p.sqPessoaJuridica', 'pj')
                        ->innerJoin('p.sqTipoPessoa', 'tp')
                        ->leftJoin('p.sqNaturezaJuridica', 'nj');
                break;

            default:
                $query = $this->_em
                        ->createQueryBuilder()
                        ->select('p.sqPessoa, p.noPessoa, tp.noTipoPessoa, p.stRegistroAtivo, pf.nuCpf, '
                                . 'pf.dtNascimento, pf.sgSexo, tp.sqTipoPessoa, pj.nuCnpj, pj.noFantasia, '
                                . 'm.sqMunicipio')
                        ->from('app:Pessoa', 'p')
                        ->leftJoin('p.sqPessoaJuridica', 'pj')
                        ->leftJoin('p.sqPessoaFisica', 'pf')
                        ->leftJoin('p.sqTipoPessoa', 'tp')
                        ->leftJoin('pf.sqMunicipio', 'm');
                break;
        }

        $this->addWhere($query, $dto);

        return $query;
    }

    /**
     * Adiciona parametros para realizar o filtro da grid
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param \Core_Dto_Abstract $dto
     */
    public function addWhere(\Doctrine\ORM\QueryBuilder $query, \Core_Dto_Abstract $dto)
    {
        $inputs = array(
            'tp.sqTipoPessoa' => $dto->getSqTipoPessoa(),
            'pf.nuCpf' => $dto->getNuCpf(),
            'pj.nuCnpj' => $dto->getNuCnpj(),
            'p.stRegistroAtivo' => $dto->getStRegistroAtivo()
        );

        switch ($dto->getSqTipoPessoa()) {
            case srvPessoa::SQ_TIPO_PESSOA_FISICA:
                $inputs['p.noPessoa'] = $dto->getNoPessoaFisica();

                break;

            case srvPessoa::SQ_TIPO_PESSOA_JURIDICA:
                $inputs['p.noPessoa'] = $dto->getNoPessoaJuridica();
                $inputs['nj.sqNaturezaJuridicaPai'] = $dto->getSqNaturezaJuridicaPai();
                $inputs['nj.sqNaturezaJuridica'] = $dto->getSqNaturezaJuridica();

                break;

            default:
                $inputs['p.noPessoa'] = $dto->getNoPessoaSemClass();
                break;
        }

        $countParams = 0;

        foreach ($inputs as $field => $value) {
            if ($value != '') {
                if (strstr($field, 'sq') || strstr($field, 'nu') || strstr($field, 'st')) {
                    if ($field == 'tp.sqTipoPessoa' && $value == srvPessoa::SEM_CLASSIFICACAO) {
                        $query->andWhere($this->_em->createQueryBuilder()->expr()->in('tp.sqTipoPessoa', ':expre'))
                                ->setParameter('expre', array(
                                    srvPessoa::SQ_TIPO_PESSOA_FISICA, srvPessoa::SQ_TIPO_PESSOA_JURIDICA
                                ));
                    } else {
                        $query->andWhere($field . ' = ?' . $countParams)
                                ->setParameter($countParams, \Zend_Filter::filterStatic($value, 'Digits'));
                    }
                } else {
                    $expre = $query->expr()->lower($query->expr()->trim($field));
                    $value = "%" . mb_strtolower(trim($value), 'UTF-8') . "%";

                    $query->andWhere($query->expr()->like('clear_accentuation(' . $expre . ')'
                                    , $query->expr()->literal($this->translate($value))));

                    if ($dto->getSqTipoPessoa() == srvPessoa::SEM_CLASSIFICACAO ||
                            $dto->getSqTipoPessoa() == srvPessoa::SQ_TIPO_PESSOA_JURIDICA) {

                        $expre = $query->expr()->lower($query->expr()->trim('pj.noFantasia'));

                        $query->orWhere($query->expr()->like('clear_accentuation(' . $expre . ')'
                                        , $query->expr()->literal($this->translate($value))));
                    }
                }

                $countParams++;
            }
        }
    }

    /**

     * @param array $params
     * @return array
     */
    public function searchPessoa(\Core_Dto_Search $dto)
    {
        $result = $this->listGrid($dto)->setMaxResults(10)->getQuery()->getArrayResult();

        $itens = array();
        foreach ($result as $item) {
            if ($item['sqTipoPessoa'] == \Core_Configuration::getCorpTipoPessoaFisica()) {
                $doc = \Zend_Filter::filterStatic($item['nuCpf'], 'MaskNumber', array('cpf'), array('Core_Filter'));
            } else {
                $doc = \Zend_Filter::filterStatic($item['nuCnpj'], 'MaskNumber', array('cnpj'), array('Core_Filter'));
            }

            $nome = $doc ? $doc . ' - ' . $item['noPessoa'] : $item['noPessoa'];
            $itens[$item['sqPessoa']] = $nome;
        }

        return $itens;
    }

    /**
     * Realiza consulta por cpf e nome
     * @param type $dto
     */
    public function searchCpf($dto)
    {
        $query = $this->listGrid($dto)->setMaxResults(10);

        if ($dto->getCpf()) {
            $query->andWhere($query->expr()->like('pf.nuCpf', '?1'))
                    ->setParameter(1, '%' . $dto->getCpf() . '%');
        }

        $itens = array();
        foreach ($query->getQuery()->getResult() as $item) {
            if ($item['sqTipoPessoa'] == \Core_Configuration::getCorpTipoPessoaFisica()) {
                $doc = \Zend_Filter::filterStatic($item['nuCpf'], 'MaskNumber', array('cpf'), array('Core_Filter'));
            } else {
                $doc = \Zend_Filter::filterStatic($item['nuCnpj'], 'MaskNumber', array('cnpj'), array('Core_Filter'));
            }

            $nome = $doc ? $doc . ' - ' . $item['noPessoa'] : $item['noPessoa'];
            $itens[$item['sqPessoa']] = $nome;
        }

        return $itens;
    }

}

