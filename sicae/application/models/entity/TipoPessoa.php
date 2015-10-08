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

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SISICMBio
 *
 * Classe para entity Tipo Pessoa
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Tipo Pessoa
 * @version     1.0.0
 * @since        2012-08-26
 */

/**
 * Sica\Model\Entity\TipoPessoa
 *
 * @ORM\Table(name="vw_tipo_pessoa")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoPessoa", readOnly=true)
 */
class TipoPessoa extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoPessoa
     *
     * @ORM\Column(name="sq_tipo_pessoa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoPessoa;

    /**
     * @var string $noTipoPessoa
     *
     * @ORM\Column(name="no_tipo_pessoa", type="string", length=30, nullable=false)
     */
    private $noTipoPessoa;

    /**
     * Set sqTipoPessoa
     *
     * @return integer 
     */
    public function setSqTipoPessoa($sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
        return $this;
    }

    /**
     * Get sqTipoPessoa
     *
     * @return integer 
     */
    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa;
    }

    /**
     * Set noTipoPessoa
     *
     * @param string $noTipoPessoa
     * @return TipoPessoa
     */
    public function setNoTipoPessoa($noTipoPessoa)
    {
        $this->noTipoPessoa = $noTipoPessoa;
        return $this;
    }

    /**
     * Get noTipoPessoa
     *
     * @return string 
     */
    public function getNoTipoPessoa()
    {
        return $this->noTipoPessoa;
    }

}