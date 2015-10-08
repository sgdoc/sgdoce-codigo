;(function($){
    $.fn.datepicker.dates['pt-BR'] = {
    days: ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado", "Domingo"],
    daysShort: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb", "Dom"],
    daysMin: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab", "Dom"],
    months: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
    monthsShort: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
    today: "Hoje"
};
}(jQuery));

$(document).ready(function() {
    var options = {
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        autoclose: true
    };

    $('.datepicker, .date').datepicker(options);
});