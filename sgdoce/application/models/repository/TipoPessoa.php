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
 * Classe para Repository de TipoPessoa
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoPessoa
 * @version      1.0.0
 * @since        2012-11-20
 */
class TipoPessoa extends \Core_Model_Repository_Base
{
    /**
     * Variável que recebe o nome da entidade
     * @access protected
     * @var string
     * @name $enName
     */
    protected $enName = 'app:VwTipoPessoa';

    /**
     * Obtém tipo pessoa
     * @return array
     */
    public function getTipoPessoa()
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('e')
              ->from($this->enName, 'e')
              ->orderBy('e.sqTipoPessoa', 'asc');

        return $query->getQuery()
                     ->useResultCache(TRUE, NULL, __METHOD__)
                     ->getResult();
    }

    /**
     * Obtém dados para combo tipo pessoa
     * @return array
     */
    public function comboTipoPessoa()
    {
        $data = $this->getTipoPessoa();
        $out = array('' => 'Selecione...');
        foreach ($data as $item) {
            $out[$item->getSqTipoPessoa()] = $item->getNoTipoPessoa();
        }
        return $out;
    }
}
