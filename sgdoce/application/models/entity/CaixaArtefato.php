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
use Core\Model\OWM\Mapping as OWM;
/**
 * Sgdoce\Model\Entity\CaixaArtefato
 *
 * @ORM\Table(name="caixa_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\CaixaArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class CaixaArtefato extends \Core_Model_Entity_Abstract {

    /**
     * @var Sgdoce\Model\Entity\Caixa
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Caixa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa", referencedColumnName="sq_caixa")})
     */
    private $sqCaixa;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")})
     */
    private $sqArtefato;

    /**
     *
     * @return Caixa
     */
    public function getSqCaixa ()
    {
        return $this->sqCaixa;
    }

    /**
     *
     * @return Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    public function setSqCaixa ($sqCaixa)
    {
        $this->sqCaixa = $sqCaixa;
        return $this;
    }

    public function setSqArtefato ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }





}