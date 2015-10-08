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
*     classe: Sgdoce\Model\Repository\vwDocumentoSgdocFisico
*     tabela: artefato_imagem
*
* @ORM\Table(name="vw_documento_sgdoc_fisico")
* @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\vwDocumentoSgdocFisico")
*/
class vwDocumentoSgdocFisico extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string $tipo
     * @ORM\Column(name="tipo", type="string", nullable=false)
     */
    private $tipo;

    /**
     * @var string $numero
     * @ORM\Column(name="numero", type="string", nullable=false)
     */
    private $numero;

    /**
     * @var string $origem
     * @ORM\Column(name="origem", type="string", nullable=false)
     */
    private $origem;

    /**
     * @var string $destino
     * @ORM\Column(name="destino", type="string", nullable=false)
     */
    private $destino;

    /**
     * @var string $interessado
     * @ORM\Column(name="interessado", type="string", nullable=false)
     */
    private $interessado;

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
     * @var string $cargo
     * @ORM\Column(name="cargo", type="string", nullable=false)
     */
    private $cargo;

    /**
     * @var string $assinatura
     * @ORM\Column(name="assinatura", type="string", nullable=false)
     */
    private $assinatura;

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

    /**
     * @var string $prioridade
     * @ORM\Column(name="prioridade", type="string", nullable=false)
     */
    private $prioridade;

    /**
     * @var string $autor
     * @ORM\Column(name="autor", type="string", nullable=false)
     */
    private $autor;
    
    function getId() {
        return $this->id;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getNumero() {
        return $this->numero;
    }

    function getOrigem() {
        return $this->origem;
    }

    function getDestino() {
        return $this->destino;
    }

    function getInteressado() {
        return $this->interessado;
    }

    function getAssunto() {
        return $this->assunto;
    }

    function getAssuntoComplementar() {
        return $this->assuntoComplementar;
    }

    function getCargo() {
        return $this->cargo;
    }

    function getAssinatura() {
        return $this->assinatura;
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

    function getPrioridade() {
        return $this->prioridade;
    }

    function getAutor() {
        return $this->autor;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
        return $this;
    }

    function setNumero($numero) {
        $this->numero = $numero;
        return $this;
    }

    function setOrigem($origem) {
        $this->origem = $origem;
        return $this;
    }

    function setDestino($destino) {
        $this->destino = $destino;
        return $this;
    }

    function setInteressado($interessado) {
        $this->interessado = $interessado;
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

    function setCargo($cargo) {
        $this->cargo = $cargo;
        return $this;
    }

    function setAssinatura($assinatura) {
        $this->assinatura = $assinatura;
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

    function setPrioridade($prioridade) {
        $this->prioridade = $prioridade;
        return $this;
    }

    function setAutor($autor) {
        $this->autor = $autor;
        return $this;
    }



}