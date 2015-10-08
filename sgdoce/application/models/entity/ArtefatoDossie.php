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

use Doctrine\DBAL\Types\BigIntType;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\ArtefatoDossie
 *
 * @ORM\Table(name="artefato_dossie")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoDossie")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoDossie extends \Core_Model_Entity_Abstract
{

    /**
     * @var bigint $sqArtefato
     *
     * @ORM\Id
	 * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", inversedBy="sqArtefatoDossie")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var string $noTitulo
     * @ORM\Column(name="no_titulo", type="string", length=250, nullable=false)
     */
    private $noTitulo;

    /**
     * @var text $txObservacao
     * @ORM\Column(name="tx_observacao", type="text", nullable=true)
     */
    private $txObservacao;


    /**
     * Set sqArtefato
     *
     * @param bigint $sqArtefato
     * @return bigint
     */
    public function setSqArtefato($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    public function getNoTitulo()
    {
        return $this->noTitulo;
    }

    public function setNoTitulo($noTitulo)
    {
        $this->noTitulo = $noTitulo;
    }

    public function getTxObservacao()
    {
        return $this->txObservacao;
    }

    public function setTxObservacao($txObservacao)
    {
        $this->txObservacao = $txObservacao;
    }
}
