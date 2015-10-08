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
 * Classe para Repository Pessoa Fisica
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwPessoaFisica
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwPessoaFisica extends \Core_Model_Repository_Base
{

    /**
     * Váriavel PessoaFisica
     * @var string
     * @name app:VwPessoaFisica
     * @access private
     */
    private $_enName = 'app:VwPessoaFisica';

    public function searchPessoaFisica($dto, $retornaCpf = TRUE, $limit = 10)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('pf.noPessoaFisica'));

        $query = $queryBuilder->select(
                'p.sqPessoa',
                'pf.noPessoaFisica',
                'pf.nuCpf'
            )
            ->from($this->_entityName, 'pf')
            ->innerJoin('pf.sqPessoaFisica', 'p')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('pf.noPessoaFisica')
            ->setMaxResults($limit);

        $res = $query->getQuery()->getArrayResult();
        $out = array();

        if ($retornaCpf) {
            foreach ($res as $item) {
                $nuCpf = null;

                if($item['nuCpf']) {
                    $nuCpf  = \Zend_Filter::filterStatic($item['nuCpf'], 'MaskNumber', array('cpf'), array('Core_Filter'));
                    $nuCpf .= ' - ';
                }

                $out[$item['sqPessoa']] =  $nuCpf . $item['noPessoaFisica'];
            }
        } else {
            foreach ($res as $item) {

                $out[$item['sqPessoa']] = $item['noPessoaFisica'];
            }

        }

        return $out;
    }
}
