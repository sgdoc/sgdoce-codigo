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
 * Sgdoce\Model\Entity\ComentarioArtefato
 *
 * @ORM\Table(name="comentario_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ComentarioArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ComentarioArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * limite de char por comentario
     *
     *  @var integer
     * */
    const T_TX_COMENTARIO_LIMIT = 2000;

    /**
     * @var integer $sqComentarioArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_comentario_artefato", type="integer", nullable=false)
     * */
    private $sqComentarioArtefato;

    /**
     * @var text $txComentario
     *
     * @ORM\Column(name="tx_comentario", type="text", nullable=false)
     */
    private $txComentario;

    /**
     * @var datetime $dtComentario
     *
     * @ORM\Column(name="dt_comentario", type="zenddate", nullable=false)
     */
    private $dtComentario;

    /**
     * @var Sgdoce\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoa;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato", inversedBy="sqComentarioArtefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")})
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\VwUltimoTramiteArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUltimoTramiteArtefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")})
     */
    private $vwUltimoTramiteArtefato;

    /**
     * Get sqComentarioArtefato
     *
     * @return integer
     */
    public function getSqComentarioArtefato()
    {
        return $this->sqComentarioArtefato;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\PessoaSgdoce
     */
    public function getSqPessoa()
    {
    	return $this->sqPessoa;
    }

    /**
     * Get sqUnidade
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
    	return $this->sqUnidadeOrg;
    }

    /**
     * Get txComentario
     *
     * @return text
     */
    public function getTxComentario()
    {
    	return $this->txComentario;
    }

    /**
     * Get dtComentario
     *
     * @return datetime
     */
    public function getDtComentario()
    {
    	return $this->dtComentario;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato()
    {
    	return $this->sqArtefato;
    }

    /**
     * set sqComentarioArtefato
     *
     * @param integer
     * @return ComentarioArtefato
     */
    public function setSqComentarioArtefato ($sqComentarioArtefato)
    {
    	$this->sqComentarioArtefato = $sqComentarioArtefato;
    	return $this;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return ComentarioArtefato
     */
    public function setSqPessoa (\Sgdoce\Model\Entity\VwPessoa $sqPessoa)
    {
    	$this->sqPessoa = $sqPessoa;
    	return $this;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrg
     * @return ComentarioArtefato
     */
    public function setSqUnidadeOrg (\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrg)
    {
    	$this->sqUnidadeOrg = $sqUnidadeOrg;
    	return $this;
    }

    /**
     * Set txComentario
     *
     * @param text $txComentario
     * @return ComentarioArtefato
     */
    public function setTxComentario ($txComentario)
    {


error_log(sprintf('[%s]%s%s', 'SGDOCE_teste__TX_COMENTARIO', PHP_EOL, var_export(array(
               'mb_internal_encoding' => mb_internal_encoding(),
               'length'               => mb_strlen($txComentario,'UTF-8'),
               'length_replace'       => mb_strlen(str_replace(chr(13), '', $txComentario),'UTF-8'),
               'detect_encoding'      => mb_detect_encoding($txComentario),
           ),true)));
            mb_internal_encoding('UTF-8');
error_log(sprintf('[%s]%s%s', 'SGDOCE_teste__TX_COMENTARIO', PHP_EOL, var_export(array(
               'mb_internal_encoding' => mb_internal_encoding(),
               'length'               => mb_strlen($txComentario),
               'length_replace'       => mb_strlen(str_replace(chr(13), '', $txComentario)),
               'detect_encoding'      => mb_detect_encoding($txComentario),
           ),true)));

//        $this->txComentario = mb_substr(str_replace(chr(13), '', $txComentario), 0, self::T_TX_COMENTARIO_LIMIT, 'UTF-8');
        $this->txComentario = $txComentario;
        return $this;
    }

    /**
     * Set dtComentario
     *
     * @param datetime $dtComentario
     * @return ComentarioArtefato
     */
    public function setDtComentario($dtComentario)
    {
        $this->dtComentario = $dtComentario;
        return $this;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ComentarioArtefato
     */
    public function setSqArtefato (\Sgdoce\Model\Entity\Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * @return Sgdoce\Model\Entity\VwUltimoTramiteArtefato
     * */
    function getVwUltimoTramiteArtefato ()
    {
        return $this->vwUltimoTramiteArtefato;
    }

    /**
     * @param Sgdoce\Model\Entity\VwUltimoTramiteArtefato
     * @return ComentarioArtefato
     * */
    function setVwUltimoTramiteArtefato ($vwUltimoTramiteArtefato)
    {
        $this->vwUltimoTramiteArtefato = $vwUltimoTramiteArtefato;
        return $this;
    }
}