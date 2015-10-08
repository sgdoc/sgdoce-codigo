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

namespace Sgdoce\Model\Entity;



use Doctrine\ORM\Mapping as ORM;

/**
 * Atribuicao
 *
 * @ORM\Table(name="atribuicao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Atribuicao")
 */
 class VwAtribuicao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqAtribuicao
     *
     * @ORM\Column(name="sq_atribuicao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAtribuicao;

    /**
     * @var string $noAtribuicao
     *
     * @ORM\Column(name="no_atribuicao", type="string", length=50, nullable=false)
     */
    private $noAtribuicao;


    /**
     * Get sqAtribuicao
     *
     * @return integer
     */
    public function getSqAtribuicao()
    {
        return $this->sqAtribuicao;
    }

    /**
     * Set noAtribuicao
     *
     * @param string $noAtribuicao
     * @return Atribuicao
     */
    public function setNoAtribuicao($noAtribuicao)
    {
        $this->noAtribuicao = $noAtribuicao;
        return $this;
    }

    /**
     * Get noAtribuicao
     *
     * @return string
     */
    public function getNoAtribuicao()
    {
        return $this->noAtribuicao;
    }
}