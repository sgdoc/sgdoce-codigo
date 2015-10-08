/**
 * Utilitários para manipulação de strings
 * toAscii - remove a acentuação
 * iComp - comparação case insensitive
 */
var StringUtils = {
    /**
     * remove acentuação para operações de comparação
     */
    toAscii : function (str) 
    {
        var rExps=[
        {re:/\xC7/g, ch:'C'},
        {re:/\xE7/g, ch:'c'},
        {re:/[\xC0-\xC6]/g, ch:'A'},
        {re:/[\xE0-\xE6]/g, ch:'a'},
        {re:/[\xC8-\xCB]/g, ch:'E'},
        {re:/[\xE8-\xEB]/g, ch:'e'},
        {re:/[\xCC-\xCF]/g, ch:'I'},
        {re:/[\xEC-\xEF]/g, ch:'i'},
        {re:/[\xD2-\xD6]/g, ch:'O'},
        {re:/[\xF2-\xF6]/g, ch:'o'},
        {re:/[\xD9-\xDC]/g, ch:'U'},
        {re:/[\xF9-\xFC]/g, ch:'u'},
        {re:/[\xD1]/g, ch:'N'},
        {re:/[\xF1]/g, ch:'n'} ];

        for(var i=0, len=rExps.length; i<len; i++)
                str=str.replace(rExps[i].re, rExps[i].ch);

        return str;
    },
    /**
     * Comparação case e accent insensitive
     */
    iComp : function (str1, str2)
    {
        str1 = StringUtils.toAscii(str1.toUpperCase());
        str2 = StringUtils.toAscii(str2.toUpperCase());
        return (str1 == str2) ? true : false;
    },
    /**
     * Faz o parse de um texto substituindo termos chaves por valores mapeados
     * ex: {'campo' : 'valor', 'campo2' : 'valor2'}
     */
    parseTxt : function(txt, keyvals)
    {
    	for (var key in keyvals) {
            if (!keyvals.hasOwnProperty(key)) {
                continue;
            }
            value = keyvals[key];
            txt   = txt.replace(key, value);
        }
    	return txt;
    }
};