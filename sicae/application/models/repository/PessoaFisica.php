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

use Bisna\Application\Resource\Doctrine;

/**
 * SISICMBio
 *
 * Classe para Repository de Pessoa Fisica
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 PessoaFisica
 * @version	 1.0.0
 */
class PessoaFisica extends \Sica_Model_Repository
{

    public function findDataInstitucional($codigo)
    {
        if (NULL === $codigo) {
            return array(
                'sqPessoa' => NULL,
                'noPessoa' => NULL,
                'nuCpf' => NULL,
                'nuDdd' => NULL,
                'nuTelefone' => NULL,
                'txEmail' => NULL
            );
        }

        $query = $this->_em->createQueryBuilder();

        $tipoTelefoneInstituicional = \Core_Configuration::getCorpTipoTelefoneInstitucional();
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();

        $query->select('p.sqPessoa', 'p.noPessoa', 'pf.nuCpf', 't.nuDdd, t.nuTelefone', 'e.txEmail')
                ->from('app:PessoaFisica', 'pf')
                ->innerJoin('pf.sqPessoa', 'p')
                ->leftJoin('p.telefone', 't', 'WITH', $query->expr()->eq('t.sqTipoTelefone', ':tipoTelefone'))
                ->leftJoin('p.email', 'e', 'WITH', $query->expr()->eq('e.sqTipoEmail', ':tipoEmail'))
                ->where($query->expr()->eq('p.sqPessoa', ':sqPessoa'))
                ->andWhere($query->expr()->eq('pf.sqPessoa', ':sqPessoa'))
                ->setParameter('tipoTelefone', $tipoTelefoneInstituicional, 'integer')
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer')
                ->setParameter('sqPessoa', $codigo, 'integer');

        $data = $query->getQuery()->execute(array(), \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if (!$data) {
            return array(
                'sqPessoa' => NULL,
                'noPessoa' => NULL,
                'nuCpf' => NULL,
                'nuDdd' => NULL,
                'nuTelefone' => NULL,
                'txEmail' => NULL
            );
        }

        return $data[0];
    }

    public function findResponsible($nome)
    {
        $query = $this->_em->createQueryBuilder();

        $query->select('pf.nuCpf, p.sqPessoa, p.noPessoa')
                ->from($this->_entityName, 'pf')
                ->innerJoin('pf.sqPessoa', 'p')
                ->andWhere($query->expr()->eq('p.stRegistroAtivo', ':registroAtivo'))
                ->setParameter('registroAtivo', 'TRUE')
                ->setMaxResults(10)
                ->orderBy('p.noPessoa', 'ASC');

        if ($nome) {
            $expre = $query->expr()->lower($query->expr()->trim('p.noPessoa'));
            $value = "%" . mb_strtolower(trim($nome), 'UTF-8') . "%";

            $query->andWhere('(' . $query->expr()->like($expre, ':pessoa') . ' OR ' .
                            $query->expr()->like($expre, ':orPessoa') . ' OR ' .
                            $query->expr()->like('clear_accentuation(' . $expre . ')'
                                    , $query->expr()->literal($this->translate($value))) . ')')
                    ->setParameter('pessoa', $value)
                    ->setParameter('orPessoa', $this->translate($value));
        }

        $result = $query->getQuery()->execute();
        $out = array();

        foreach ($result as $res) {
            $doc = \Zend_Filter::filterStatic($res['nuCpf'], 'MaskNumber', array('cpf'), array('Core_Filter'));
            $nome = $doc ? $doc . ' - ' . $res['noPessoa'] : $res['noPessoa'];
            $out[$res['sqPessoa']] = $nome;
        }

        return $out;
    }

}
