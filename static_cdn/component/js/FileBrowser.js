/*
 * permite a manipulacao de arquivos
 * {
 *    allowShow: boolean   - define se será permitido exibir miniatura
 *    allowDelete: boolean - define se será permitido remove elemento
 *    allowSave: boolean   - define se será permitido incluir novos elementos
 *    thumbs: {            - define o tamanho de cada thumbs, necessário apenas se allowShow for igual a true
 *        width: int       - difine tamanho da div thumbs
 *        height: int      - define altura da div thumbs
 *    }
 *    localStorage: JQueryElement -define o elemento que será usado como local, temporário de armazenamento de novos elementos
 *    fileType: string[]    - define os tipos de arquivos aceitos
 *    fileMaxUpload: string - define o tamanho máximo de arquivos aceitos ("8MB", "1GB", ...)
 * }
 * */
; var FileBrowser = function (options) {
    var that  = this;

    var settings = {

        identity: 'fb_' + Math.random().toString().split('.')[1], /* identificador usado para isolar mais de um filebrowser na mesma tela */

        container: document.body, /* define o elemento HTML que mostrar filebrowser, se omitido window será usado */

        permission: {
            allowView:     true, /* define se será permitido exibir miniatura        */
            allowUpload:   true, /* define se será permitido remove elemento         */
            allowremove:   true, /* define se será permitido remove elemento         */
        },

        files: {
            acceptable: ["jpg", "jpeg", "bmp", "pdf"], /* define os tipos de arquivos aceitos */
            fileMaxUpload: "8MB",
        },

        treeview: {
            title: 'Arquivo(s)'
        },

        /* fonte temporaria */
        dataSource: {
            elms: {},
            length: 0
        },

        /* local onde serão despejados os dados processados pelo componente */
        targetSource: {}
    };

    var structure = {
        /* elementos principais do fb */
        container: $('<div>').addClass('fb-container span10'                      ),
        content:   $('<div>').addClass('fb-content fb-content span6 row row-list'),
        toolbar:   $('<div>').addClass('fb-toolbar btn-group pull-right'         ),

        /* elementos da arvore de elementos */
        treeview:      $('<div>').addClass('fb-treeview span3'             ),
        treeviewTitle: $('<h2>' ).addClass('fb-treeview-title'             ),
        treeviewList:  $('<ul>' ).addClass('fb-treeview-list nav nav-list' ),

        /* conjunto de elemntos para envio de arquivos */
        uploader: $('<form>')
    };

    this.__construct = function (options) {

        var dataSource = options.targetSource.val().toString().trim();

        settings.dataSource = dataSource
                            ? $.parseJSON(dataSource)
                            : settings.dataSource;

        $.extend(settings, options);

    }; this.__construct(options);

    /**
     * monta a tela necessário para manipulacao do arquivos
     * */
    this.render = function () {
        $(settings.container)
        .append(fbBuilder());
    };

    var fbBuilder = function () {
        structure.container.attr('id', settings.identity);

        fbCreateStructureTreeview();

        fbCreateStructureToolBar();

        structure.container.append(
            structure.treeview,
            structure.content,
            structure.toolbar
        );

        structure.toolbar.width(
            structure.content.width() + 100
        );

        return structure.container;
    };

    var flushTargetSource = function () {
        settings.targetSource.val(
            JSON.stringify(settings.dataSource)
        );
    };

    var fbCreateStructureTreeview = function () {

        structure.treeviewList.empty();

        structure.treeviewTitle.html(settings.treeview.title);
        structure.treeviewList.append( $('<li>').addClass('divider') );

        structure.treeview.append(
            structure.treeviewTitle,
            structure.treeviewList
        );

        if (! settings.dataSource.length) { return; }

        for (var elm in settings.dataSource.elms) {
            var item   = $('<li>');
            var icon   = $('<i>' ).addClass('icon-file');
            var anchor = $('<a href="javascript:;">'   ).data('key', elm);

            item.append(anchor.append(icon, settings.dataSource.elms[elm].name));

            if (settings.permission.allowView) { anchor.on('click', handleClickListItem); }

            if (settings.permission.allowremove) { anchor.on('dblclick', handleDblClickListItem); };

            structure.treeviewList.append(item);
       }
    };

    var fbCreateStructureToolBar = function () {


        if (settings.permission.allowUpload) {
            var sender = $('<input type="file">');
            sender[0].addEventListener("change", handlerUplaoderFile, false);
            structure.uploader.append(sender);
            structure.toolbar.append(structure.uploader);
        }
    };

    var resentContent = function () {
        structure.content.empty();
    };

    var handleClickListItem = function (event) {

        if (! (key = $(event.target).data('key'))) { return; }

        displayElmOnScreen(key);
    };

    var handleDblClickListItem = function (event) {
        var elm = $(event.target)

        removeDecoreElm(elm);

        if (removeConfirm()) {

            var key = elm.data('key');

            delete settings.dataSource.elms[key];
            settings.dataSource.length--;

            resentContent();

            fbCreateStructureTreeview();

            flushTargetSource();

            return;
        }

        removeUndecoreElm(elm);
    };

    var handlerUplaoderFile = function (event) {
        var file = this.files[0];
        var type = file.type.toString().split('/')[1];

        if (-1 == settings.files.acceptable.indexOf(type)) {
            alert('Tipo de arquivo inaceitável.');
            return;
        }

        if (! handlerUplaoderFileSize(file.size)) {
            alert('O arquivo selecionando é maior que tamanho permitido.');
            return;
        }

        var reader = new FileReader();

        reader.onload = function (event) {
            var data = {
                "name": file.name
                , "size": file.size
                , "type": file.type
                , "content": event.target.result.toString().split(/data:[\w\/]+;base64,/)[1]
                , "status": "transiente"
            };

            var key = md5(data.name);
            settings.dataSource.elms[key] = data;
            settings.dataSource.length++;
            displayElmOnScreen(key);
            fbCreateStructureTreeview();
            structure.uploader[0].reset();

            flushTargetSource();
        };

        reader.readAsDataURL(file);
    };

    var handlerUplaoderFileSize = function (nBytes) {
        /* refer: http://www.t1shopper.com/tools/calculate/ */
        var expoent = {
            "kb": 10, // kilobyte
            "mb": 20, // megabyte
            "gb": 30, // gigabite
            "tb": 40, // teraby
        };

        var size  = settings.files.fileMaxUpload.match(/^\d+/)[0];
        var sigla = settings.files.fileMaxUpload.match(/[a-zA-Z]+/)[0].toString().toLowerCase();

        if (! expoent[sigla]) { return false; }

        return Math.pow(2, expoent[sigla]) * size >= nBytes;
    };

    var displayElmOnScreen = function (key) {
        if (! settings.permission.allowView) {
            return;
        }

        resentContent();
        var elm = $('<div>').addClass('fb-display').append(createElmToDisplay(settings.dataSource.elms[key]));
        structure.content.append(elm);
    };

    var createElmToDisplay = function (elm) {
        var elmContent = null;
        var imageType = /^image\//;

        if (imageType.test(elm.type.toString())) {
            elmContent = $('<img>')
                         .addClass('img-polaroid')
                         .attr('src', 'data:{0};base64,{1}'.format(elm.type, elm.content));
        }

        return elmContent;
    };

    var removeConfirm = function () {
        return confirm("Deseja realmente remover este item?");
    };

    var removeDecoreElm = function (elm) {
        var text = elm.text();
        var icon = elm.find('.icon-file').removeClass('icon-file');
        elm.empty().append(icon.addClass('icon-remove'), $('<s>').append(text));
    };

    var removeUndecoreElm = function (elm) {
        var text = elm.text();
        var icon = elm.find('.icon-remove').removeClass('icon-remove');
        elm.empty().append(icon.addClass('icon-file'), text);
    };
};