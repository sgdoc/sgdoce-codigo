<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\saf\style\html;
use br\gov\sial\core\saf\DecoratorAbstract,
    br\gov\sial\core\output\screen\html\H2,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\html\Span,
    br\gov\sial\core\output\screen\html\Label,
    br\gov\sial\core\output\screen\html\Legend,
    br\gov\sial\core\output\screen\html\Anchor,
    br\gov\sial\core\output\screen\html\Strong,
    br\gov\sial\core\output\screen\html\Fieldset,
    br\gov\sial\core\output\screen\ElementAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage saf
 * @author J. Augusto <augustowebd@gmail.com>
 * @todo criar medo para definir propriedades default, caso as mesmas nao tenham sido definidas
 */
class Custom extends DecoratorAbstract
{
    /**
     * @var string
     * */
    const T_CUSTOM_VMENU_DEFAULT_ID = 'nestedAccordion';

    /**
     * Cria um elemento e retorna sua instância
     * @param type $element
     * @param type $param
     * @return \br\gov\sial\core\output\screen\ElementAbstract
     * */
    public function create ($element, \stdClass $param = NULL)
    {
        $param = (object) $param;

        if (is_string($element) && method_exists($this, $element)) {
            return $this->$element($param);
        }

        return $this->_component->create($element, $param);
    }

    /**
     * @inheritdoc
     *
     * A soma dos algarismos de width deve totalizar em 12, exemplo, 3.9, 2.10, etc.
     *
     * @return Div
     *
     * @code
     * <?php
     *     $param = new stdClass;
     *     $param->label = 'Label';
     *     $param->value = 'content';
     *     $param->width = 3.9
     *     $isaf->display($param);
     * ?>
     * @endcode
     * */
    public function display (\stdClass $param)
    {
        return $this->_component->display($param);
    }

    /**
     * <ul>
     *   <li>Menu Vertical - São esperados as seguintes propriedades:</li>
     *   <li>id      - define o id do container que armazenara o menu</li>
     *   <li>title   - titulo do menu</li>
     *   <li>options - opcoes do menu</li>
     * </ul>
     *
     * @code
     * <?php
     *   $options[0] = array('href' => 'href_#1.1', 'text' => 'OPCAO #1.1');
     *   $options[0] = array('href' => 'href_#1.2', 'text' => 'OPCAO #1.2');
     *
     *   Nota: A mudança de índice reflete em um separador entre itens do menu
     *
     *   $options[1] = array('href' => 'href_#2.1', 'text' => 'OPCAO #2.1');
     *   $options[1] = array('href' => 'href_#2.2', 'text' => 'OPCAO #2.2');
     *
     *   $options[2] = array('href' => 'href_#3.1', 'text' => 'OPCAO #3.1');
     *   $options[2] = array('href' => 'href_#3.1', 'text' => 'OPCAO #3.1');
     *
     *   $param = new stdClass;
     *   $param->id      = 'identifier';
     *   $param->title   = 'Menu Name';
     *   $param->options = $options;
     *   $param->type    = 'h'
     *
     *   $safObject->menu((object) $param);
     * ?>
     * @endcode
     *
     * @param \stdClass $param
     * @return ElementContainerAbstract
     */
    public function menu (\stdClass $param)
    {
        return $this->_component->menu($param);
    }

    /**
     * Cria o componente Brandbar
     *
     * @code
     * <?php
     *   $options[] = array('href' => 'href_#1.1', 'text' => 'OPCAO #1.1');
     *   $options[] = array('href' => 'href_#1.2', 'text' => 'OPCAO #1.2');
     *   $options[] = array('href' => 'href_#3.1', 'text' => 'OPCAO #3.1');
     *   ...
     *   $options[] = array('href' => 'href_#N.m', 'text' => 'OPCAO #N.M');
     *
     *   $safObject->VMenu((object) $param);
     * ?>
     * @endcode
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function brandbar (\stdClass $param)
    {
        return $this->_component->brandbar($param);
    }

    /**
     * Breadcrumb
     * @param stdClass $param
     * @return ElementContainerAbstract
     */
    public function breadcrumb (\stdClass $param)
    {
        return $this->_component->breadcrumb($param);
    }

    /**
     * Cria a barra de botões. Segue abaixo a lista de opções disponíveis:
     *
     * <ul>
     *  <li>first</li>
     *  <li>prev</li>
     *  <li>next</li>
     *  <li>last</li>
     *  <li>save</li>
     *  <li>edit</li>
     *  <li>abort</li>
     *  <li>cancel</li>
     *  <li>delete</li>
     *  <li>complete</li>
     *  <li>print</li>
     *  <li>submit</li>
     *  <li>search</li>
     *  <li>forgot</li>
     *  <li>less</li>
     *  <li>plus</li>
     * </ul>
     *
     * @code
     * <?php
     *      $app->add('buttonbar', (object) array('options' => array('first', 'prev', 'next', 'last')));
     * @encode
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function buttonbar (\stdClass $param)
    {
        return $this->_component->buttonbar($param);
    }

    /**
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function grid (\stdClass $param)
    {
        return $this->_component->grid($param);
    }

    /**
     * @inheritdoc
     *
     *
     * @code
     * <?php
     *  # ... (.'some code before'.) ...
     *
     *  # cria objeto de configuração do component
     *  $config =  new stdClass;
     *
     *  # define o local onde encontrar o smartFilter.js do pluging o caminho
     *  # informado em $config->cdn será concatenado com 'component/js/smartFilter.js'
     *  $config->cdn = 'http://dev.static.cdn.icmbio.gov.br/';
     *
     *  # configura o filtro
     *  $config->comboOptions = array(
     *      array('value' => 'valueOfOption_1',  'text' => 'labelOfOption_1'),
     *      array('value' => 'valueOfOption_2',  'text' => 'labelOfOption_2'),
     *      array('value' => 'valueOfOption_N',  'text' => 'labelOfOption_N'),
     *  )
     *
     *  # configura o nome da propriedade que sera usada como 'value' do filter
     *  # o valor tem de casar com uma das propriedas do subarray de comboOptions
     *  $config->comboValue  = 'value';
     *
     *  # configura o nome da propriedade que sera usada como 'label' do filter
     *  # o valor tem de casar com uma das propriedas do subarray de comboOptions
     *  $config->comboText  = 'text';
     *
     *  # configura o local de recuperacao de dados, por padrao este componente
     *  # sempre buscará dados em alguma fonte externa, assim, por hora, ele não
     *  # suporta que seja informado um array diretamente como valor
     *  $config->httpRequest = new stdClass;
     *
     *  # define a url da fonte de dados
     *  $config->httpRequest->url = '/system/module/action/';
     *
     *  # define o tipo de requisicao que sera usada para recuperar os dados
     *  $config->httpRequest->requestType = 'POST';
     *
     *  # define que a conexao para busca dos dados não utilizará cache
     *  $config->httpRequest->useCache = FALSE;
     *
     *  # define o tipo de dados esperado
     *  $config->httpRequest->dataType = 'json';
     *
     *  # define configuracao da grid que exibirá o resultado da pesquisa
     *  $config->grid = new stdClass;
     *
     *  # define as colunas da grid
     *  $config->grid->columns[] = array(
     *      'label'  => 'column Label',
     *      'dindex' => 'columnIndex' ,
     *      'sorter' => TRUE
     *  );
     *
     *  # define a coluna que tera seu valor usado como chave primaria nas
     *  # ações realizadas sobre os dados. Esta propriedade devera apontar
     *  # para $config->grid->columns['dindex'] de uma das colunas da grid
     *  $config->grid->rowKey = 'columnIndex';
     *
     *  # define os eventos, ações, sobre os dados exibidos na grid
     *  # inicialmente apenas 3 eventos sao suportados: detailt, edit e remove
     *  $config->grid->event[] = array(
     *      'on'     => 'detail',
     *      'title'  => 'Detalhando dados',
     *      'url'    => '/system/module/action/'
     *  );
     *
     *  $config->grid->event[] = array(
     *      'on'     => 'edit',
     *      'title'  => 'Alterando dados',
     *      'url'    => '/system/module/action/'
     *  )
     *
     *  $config->grid->event[] = array(
     *      'on'     => 'remove',
     *      'title'  => 'Removendo dados',
     *      'url'    => '/system/module/action/'
     *  )
     *
     *  # adicionado o objeto ao documento
     *  $custonObjectRefer->add('smartyFilter', $config);
     *
     *  # ... (some code after) ...
     *
     * ?>
     * @endcode
     *
     * Todos os params deste metodo devem ser passados num único objeto, os params básicos são:
     * <p><i>string</i>       <b>cdn</b>                        *se informado*, define o local onde estarão armazenados os javascripts usados pelo componente.</p>
     * <p><i>stdClass[]</i>   <b>comboOptions</b>               Conjunto de valores que serão usados para popular o combo de filtro, vide code acima.</p>
     * <p><i>string</i>       <b>comboText</b>                  Determina qual das opções em <b>comboData</b> será utilizada como label no combo</p>
     * <p><i>string</i>       <b>comboValue</b>                 Determina qual das opções em <b>comboData</b> será utilizada como value no combo</p>
     * <p><i>stdClass</i>     <b>httpRequest</b>                Configura a conexão de obtenção dos dados </p>
     * <p><i>string</i>       <b>httpRequest::url</b>           Determina a url de recuperação dos dados</p>
     * <p><i>string</i>       <b>httpRequest::requestType</b>   Determina o tipo da requisição, que pode variar entre todos os tipo suportados por uma conexão <b>HttpRequest</b></p>
     * <p><i>boolean</i>      <b>httpRequest::useCache</b>      Determina se conexão poderá utilizar recurso de cache</p>
     * <p><i>string</i>       <b>httpRequest::dataType</b>      Define o tipo de dados esperado (text, html, json, xml, etc)</p>
     * <p><i>stdClass</i>     <b>grid</b>                       Configura grid usada pelo este componente</p>
     * <p><i>string[]</i>     <b>grid::columns</b>              Determina as colunas da grid</p>
     * <p><i>string</i>       <b>grid::rowKey</b>               Determina qual das colunas configuradas em <b>grid::columns</b> será usada como chave primária</p>
     * <p><i>string[]</i>     <b>event</b>                      Determina qual(is) evento(s) estar(á|ão) disponíve(l|is). O evento habilitado implicará na aparição de seu botão de ação correspondente na coluna de ação da grid</p>
     * <p><i>string</i>       <b>event::on</b>                  Determina quando a ação será disparada on(<i>detail</i>, <i>edit</i>, <i>remove</i>)</p>
     * <p><i>string</i>       <b>event::title</b>               Determina o título que será usado na identificação da modal exibida na execução da evento</p>
     * <p><i>string</i>       <b>event::url</b>                 Determina o local que será carregado ao executar o evento</p>
     * <p><i>string</i>       <b>event::requestType</b>         Determina o tipo de conexão utilizada para carregar <b>event::url</b></p>
     *
     * @param stdClass $param{
     *    string cdn,
     *    string[] comboOptions,
     *    string comboValue,
     *    string comboText,
     *    string[] httpRequest
     *    string[] grid
     * }
     * @return Div
     * */
    public function smartFilter (\stdClass $param)
    {
        $param = (object) $param;
        $cdn   = $this->safeToggle($param, 'cdn');

        # adiciona javascript especifico do menu no documento
        $param->src = $cdn . 'component/js/SAFSmartFilter.js';
        $this->add('javascript', $param);

        # adiciona css especifico do menu no documentp
        $this->add('stylesheet', (object) array(
            'href'  => $cdn . 'component/css/SAFSmartFilter.css',
            'media' => 'screen',
            'rel'   => 'stylesheet',
            'type'  =>' text/css'),
            'head');

        return $this->_component->smartFilter($param);
    }

    /**
     * cria autocomplete
     *
     * @param stdClass $param
     * @return Div
     * */
    public function autoComplete (\stdClass $param)
    {
        $element = $this->_component->autoComplete($param);

        if (isset($param->label)) {

            $controlGroup = Div::factory()->addClass('control-group');

            $label = new Label($param->label);
            $label->addClass('control-label');

            if (isset($param->required)) {

                $element->addClass('required');

                $span = Span::factory()->addClass('required');
                $span->setContent('*' . '&nbsp;');

                # label->span
                $label->add($span);
            }

            # div.control-group -> label
            $controlGroup->add($label);
            $element = $controlGroup->add(Div::factory()->addClass('controls')->add($element));
        }

        return $element;
    }

    /**
     * cria grupo de radio button
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GRadio (\stdClass $param)
    {
        $GRadio = $this->_component->GRadio($param);
        $this->radioAndChecGroupProperty($GRadio, $param);
        return $GRadio;
    }

    /**
     * cria grupo de checkbox
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function GCheck (\stdClass $param)
    {
        $GCheck = $this->_component->GCheck($param);
        $this->radioAndChecGroupProperty($GCheck, $param);
        return $GCheck;
    }

    /**
     * cria combo
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function combo (\stdClass $param)
    {
         $element = $this->_component->combo($param);

        if (isset($param->label)) {

            $controlGroup = Div::factory()->addClass('control-group');

            $label = new Label($param->label);
            $label->addClass('control-label');

            if (isset($param->required)) {

                $element->addClass('required');

                $span = Span::factory()->addClass('required');
                $span->setContent('*' . '&nbsp;');
                $label->add($span);
            }

            $controlGroup->add($label);
            $element = $controlGroup->add($element);
        }

        return $element;
    }

    /**
     * cria formulario
     *
     * @param stdClass $param
     * @return Form
     * */
    public function form (\stdClass $param)
    {
        return $this->_component->form($param);
    }

    /**
     * cria tela
     *
     * @param stdClass
     * @return Div
     * */
    public function screenForm (\stdClass $param)
    {
        return $this->_component->screenForm($param);
    }

    /**
     * @param stdClass $param
     * @return Title
     * */
    public function title (\stdClass $param)
    {
        return $this->_component->title($param);
    }

    /**
     * @param stdClass $param
     * @return Meta
     * */
    public function meta (\stdClass $param)
    {
        return $this->_component->meta($param);
    }

    /**
     * cria definicao de base de link para o documento
     *
     * @param stdClass $param
     * @return Base
     * */
    public function base (\stdClass $param)
    {
        return $this->_component->base($param);
    }

    /**
     * @param stdClass $config
     * @return Comment
     * */
    public function comment (\stdClass $param)
    {
        return $this->_component->comment($param);
    }

    /**
     * cria referencia externo
     *
     * @param stdClass $param
     * @return Link
     * */
    public function link (\stdClass $param)
    {
        return $this->_component->link($param);
    }

    /**
     * cria referencia para documento css
     *
     * @param stdClass $param
     * @return Link
     * */
    public function stylesheet (\stdClass $param)
    {
        return $this->_component->stylesheet($param);
    }

    /**
     * cria referencia para documento javascript
     *
     * @param stdClass $param
     * @return Javascript
     * */
    public function javascript (\stdClass $param)
    {
        return $this->_component->javascript($param);
    }

    /**
     * cria quebra de linha
     *
     * @return Br
     * */
    public function br ()
    {
        return $this->_component->br();
    }

    /**
     * @return Div
     * */
    public function div ()
    {
        return $this->_component->div();
    }

    /**
     * cria linha horizontal
     *
     * @return HR
     * */
    public function hr ()
    {
        return $this->_component->hr();
    }

    /**
     * cria imagem
     *
     * @param stdClass $param
     * @return Img
     * */
    public function img (\stdClass $param)
    {
        return $this->_component->img($param);
    }

    /**
     * painel de exibicao de componente
     *
     * @param stdClass $param
     * @return Fieldset
     * */
    public function panel (\stdClass $param)
    {
        return $this->_component->panel($param);
    }

    /**
     * cria paragrafo
     *
     * @param stdClass $param
     * @return Paragraph
     * */
    public function paragraph (\stdClass $param)
    {
        return $this->_component->paragraph($param);
    }

    /**
     * @param stdClass $param
     * @return Input
     * */
    public function input (\stdClass $param)
    {
        $element = $this->_component->input($param);

        if (isset($param->label)) {

            $controlGroup = Div::factory()->addClass('control-group');

            $label = new Label($param->label);
            $label->addClass('control-label');

            if (isset($param->required)) {

                $element->addClass('required');

                $span = Span::factory()->addClass('required');
                $span->setContent('*' . '&nbsp;');

                # label->span
                $label->add($span);
            }

            # div.control-group -> label
            $controlGroup->add($label);
            $element = $controlGroup->add(Div::factory()->addClass('controls')->add($element));
        }

        return $element;
    }

    /**
     * criar campo com label
     *
     * @param stdClass $param
     * @return Div
     * @example SAFHTML::inputLabel
     * @code
     * <?php
     *   # cria param
     *   $param = new stdClass;
     *   $param->label    = 'titulo do campo'  ; // tipo do labela
     *   $param->required = TRUE               ; // define se campo sera requerido
     *   $param->name     = 'nome_input'       ; // nome do campo
     *   $param->type     = 'text'             ; // tipo do input que sera criado
     *   $param->value    = 'init value'       ; // valor inicial do campo
     *
     *   $safhtmlObject->inputlabel($param);
     * ?>
     * @endcode
     * */
    public function inputlabel (\stdClass $param)
    {
        return $this->_component->inputLabel($param);
    }

    /**
     * cria textarea
     *
     * @param stdClass $param
     * @return TextArea
     * */
    public function textarea (\stdClass $param)
    {
        $element = $this->_component->textarea($param);

        if (isset($param->label)) {

            $controlGroup = Div::factory()->addClass('control-group');

            $label = new Label($param->label);
            $label->addClass('control-label');

            if (isset($param->required)) {

                $element->addClass('required');

                $span = Span::factory()->addClass('required');
                $span->setContent('*' . '&nbsp;');

                # label->span
                $label->add($span);
            }

            # div.control-group -> label
            $controlGroup->add($label);
            $element = $controlGroup->add(Div::factory()->addClass('controls')->add($element));
        }

        return $element;
    }

    /**
     * progressbar
     *
     * @param stdClass $param
     * @return ElementContainerAbstract
     * */
    public function progressbar (\stdClass $param)
    {
        return $this->_component->progressbar($param);
    }

    /**
     * @param Fieldset $group
     * @param stdClass $property
     * */
    public function radioAndChecGroupProperty (Fieldset $group, \stdClass $property)
    {
        if (isset($property->id)) {
            $group->attr('id', $property->id);
        }

        if (isset($param->title)) {
            $group->add(new Legend($property->title));
        }
    }

    /**
     * Alertas em tela
     * http://twitter.github.com/bootstrap/components.html#alerts
     *
     * @param stdClass $param
     * @return Alert
     * */
    public function alert (\stdClass $param)
    {
        return $this->_component->alert($param);
    }

    /**
     * Modal
     *
     * @code
     * <?php
     *   # Modal
     *   $modalBody = new \stdClass();
     *   $modalBody->content = 'Conteúdo do modal';
     *
     *   # A criação de um conteúdo para o Footer é opcional
     *   # Nesse exemplo há a criação de um novo botão.
     *   $footerButton = new \stdClass();
     *   $footerButton->label = 'Salvar';
     *   $footerButton->name = 'btnSalvar';
     *
     *   $modalFooter = new \stdClass();
     *   $modalFooter->content = $saf->create('button', $footerButton);
     *
     *   $modalConf = new \stdClass();
     *   $modalConf->id = 'safModal';
     *   $modalConf->title = 'Título do modal';
     *   $modalConf->body = $saf->create('paragraph', $modalBody);
     *   $modalConf->footer = $modalFooter;
     *
     *   # NOTA: O botão para acionar o modal deverá ser criado a parte
     *   # e deverá utilizar o mesmo 'id' do modal.
     *   $modalButtonConf = new \stdClass();
     *   $modalButtonConf->label = 'abrir';
     *   $modalButtonConf->name = 'btnAbrir';
     *
     *   $modalButton = $safObject->create('button', $modalButtonConf);
     *   $modalButton->attr('data-toggle', 'modal')
     *               ->attr('href', '#' . $modalConf->id);
     *
     *   $safObject->modal($modalConf);
     *   $safObject->add($modalButton);
     * @encode
     *
     * @param stdclass $param
     * @return Modal
     */
    public function modal (\stdClass $param)
    {
        return $this->_component->modal($param);
    }

    /**
     * @param stdClass $param
     * @return Well
     */
    public function well (\stdClass $param)
    {
        return $this->_component->well($param);
    }

    /**
     * formulario de login
     * @param stdClass $param
     * @return Div
     * */
    public function login (\stdClass $param)
    {
        # o param fluidLenght pode aparecer de duas formas
        $lenghts   = explode('.', $this->safeToggle($param, 'fluidLenght', 0.0));
        $container = Div::factory()->addClass('row-fluid');
        $spacer    = Div::factory()->addClass('span' . current($lenghts));
        $content   = Div::factory()->addClass('span' . end($lenghts))
                                   ->add($this->_component->login($param));

        $container->add($spacer)
                  ->add($content);

        return $container;
    }

    /**
     * ImageMap
     *
     * @code
     * <?php
     *      $param = new \stdClass();
     *      $param->img = '/media/image/Yin_yang.png';
     *      $param->usemap = 'mapa';
     *      $param->area = array(
     *          array(
     *              'shape' => 'rect',
     *              'coords' => '0,0,195,106',
     *              'href' => 'foo'
     *          ),
     *          array(
     *              'shape' => 'rect',
     *              'coords' => '0,107,195,195',
     *              'href' => 'bar'
     *          ),
     *      );
     *
     *   $safObject->imageMap((object) $param);
     * ?>
     * @endcode
     *
     * @param stdclass $param
     * @return ImageMap
     */
    public function imageMap (\stdClass $param)
    {
        return $this->_component->imageMap($param);
    }

    /**
     * Json
     * @code
     * <?php
     *    # Json
     *    $param = new \stdClass();
     *    $param->data = array('1' => 'foo', '2' => 'bar');
     *
     *    $safObject->json((object) $param);
     * ?>
     * @encode
     * @param stdclass $param
     * @return json
     */
    public function json (\stdClass $param)
    {
        return $this->_component->json($param);
    }

    /**
     * Navigation
     * @code
     * <?php
     *    # Navigation do tipo Tab sem menu Dropdown
     *    $param = new \stdClass();
     *
     *    # Tipo de navigation (tabs | pills | lists)
     *    $param->type = 'tabs';
     *
     *    # Item de menu ativo
     *    $param->active = 'Home';
     *
     *    # Itens de menu
     *    $param->item = array(
     *        array('Home', '#'),
     *        array('Icmbio', '#')
     *    );
     *
     *    $safObject->tab((object) $param);
     *
     *   # Navigation do tipo Pilha com menu Dropdown
     *   $tabData = new \stdClass ();
     *   $tabData->type = 'pills';
     *   $tabData->active = 'Home';
     *
     *   # Item 'ICMBio contendo um menu Dropdown
     *   $tabData->item = array(
     *       array('text' => 'Home', 'href' => '#'),
     *       array('text' => 'ICMBio', 'href' => '#', 'dropdown' => array(
     *                                                               array('text' => 'Alerta', 'href' => '#'),
     *                                                               array('text' => 'Sisbio', 'href' => '#')
     *                                                           ))
     * ?>
     * @encode
     * @param stdclass $param
     * @return Tab
     */
    public function navigation (\stdClass $param)
    {
        return $this->_component->navigation($param);
    }

    /**
     * Componente Table
     * @return Table
     */
    public function table ()
    {
        return $this->_component->table();
    }

    /**
     * Componente Table Row
     * @return TableRow
     */
    public function tableRow ()
    {
        return $this->_component->tableRow();
    }

    /**
     * Componente Table Row
     * @return TableRow
     */
    public function tableBody ()
    {
        return $this->_component->tableBody();
    }

    /**
     * Componente Table Head
     * @return TableHead
     */
    public function tableHead ()
    {
        return $this->_component->tableHead();
    }

    /**
     * Componente TableHeaderCell
     * @return TableHeaderCell
     */
    public function tableHeaderCell (\stdClass $param = NULL)
    {
        return $this->_component->tableHeaderCell($param);
    }

    /**
     * Componente Table Data
     * @return TableData
     */
    public function tableData (\stdClass $param = NULL)
    {
        return $this->_component->tableData($param);
    }

    /**
     * Componente UL
     * @return UL
     */
    public function ul ()
    {
        return $this->_component->ul();
    }

    /**
     * Componente LI
     * @return LI
     */
    public function li ()
    {
        return $this->_component->li();
    }

    /**
     * Componente Anchor
     * @return Anchor
     */
    public function anchor (\stdClass $param = NULL)
    {
        return $this->_component->anchor($param);
    }

    /**
     * Componente Text
     * @return Text
     */
    public function text (\stdClass $param = NULL)
    {
        return $this->_component->text($param);
    }

    /**
     * Componente Label
     * @return Label
     */
    public function label (\stdClass $param = NULL)
    {
        return $this->_component->label($param);
    }

    /**
     * Componente Select
     * @return Label
     */
    public function select (\stdClass $param = NULL)
    {
        return $this->_component->select($param);
    }

    /**
     * Componente Select
     * @return Text
     */
    public function spacer (\stdClass $param = NULL)
    {
        return $this->_component->spacer($param);
    }

    /**
     * cria icone
     *
     * @return ElementAbstract
     */
    public function icon (\stdClass $param = NULL)
    {
        return $this->_component->icon($param);
    }
}
