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
 * Esfera
 *
 * @ORM\Table(name="esfera")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Esfera")
 */
 class VwEsfera extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqEsfera
     *
     * @ORM\Column(name="sq_esfera", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEsfera;

    /**
     * @var text $noEsfera
     *
     * @ORM\Column(name="no_esfera", type="text", nullable=false)
     */
    private $noEsfera;


    /**
     * Get sqEsfera
     *
     * @return integer
     */
    public function getSqEsfera()
    {
        return $this->sqEsfera;
    }

    /**
     * Set noEsfera
     *
     * @param text $noEsfera
     * @return Esfera
     */
    public function setNoEsfera($noEsfera)
    {
        $this->noEsfera = $noEsfera;
        return $this;
    }

    /**
     * Get noEsfera
     *
     * @return text
     */
    public function getNoEsfera()
    {
        return $this->noEsfera;
    }
}