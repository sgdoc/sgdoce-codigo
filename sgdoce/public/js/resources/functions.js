function sprintf() {
    //   example 1: sprintf("%01.2f", 123.1);
    //   returns 1: 123.10
    //   example 2: sprintf("[%10s]", 'monkey');
    //   returns 2: '[    monkey]'
    //   example 3: sprintf("[%'#10s]", 'monkey');
    //   returns 3: '[####monkey]'
    //   example 4: sprintf("%d", 123456789012345);
    //   returns 4: '123456789012345'
    //   example 5: sprintf('%-03s', 'E');
    //   returns 5: 'E00'

    var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
    var a = arguments;
    var i = 0;
    var format = a[i++];

    // pad()
    var pad = function (str, len, chr, leftJustify) {
        if (!chr) {
            chr = ' ';
        }
        var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0)
                .join(chr);
        return leftJustify ? str + padding : padding + str;
    };

    // justify()
    var justify = function (value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
        var diff = minWidth - value.length;
        if (diff > 0) {
            if (leftJustify || !zeroPad) {
                value = pad(value, minWidth, customPadChar, leftJustify);
            } else {
                value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
            }
        }
        return value;
    };

    // formatBaseX()
    var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {
            '2': '0b',
            '8': '0',
            '16': '0x'
        }[base] || '';
        value = prefix + pad(number.toString(base), precision || 0, '0', false);
        return justify(value, prefix, leftJustify, minWidth, zeroPad);
    };

    // formatString()
    var formatString = function (value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
        if (precision != null) {
            value = value.slice(0, precision);
        }
        return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
    };

    // doFormat()
    var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
        var number, prefix, method, textTransform, value;

        if (substring === '%%') {
            return '%';
        }

        // parse flags
        var leftJustify = false;
        var positivePrefix = '';
        var zeroPad = false;
        var prefixBaseX = false;
        var customPadChar = ' ';
        var flagsl = flags.length;
        for (var j = 0; flags && j < flagsl; j++) {
            switch (flags.charAt(j)) {
                case ' ':
                    positivePrefix = ' ';
                    break;
                case '+':
                    positivePrefix = '+';
                    break;
                case '-':
                    leftJustify = true;
                    break;
                case "'":
                    customPadChar = flags.charAt(j + 1);
                    break;
                case '0':
                    zeroPad = true;
                    customPadChar = '0';
                    break;
                case '#':
                    prefixBaseX = true;
                    break;
            }
        }

        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if (!minWidth) {
            minWidth = 0;
        } else if (minWidth === '*') {
            minWidth = +a[i++];
        } else if (minWidth.charAt(0) == '*') {
            minWidth = +a[minWidth.slice(1, -1)];
        } else {
            minWidth = +minWidth;
        }

        // Note: undocumented perl feature:
        if (minWidth < 0) {
            minWidth = -minWidth;
            leftJustify = true;
        }

        if (!isFinite(minWidth)) {
            throw new Error('sprintf: (minimum-)width must be finite');
        }

        if (!precision) {
            precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type === 'd') ? 0 : undefined;
        } else if (precision === '*') {
            precision = +a[i++];
        } else if (precision.charAt(0) == '*') {
            precision = +a[precision.slice(1, -1)];
        } else {
            precision = +precision;
        }

        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

        switch (type) {
            case 's':
                return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
            case 'c':
                return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
            case 'b':
                return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'o':
                return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'x':
                return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'X':
                return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad)
                        .toUpperCase();
            case 'u':
                return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
            case 'i':
            case 'd':
                number = +value || 0;
                number = Math.round(number - number % 1); // Plain Math.round doesn't just truncate
                prefix = number < 0 ? '-' : positivePrefix;
                value = prefix + pad(String(Math.abs(number)), precision, '0', false);
                return justify(value, prefix, leftJustify, minWidth, zeroPad);
            case 'e':
            case 'E':
            case 'f': // Should handle locales (as per setlocale)
            case 'F':
            case 'g':
            case 'G':
                number = +value;
                prefix = number < 0 ? '-' : positivePrefix;
                method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
                textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
                value = prefix + Math.abs(number)[method](precision);
                return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
            default:
                return substring;
        }
    };

    return format.replace(regex, doFormat);
}

var Dates = {
    convertBrToUs: function (s) {
        var datePart = s.match(/\d+/g),
                Year = datePart[2],
                Month = datePart[1],
                Day = datePart[0];

        return Year + '-' + Month + '-' + Day;
    },
    convert: function (d) {
        // Converts the date in d to a date-object. The input can be:
        //   a date object: returned without modification
        //  an array      : Interpreted as [year,month,day]. NOTE: month is 0-11.
        //   a number     : Interpreted as number of milliseconds
        //                  since 1 Jan 1970 (a timestamp)
        //   a string     : Any format supported by the javascript engine, like
        //                  "YYYY/MM/DD", "MM/DD/YYYY", "Jan 31 2009" etc.
        //  an object     : Interpreted as an object with year, month and date
        //                  attributes.  **NOTE** month is 0-11.
        return (
                d.constructor === Date ? d :
                d.constructor === Array ? new Date(d[0], d[1], d[2]) :
                d.constructor === Number ? new Date(d) :
                d.constructor === String ? new Date(d) :
                typeof d === "object" ? new Date(d.year, d.month, d.date) :
                NaN
                );
    },
    compare: function (a, b) {
        // Compare two dates (could be of any type supported by the convert
        // function above) and returns:
        //  -1 : if a < b
        //   0 : if a = b
        //   1 : if a > b
        // NaN : if a or b is an illegal date
        // NOTE: The code inside isFinite does an assignment (=).
        return (
                isFinite(a = this.convert(a).valueOf()) &&
                isFinite(b = this.convert(b).valueOf()) ?
                (a > b) - (a < b) :
                NaN
                );
    },
    inRange: function (d, start, end) {
        // Checks if date in d is between dates in start and end.
        // Returns a boolean or NaN:
        //    true  : if d is between start and end (inclusive)
        //    false : if d is before start or after end
        //    NaN   : if one or more of the dates is illegal.
        // NOTE: The code inside isFinite does an assignment (=).
        return (
                isFinite(d = this.convert(d).valueOf()) &&
                isFinite(start = this.convert(start).valueOf()) &&
                isFinite(end = this.convert(end).valueOf()) ?
                start <= d && d <= end :
                NaN
                );
    }
}

/**
 *
 * @param {string} value
 * @returns {Boolean}
 */
function isCPFValid(value) {
    value = value.replace('.', '');
    value = value.replace('.', '');
    var cpf = value.replace('-', '');

    if (cpf == "") {
        return true;
    }

    while (cpf.length < 11)
        cpf = "0" + cpf;
    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
    var a = [];
    var b = new Number;
    var c = 11;
    for (i = 0; i < 11; i++) {
        a[i] = cpf.charAt(i);
        if (i < 9)
            b += (a[i] * --c);
    }
    if ((x = b % 11) < 2) {
        a[9] = 0
    } else {
        a[9] = 11 - x
    }
    b = 0;
    c = 11;
    for (y = 0; y < 10; y++)
        b += (a[y] * c--);
    if ((x = b % 11) < 2) {
        a[10] = 0;
    } else {
        a[10] = 11 - x;
    }
    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg))
        return false;

    return true;

}

/**
 *
 * @param {string} cnpj
 * @returns {Boolean}
 */
function isCNPJValid(cnpj) {
    cnpj = cnpj.replace('/', '');
    cnpj = cnpj.replace('.', '');
    cnpj = cnpj.replace('.', '');
    cnpj = cnpj.replace('-', '');

    if (cnpj == "") {
        return true;
    }

    var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
    digitos_iguais = 1;

    if (cnpj.length < 14 && cnpj.length < 15) {
        return false;
    }
    for (i = 0; i < cnpj.length - 1; i++) {
        if (cnpj.charAt(i) != cnpj.charAt(i + 1)) {
            digitos_iguais = 0;
            break;
        }
    }

    if (!digitos_iguais) {
        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0, tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0)) {
            return false;
        }
        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) {
            return false;
        }
        return true;
    } else {
        return false;
    }

}

/**
 * Comment
 */
function isCEPValid(cep) {
    // Caso o CEP não esteja nesse formato ele é inválido!
    var expr = /^[0-9]{5}-[0-9]{3}$/;
    var arError = ['00000-000'];
    if(cep.length > 0){
        if ($.inArray(cep,arError) !== -1) {
            return false;
        }
        if(expr.test(cep))
            return true;
        else
            return false;
    }else{
        return true;
    }
}

/**
 * @href http://www.comprasgovernamentais.gov.br/paginas/comunicacoes-administrativas/numero-unico-de-protocolo-nup
 *
 * @param {string} nuProcessoSemDV
 * @returns {string}
 */
 function calculaDVNupSiorg( nuProcessoSemDV ) {

    if( nuProcessoSemDV != NaN ) {
        //Retorna a string dele
        var StringDoNUP = nuProcessoSemDV.toString();

        //Pega os 12 primeiros dígitos do NUP
        var parte1 = StringDoNUP.substring(0, 12);
         //Pega os últimos dígitos do NUP
        var parte2 = StringDoNUP.substring(12, StringDoNUP.length);

        //Adiciona os dois zeros finais à segunda parte do NUP
        parte2 = parte2 + "00";

        //Resto da divisão inteira por 97
        var resto1 = parte1 % 97;

        //Concatena ao resto da divisão a segunda parte do NUP, depois calcula o resto da divisão inteira
        var resto2 = (resto1.toString() + parte2) % 97;

        //Calcula o DV
        var DV = 98 - resto2;

        //Se o DV foi menor que 10, tem que adicionar um zero a esquerda
         if (DV < 10) {
            DV = "0" + DV;
         }

        return DV;
    }
}



/**
 *
 * FUNÇÕES PARA CALCULO DO "MODULO" com numeros bigint
 *
 * Usage: bi_mod('009999900000001201576',97)
 *
 */
// CONSTANTS:
const bi_RADIX = 10000000;
const bi_RADINV = 1 / bi_RADIX;
const bi_RADSQR = bi_RADIX * bi_RADIX;
const bi_LRADIX = 1000000000000000;


function bi_trim0(s) {  // trim leading zeros from an "integer" string s
    while (s.charAt(0) == '0' && s.length > 1)
        s = s.substring(1);
    return s;
}

function vld(s) {
    var i, s = s.toString().replace(/[^\-\d]/g, '');

    if (s.lastIndexOf('-') > 0)
        s = '-' + s.replace(/\-/g, '');
    if (!s.match(/[1-9]/))
        s = '0';
    else {
        if (s.charAt(0) == '0')
            s = s.replace(/^0+/, '');
        if (s.substr(0, 2) == '-0')
            s = s.replace(/^\-0+/, '-');
    }
    return s;
}

function sgn(v) { // sign of v
    if (v == '0')
        return  0;
    if (v.charAt(0) == '-')
        return -1;
    return 1;
}

function abv(v) { // absolute value of v
    if (v.charAt(0) == '-')
        return v.substring(1);
    return v;
}

// function less(v1,v2) returns true iff v1 < v2 (numerically)
// works with pre-validated "signed integer" strings v1, v2
function less(v1, v2) {
    var s1 = 1;
    if (v1 == '0')
        s1 = 0;
    if (v1.charAt(0) == '-')
        s1 = -1;
    var s2 = 1;
    if (v2 == '0')
        s2 = 0;
    if (v2.charAt(0) == '-')
        s2 = -1;

    //if (v1==v2) return false;      // equal values
    if (s1 != s2)
        return (s1 < s2);  // different signs
    if (s1 == 1)
        return (v1.length < v2.length || (v1.length == v2.length && v1 < v2)) // unequal positives
    if (s1 == -1)
        return (v1.length > v2.length || (v1.length == v2.length && v1 > v2)) // unequal negatives
    return false;
}

function subtractUntilLess(a1, a2) {  // expected: a1>a2>0
    var L1 = a1.length, L2 = a2.length, L = Math.max(L1, L2), M = Math.ceil(L / 15);
    var n, k, i = 0, r = [], b1 = [], b2 = [];
    r[M] = 0;

    for (var i = 0, k = 0; k < L; k += 15, i++) {
        b1[i] = a1.substring(L1 - k - 15, L1 - k) - 0;
        b2[i] = a2.substring(L2 - k - 15, L2 - k) - 0;
    }
    while (!less_(b1, b2)) {
        for (var i = 0; i < M; i++) {
            b1[i] = b1[i] - b2[i];
            if (b1[i] < 0) {
                b1[i] += bi_LRADIX;
                b1[i + 1] -= 1;
            } // borrow
        }
    }
    for (var i = 0; i < M; i++)
        b1[i] = ('' + (bi_LRADIX + b1[i])).substring(1);
    return bi_trim0(b1.reverse().join(''));
}

function less_(x, y) { // vectors x,y
    var i, xL = x.length, yL = y.length;
    if (xL < yL) {
        for (i = xL; i < yL; i++)
            if (y[i])
                return 1;
        for (i = xL - 1; i >= 0; i--) {
            if (x[i] > y[i])
                return 0;
            if (x[i] < y[i])
                return 1;
        }
    }
    else {
        for (i = yL; i < xL; i++)
            if (x[i])
                return 0;
        for (i = yL - 1; i >= 0; i--) {
            if (x[i] > y[i])
                return 0;
            if (x[i] < y[i])
                return 1;
        }
    }
    return 0;
}

function bi_mod(x1, x2) {
    var v1 = vld(x1), s1 = sgn(v1), a1 = abv(v1),
            v2 = vld(x2), s2 = sgn(v2), a2 = abv(v2),
            r = '0';

    if (s2 == 0)
        return 'x mod y  is the remainder r of the division x/y; it is undefined for y = 0. \nFor x,y > 0, the quotient q and remainder r are defined by  x = qy + r,  0 \u2264 r < y.\nFor any x and nonzero y, the remainder r satisfies:  x = qy + r,  |r| < |y|.';
    if (s1 > 0)
        r = bi_modU(a1, a2);
    if (s1 < 0)
        r = '-' + bi_modU(a1, a2);
    if (r == '-0')
        return '0';
    return r;
}

function bi_str2vec(s, len) {
    var v = [], i = 0, k;
    if (s.charAt(0) == '-')
        s = s.substring(1);
    for (k = s.length; k > 0; k -= 7)
        v[i++] = s.substring(k - 7, k) - 0;
    v[i++] = 0;
    if (len != null)
        while (i < len)
            v[i++] = 0;
    return v;
}

function bi_vec2str(v) {
    var r = [];
    for (var i = 0; i < v.length; i++) {
        if (v[i] >= bi_RADIX) {
            v[i + 1] += Math.floor(v[i] * bi_RADINV);
            v[i] = v[i] % bi_RADIX;
        }
        r[i] = ('' + (bi_RADIX + v[i])).substring(1);
    }
    return bi_trim0(r.reverse().join(''));
}

//returns a duplicate of bigInt x
function bi_dup(x) {
    var buff = new Array(x.length);
    bi_copy_(buff, x);
    return buff;
}

//do x=y on bigInts x and y.  x must be an array at least as big as y (not counting the leading zeros in y).
function bi_copy_(x, y) {
    var i, xL = x.length, k = Math.min(xL, y.length);
    for (i = 0; i < k; i++)
        x[i] = y[i];
    for (i = k; i < xL; i++)
        x[i] = 0;
}

function bi_copyInt_(x, n) {
    x[0] = n % bi_RADIX;
    x[1] = Math.floor(n * bi_RADINV);
    for (var i = x.length - 1; i > 1; i--)
        x[i] = 0;
}

// bi_mod_ called from bi_GCD_
function bi_mod_(x, y) { // input vectors x,y; result returned in x
    var q = new Array(x.length); //bi_copyInt_(q,0);
    var r = new Array(x.length); //bi_copyInt_(r,0);
    bi_divide_(x, y, q, r)
    bi_copy_(x, r);
}

function bi_modU(a1, a2) { // unsigned "integer" strings
    var L1 = a1.length, k = a2.length, c = 1, r = 0, b, d, n2;

    if (a2 == 0)
        return 'Cannot divide by zero.'
    if (less(a1, a2))
        return a1;
    if (a1 == a2)
        return '0';
    if (k < 15) {
        n2 = 1 * a2;
        for (var i = 0; i < L1; i++) {
            r = (r + c * a1.charAt(L1 - i - 1)) % n2;
            c = (c * 10) % n2;
        }
        return r.toString();
    }
    if (a1.length <= k + 1) {
        r = subtractUntilLess(a1, a2);
        return r;
    }
    var x = bi_str2vec(a1, 2);
    var y = bi_str2vec(a2, 2);
    var q = new Array(x.length); //bi_copyInt_(q,0);
    var r = new Array(x.length); //bi_copyInt_(r,0);
    bi_divide_(x, y, q, r)

    return bi_vec2str(r);
}

function bi_divide_(x, y, q, r) {  // modified HAC Algorithm 14.20 taking advantage of FPU
    var i, j, n, t, d, qj;
    if (bi_zero(y))
        return;  // 'Cannot divide by zero.'

    if (bi_equalV(x, y)) {
        bi_copyInt_(q, 1);
        bi_copyInt_(r, 0);
        return;
    }
    if (bi_hod(y) == 0 && y[0] == 1) {
        bi_copy_(q, x);
        bi_copyInt_(r, 0);
        return;
    }

    bi_copyInt_(q, 0);
    bi_copy_(r, x);

    if (less_(x, y))
        return;

    n = bi_hod(x); // n = index of x's most significant "digit"
    t = bi_hod(y); // t = index of y's most significant "digit"

    r[-1] = 0;
    r[-2] = 0;
    y[-1] = 0;

    d = 1.0 / (bi_RADIX * y[t] + y[t - 1]);

    j = n - t;
    qj = Math.floor((bi_RADIX * r[n] + r[n - 1] + bi_RADINV * r[n - 2]) * d);
    if (qj >= bi_RADIX)
        qj = bi_RADIX - 1;

    if (qj)
        bi_linCombShift_(r, y, -qj, j);

    while (bi_negativeOrNaN(r)) {
        qj--;
        bi_copy_(r, x);
        bi_linCombShift_(r, y, -qj, j);
    }
    while (!bi_less_shf(r, y, j)) {
        qj++;
        bi_subShift_(r, y, j);
    }
    q[j] = qj;

    for (i = n; i > t; i--) {
        j = i - t - 1;
        qj = Math.floor(0.000000003 + (bi_RADSQR * r[i] + bi_RADIX * r[i - 1] + r[i - 2]) * d);
        if (qj >= bi_RADIX)
            qj = bi_RADIX - 1;

        bi_linCombShift_(r, y, -qj, j);
        if (isNaN(r[r.length - 1]) || r[r.length - 1] < 0) {
            bi_addShift_(r, y, j);
            qj--;
        }
        q[j] = qj;
    }
    delete r[-1];
    delete r[-2];
    delete y[-1];
}

function bi_addShift_(x, y, ys) {
    var i, c, k, xL = x.length, yL = y.length, k = Math.min(xL, yL + ys);

    for (c = 0, i = ys; i < k; i++) {
        c += x[i] + y[i - ys];
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
    for (i = k; c && i < xL; i++) {
        c += x[i];
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
}

function bi_subShift_(x, y, ys) { // x,y nonnegative;
    var i, c, xL = x.length, yL = y.length, k = Math.min(xL, yL + ys);

    for (c = 0, i = ys; i < k; i++) {
        c += x[i] - y[i - ys];
        if (c < 0) {
            c + bi_RADIX;
            x[i + 1]--;
        }
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
    for (i = k; c && i < xL; i++) {
        c += x[i];
        if (c < 0) {
            c + bi_RADIX;
            x[i + 1]--;
        }
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
    // For negatives only: must go on borrowing
    for (; x[i] < 0 && i < xL - 1; i++) {
        x[i] += bi_RADIX;
        x[i + 1]--;
    }
}

function bi_less_shf(x, y, ys) { // x,y nonnegative; y not zero; returns (x < y*RADIX^ys)
    var i, xL = x.length, yL = y.length, k = (xL < yL + ys ? xL : yL + ys);  //k=Math.min(xL,yL+ys);

    for (i = k; i < xL; i++)
        if (x[i] > 0)
            return 0;
    for (i = k - ys; i < yL; i++)
        if (y[i] > 0)
            return 1;
    for (i = k - 1; i >= ys; i--) {
        if (x[i] > y[i - ys])
            return 0;
        if (x[i] < y[i - ys])
            return 1;
    }
    return 0;
}

function bi_linCombShift_(x, y, b, ys) {  // let x = x + b*y*RADIX^ys
    var i, j, c = 0, xL = x.length, yL = y.length, k = Math.min(xL, yL + ys);

    for (i = ys; i < k; i++) {
        c += x[i] + b * y[i - ys];
        if (c < 0) {
            j = Math.ceil(-c * bi_RADINV);
            c += j * bi_RADIX;
            x[i + 1] -= j;
        }
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
    for (i = k; c && i < xL; i++) {
        c += x[i];
        if (c < 0) {
            j = Math.ceil(-c * bi_RADINV);
            c += j * bi_RADIX;
            x[i + 1] -= j;
        }
        x[i] = c % bi_RADIX;
        c = Math.floor(c * bi_RADINV);
    }
    // For negatives only: must go on borrowing
    for (; x[i] < 0 && i < xL - 1; i++) {
        x[i] += bi_RADIX;
        x[i + 1]--;
    }
}

function bi_zero(x) {
    for (var i = x.length - 1; i >= 0; i--)
        if (x[i])
            return 0;
    return 1;
}

function bi_negativeOrNaN(x) {
    if (isNaN(x[x.length - 1]) || x[x.length - 1] < 0)
        return 1;
    return 0;
}

function bi_equalV(x, y) { // vectors x,y
    var i, xL = x.length, yL = y.length, k = (xL < yL ? xL : yL);
    for (i = 0; i < k; i++)
        if (x[i] != y[i])
            return 0;
    if (xL > yL) {
        for (; i < xL; i++)
            if (x[i])
                return 0;
    }
    else {
        for (; i < yL; i++)
            if (y[i])
                return 0;
    }
    return 1;
}

function bi_hod(x) { // the high-order digit (most significant element) of x
    for (var i = x.length - 1; i > 0; i--)
        if (x[i])
            return i;
    return 0;
}
/*
 * FINAL BLOCO DE FUNÇÕES DE CALCULO DE "MODULO"
 */
