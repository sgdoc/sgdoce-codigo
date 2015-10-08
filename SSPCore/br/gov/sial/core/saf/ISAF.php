<?php
/*
 * Copyright 2011 ICMBio
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
namespace br\gov\sial\core\saf;

/**
 * SIAL Application Form (SAF)
 *
 * @package br.gov.sial.core
 * @subpackage saf
 * @author J. Augusto <augustowebd@gmail.com>
 * */
interface ISAF
{
    /**
     * adiciona elemento ao documento
     *
     * o primeiro param define o tipo de elemento que sera adicionado, como por exemplo label, campo input, select
     * div e etc., o segundo param, objeto|array , define todas as propriedades do elemento, o terceiro param define
     * a area de inclusao, existem apenas duas opcoes: head e body.
     *
     * @code
     * <?php
     *     # adiciona um  label a app
     *     $app->add('label', array('for' => 'idElement', 'text' => 'nome do usuario'));
     *
     *     # adiciona um campo
     *     $app->add('input', array('name', 'elementName', 'value' => 'foo'));
     *
     *     # adiciona objeto
     *     $app->add(new Input('field_name'));
     * ?>
     * @endcode
     *
     * @param string $elType
     * @param stdClass $param
     * @param enum[head, body] $place
     * @return ISAF
     * @throws IllegalArgumentException
     * */
    public function add ($elType, $param = NULL, $place = 'head');

    /**
     * Cria componente de menu
     * @param \stdClass $param
     * @return ElementContainerAbstract
     */
    public function menu (\stdClass $param);

    /**
     * cria brandBar
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function brandbar (\stdClass $param);

    /**
     * Cria breadcrumb
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function breadcrumb (\stdClass $param);

    /**
     * cria barra de botoes
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function buttonbar (\stdClass $param);

    /**
     * mostra o conteúdo em colunas [label: value]
     * PS: <b>
     * o terceiro param 'width' determina o tamanho das duas colunas [label, column]
     * sendo a parte inteira do float para definir o tamanho do label e a francionaria
     * para definir o tamanho da coluna.
     * </b>
     *
     * @param stdClass $param
     * */
    public function display (\stdClass $param);

    /**
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function grid (\stdClass $param);

    /**
     * Filtro inteligente<br />
     * <br />
     * este componente é a combinação de uma área destinada à informar os dados que serao usados como filtro uma segunda<br />
     * área composta por uma grid que sera usada para exibir o resultado da pesquisa e um terceiro elemento usado para<br />
     * detalhar os dados listados na grid de resultado.
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function smartFilter (\stdClass $param);

    /**
     * autocomplete
     * configura/cria campo com capacidade de recuperar dados numa fonte remota para preencher-se
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function autoComplete (\stdClass $param);

    /**
     * cria grupo de radio button
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GRadio (\stdClass $param);

    /**
     * cria grupo de check button
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GCheck (\stdClass $param);

    /**
     * cria combo
     *
     * @param stdClass $param
     * @return Select
     * */
    public function combo (\stdClass $param);

    /**
     * cria formulario
     *
     * @param stdClass $param
     * @return Form
     * */
    public function form (\stdClass $param);

    /**
     * cria tela
     *
     * @param stdClass
     * @return Div
     * */
    public function screenForm (\stdClass $param);

    /**
     * cria titulo do documento, usado no cabecalho do documento
     *
     * @param stdClass $param
     * @return Title
     * */
    public function title (\stdClass $param);

    /**
     * cria meta informacao do documento
     *
     * @param stdClass $param
     * @return Meta
     * */
    public function meta (\stdClass $param);

    /**
     * cria definicao de base de link para o documento
     *
     * @param stdClass $param
     * @return Base
     * */
    public function base (\stdClass $param);

    /**
     * cria comentario de codigo
     *
     * @param stdClass $param
     * @return Comment
     * */
    public function comment (\stdClass $param);

    /**
     * progressbar
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function progressbar (\stdClass $param);

    /**
     * cria referencia externo
     *
     * @param stdClass $param
     * @return Link
     * */
    public function link (\stdClass $param);

    /**
     * cria referencia para documento css
     *
     * @param stdClass $param
     * @return Link
     * */
    public function stylesheet (\stdClass $param);

    /**
     * cria referencia para documento javascript
     *
     * @param stdClass $param
     * @return Javascript
     * */
    public function javascript (\stdClass $param);

    /**
     * cria imagem
     *
     * @param stdClass $param
     * @return Img
     * */
    public function img (\stdClass $param);

    /**
     * cria campo para entrada de dados
     *
     * @param stdClass $param
     * @return Input
     * */
    public function input (\stdClass $param);

    /**
     * criar campo com label
     *
     * @param stdClass $param
     * @return ElementAbstract
     * */
    public function inputlabel (\stdClass $param);

    /**
     * cria paragrafo
     *
     * @param stdClass $param
     * @return Paragraph
     * */
    public function paragraph (\stdClass $param);

    /**
     * painel de exibicao de componente
     *
     * @param stdClass $param
     * */
    public function panel (\stdClass $param);

    /**
     * cria textarea
     *
     * @param stdClass $param
     * @return TextArea
     * */
    public function textarea (\stdClass $param);

    /**
     * formulario de login
     * @param stdClass $param
     * @return Form
     * */
    public function login (\stdClass $param);

    /**
     * cria quebra de linha
     *
     * @return Br
     * */
    public function br ();

    /**
     * cria linha horizontal
     *
     * @return HR
     * */
    public function hr ();

    /**
     * Cria alert
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function alert (\stdClass $param);

    /**
     * Cria well
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function well (\stdClass $param);

    /**
     * Cria modal
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function modal (\stdClass $param);

    /**
     * Cria image map
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function imageMap (\stdClass $param);

    /**
     * Cria JSON
     * @param array $param
     * @return string
     */
    public function json (\stdClass $param);

    /**
     * Cria componente de navegação por tabs, pilhas ou listas.
     * @param array $param
     * @return string
     */
    public function navigation (\stdClass $param);

    /**
     * Cria table
     * @param array $param
     * @return string
     */
    public function table ();

    /**
     * Cria tableRow
     * @return TableRow
     */
    public function tableRow ();

    /**
     * Cria tableBody
     * @return TableBody
     */
    public function tableBody ();

    /**
     * Cria tableHead
     * @return TableHead
     */
    public function tableHead ();

    /**
     * Cria tableHeaderCell
     * @return TableHeaderCell
     */
    public function tableHeaderCell (\stdClass $param = NULL);

    /**
     * Cria tableData
     * @return TableData
     */
    public function tableData (\stdClass $param = NULL);

    /**
     * Cria ul
     * @return UL
     */
    public function ul ();

    /**
     * Cria li
     * @return LI
     */
    public function li ();

    /**
     * Cria anchor
     * @return Anchor
     */
    public function anchor (\stdClass $param = NULL);

    /**
     * Cria Text
     * @return Text
     */
    public function text (\stdClass $param = NULL);

    /**
     * Cria Label
     * @return Label
     */
    public function label (\stdClass $param = NULL);

    /**
     * Cria Select
     * @return Select
     */
    public function select (\stdClass $param = NULL);

    /**
     * cria espaçador
     *
     * @return ElementAbstract
     */
    public function spacer (\stdClass $param = NULL);

    /**
     * cria icone
     *
     * @return ElementAbstract
     */
    public function icon (\stdClass $param = NULL);
}