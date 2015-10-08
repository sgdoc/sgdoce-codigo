$.extend( $.fn.dataTableExt.oStdClasses, {
	"sWrapper": "dataTables_wrapper form-inline"
} );

/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
	return {
		"iStart":         oSettings._iDisplayStart,
		"iEnd":           oSettings.fnDisplayEnd(),
		"iLength":        5,
		"iTotal":         oSettings.fnRecordsTotal(),
		"iFilteredTotal": oSettings.fnRecordsDisplay(),
		"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
		"iTotalPages":    Math.ceil( oSettings._iRecordsTotal / oSettings._iDisplayLength  )
	};
}

/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
	"bootstrap": {
		"fnInit": function( oSettings, nPaging, fnDraw ) {
			var oLang = oSettings.oLanguage.oPaginate;
			var fnClickHandler = function ( e ) {
				e.preventDefault();
				if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
					fnDraw( oSettings );
				}
			};

			$(nPaging).addClass('pagination').append(
				'<ul>'+
					'<li class="prev disabled" title="'+oLang.sPrevious+'"><a href="#">&larr;</a></li>'+
					'<li class="next disabled" title="'+oLang.sNext+'"><a href="#">&rarr;</a></li>'+
				'</ul>'
			);
			var els = $('a', nPaging);
			$(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
			$(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
		},

		"fnUpdate": function ( oSettings, fnDraw ) {
			var oPaging = oSettings.oInstance.fnPagingInfo();
			var iListLength = oPaging.iLength;
            var an = oSettings.aanFeatures.p;
			var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

			if ( oPaging.iTotalPages < iListLength) {
				iStart = 1;
				iEnd = oPaging.iTotalPages;
			}
			else if ( oPaging.iPage <= iHalf ) {
				iStart = 1;
				iEnd = iListLength;
			} else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
				iStart = oPaging.iTotalPages - iListLength + 1;
				iEnd = oPaging.iTotalPages;
			} else {
				iStart = oPaging.iPage - iHalf + 1;
				iEnd = iStart + iListLength - 1;
			}

			for (var i=0, iLen=an.length ; i<iLen ; i++ ) {
				// Remove the middle elements
				$('li:gt(0)', an[i]).filter(':not(:last)').remove();

				// Add the new list items and their event handlers
				for (var j=iStart ; j<=iEnd ; j++ ) {
					sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
					$('<li '+sClass+'><a href="#">'+j+'</a></li>')
						.insertBefore( $('li:last', an[i])[0] )
						.bind('click', function (e) {
							e.preventDefault();
							oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oSettings._iDisplayLength;
							fnDraw( oSettings );
						} );
				}

				// Add / remove disabled classes from the static elements
				if ( oPaging.iPage === 0 ) {
					$('li:first', an[i]).addClass('disabled');
				} else {
					$('li:first', an[i]).removeClass('disabled');
				}

				if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
					$('li:last', an[i]).addClass('disabled');
				} else {
					$('li:last', an[i]).removeClass('disabled');
				}
			}
		}
	}
});

$.extend($.fn.dataTableExt.oStdClasses,
    {
        "sSortAsc": "header headerSortDown",
        "sSortDesc": "header headerSortUp",
        "sSortable": "header"
    }
    );

var Grid = {

    options:function(target){

        if (typeof target === 'string') {
            var url = target;
            var serverParams = function() {};
        } else {
            var url = target.attr('action');
            var serverParams = function ( aoData ) {
                $('select:not(:disabled), input:not(:radio):not(:checkbox):not(:disabled)', target).each(function (i, element) {
                    aoData.push( { 'name': element.name, 'value': element.value } );
                });

                $('input:radio:checked:not(:disabled), input:checkbox:checked:not(:disabled)', target).each(function (i, element) {
                    aoData.push( { 'name': element.name, 'value': element.value } );
                });

            };
        }

        return {
            'bAutoWidth': false,
            'bProcessing': true,
            'bJQueryUI': false,
            'bServerSide': true,
            'bFilter': false,
            'sPaginationType': 'bootstrap',
            'iDisplayLength': 10,
            'bRetrieve':true,
            'sAjaxSource': url,
            'fnServerParams': serverParams,
            "sDom": "<'row-fluid'<'span6'fr><'span6'l>>t<'row-fluid'<'span6'i><'span6'p>>",
            "oLanguage": {
                "sProcessing":   '<label>Carregando registros<span class="threeLittleDots">.</span><span class="threeLittleDots">.</span><span class="threeLittleDots">.</span></label>',
                "sLengthMenu":   "Registros por página _MENU_",
                "sZeroRecords":  "Nenhum registro encontrado.",
                "sInfo":         "Mostrando _START_ a _PAGINADO_",
                "sInfoEmpty":    "Mostrando de 0 até 0 de 0 registros",
                "sInfoFiltered": " de _MAX_ registros.",
                "sInfoPostFix":  "",
                "sSearch":       "Buscar:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Primeira",
                    "sPrevious": "Anterior",
                    "sNext":     "Próxima",
                    "sLast":     "Última"
                }
            }
        }
    },
    loadNoPagination: function(target, element){
        this.load(target, element);
        element.siblings('.row-fluid').hide();

        var headers = element.find('.header');

        $(document).ajaxStop(function(){
            headers.removeClass('headerSortDown');
        });

        headers.unbind('click');
    },
    load: function(target, element){

        element.addClass('table')
        .addClass('table-striped')
        .addClass('table-bordered')
        .dataTable(Grid.options(target));

        if (typeof target === 'object') {
            target.unbind('submit').bind('submit', function(){
                if (target.valid()) {

                    $(document).ajaxStop(function(){
                        element.parents('.hidden').removeClass('hidden');
                        $('.alert').css('display', 'none');
                    });

                    element.fnDraw(false);

                    return false;
                }

                return false;
            });
        } else {
            element.unbind('submit').bind('submit', function() {
                element.fnDraw(false);
                return false;
            });
        }

        $('.sorting_disabled').unbind('click');
    },

    actionDefault: function(codigo, tipo, callBack){
        switch (tipo){
            case 'edit':
                Navegacao.dispatch('edit/codigo/' + codigo);
                break;

            case 'delete':
                if(!callBack){
                    callBack = function(){
                        Navegacao.dispatch('delete/codigo/' + codigo);
                    }
                }

                Message.showConfirmation({
                    'body': 'Deseja realmente excluir?',
                    'yesCallback': callBack
                });

                break;
        }
    }
}
