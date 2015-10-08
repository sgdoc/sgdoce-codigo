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
 * SISICMBio
 *
 * Classe para Entity PessoaInteressadaArtefato
 *
 * @package      Model
 * @subpackage     Entity
 * @name         PessoaInteressadaArtefato
 * @version     1.0.0
 * @since        2013-02-07
 */

/**
 * Sgdoce\Model\Entity\PessoaInteressadaArtefato
 *
 * @ORM\Table(name="pessoa_interessada_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaInteressadaArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaInteressadaArtefato extends \Core_Model_Entity_Abstract
{

    /**
     * @var bigint $sqArtefato
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", inversedBy="sqPessoaInteressadaArtefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce")
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * Set sqPessoaSgdoce
     *
     * @param object $sqPessoaSgdoce
     * @return PessoaInteressadaArtefato
     */
    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
        return $this;
    }

    /**
     * Get sqPessoaSgdoce
     *
     * @return PessoaSgdoce
     */
    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce;
    }

    /**
     * Get sqArtefato
     *
     * @return Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato ? $this->sqArtefato : new Artefato();
    }

    /**
     * Set sqArtefato
     *
     * @param object $sqArtefato
     * @return PessoaInteressadaArtefato
     */
    public function setSqArtefato(Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }
}