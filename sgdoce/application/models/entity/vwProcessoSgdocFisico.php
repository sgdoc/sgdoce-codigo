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
* Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
* */
namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Sgdoce\Model\Entity\VwUltimaImagemArtefato
* Representação da ultima imagem do artefato:
*     classe: Sgdoce\Model\Repository\vwProcessoSgdocFisico
*     tabela: artefato_imagem
*
* @ORM\Table(name="vw_processo_sgdoc_fisico")
* @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\vwProcessoSgdocFisico")
*/
class vwProcessoSgdocFisico extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string $numeroProcesso
     * @ORM\Column(name="numero_processo", type="string", nullable=false)
     */
    private $numeroProcesso;

    /**
     * @var string $assunto
     * @ORM\Column(name="assunto", type="string", nullable=false)
     */
    private $assunto;

    /**
     * @var string $assuntoComplementar
     * @ORM\Column(name="assunto_complementar", type="string", nullable=false)
     */
    private $assuntoComplementar;

    /**
     * @var string $origem
     * @ORM\Column(name="origem", type="string", nullable=false)
     */
    private $origem;

    /**
     * @var string $interessado
     * @ORM\Column(name="interessado", type="string", nullable=false)
     */
    private $interessado;

    /**
     * @var string $cpfCnpj
     * @ORM\Column(name="cpf_cnpj", type="string", nullable=false)
     */
    private $cpfCnpj;

    /**
     * @var string $procedencia
     * @ORM\Column(name="procedencia", type="string", nullable=false)
     */
    private $procedencia;
    
    /**
     * @var string $digital
     * @ORM\Column(name="digital", type="string", nullable=false)
     */
    private $digital;

    /**
     * @var string $dtCadastro
     * @ORM\Column(name="dt_cadastro", type="string", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var string $dtPrazo
     * @ORM\Column(name="dt_prazo", type="string", nullable=false)
     */
    private $dtPrazo;

    /**
     * @var string $ultimoTramite
     * @ORM\Column(name="ultimo_tramite", type="string", nullable=false)
     */
    private $ultimoTramite;
    
    function getId() {
        return $this->id;
    }

    function getNumeroProcesso() {
        return $this->numeroProcesso;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getAssuntoComplementar() {
        return $this->assuntoComplementar;
    }

    function getOrigem() {
        return $this->origem;
    }

    function getInteressado() {
        return $this->interessado;
    }

    function getCpfCnpj() {
        return $this->cpfCnpj;
    }

    function getProcedencia() {
        return $this->procedencia;
    }

    function getDigital() {
        return $this->digital;
    }

    function getDtCadastro() {
        return $this->dtCadastro;
    }

    function getDtPrazo() {
        return $this->dtPrazo;
    }

    function getUltimoTramite() {
        return $this->ultimoTramite;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setNumeroProcesso($numeroProcesso) {
        $this->numeroProcesso = $numeroProcesso;
        return $this;
    }

    function setAssunto($assunto) {
        $this->assunto = $assunto;
        return $this;
    }

    function setAssuntoComplementar($assuntoComplementar) {
        $this->assuntoComplementar = $assuntoComplementar;
        return $this;
    }

    function setOrigem($origem) {
        $this->origem = $origem;
        return $this;
    }

    function setInteressado($interessado) {
        $this->interessado = $interessado;
        return $this;
    }

    function setCpfCnpj($cpfCnpj) {
        $this->cpfCnpj = $cpfCnpj;
        return $this;
    }

    function setProcedencia($procedencia) {
        $this->procedencia = $procedencia;
        return $this;
    }

    function setDigital($digital) {
        $this->digital = $digital;
        return $this;
    }

    function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    function setDtPrazo($dtPrazo) {
        $this->dtPrazo = $dtPrazo;
        return $this;
    }

    function setUltimoTramite($ultimoTramite) {
        $this->ultimoTramite = $ultimoTramite;
        return $this;
    }



}