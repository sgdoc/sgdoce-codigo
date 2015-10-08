/**
 * abreviacao do console.log
 * */
var cl = function() { return console.log.apply(console, arguments); };

/*
 * equivalente ao spintf do PHP
 *
 * @example 'The {0} is dead. Don\'t code {0}. Code {1} that is open source!'.format('ASP', 'PHP');
 * @author http://stackoverflow.com/questions/610406/javascript-equivalent-to-printf-string-format
 * */
if (!String.prototype.format) {
    String.prototype.format = function() {
        var formatted = this;
        for (var i = 0; i < arguments.length; i++) {
            var regexp = new RegExp('\\{' + i + '\\}', 'gi');
            formatted = formatted.replace(regexp, arguments[i]);
        }
        return formatted;
    };
}

/*
 * equivalente ao ucfirst do PHP
 *
 * @author http://snipplr.com/view/43670/
 * */
if (!String.prototype.ucfirst) {
    String.prototype.ucfirst = function() {
        var string = this;
        return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
    };
}

/**
 * @use ["x", "y"].indexOf("x")
 * */
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

/**
 * var textarea = $(this).val();
 * var find = ["<", ">", "\n"];
 * var replace = ["&lt;", "&gt;", "<br/>"];
 * textarea = textarea.replaceArray(find, replace);
 * */
if (!String.prototype.replaceArray) {
    String.prototype.replaceArray = function(find, replace) {
      var replaceString = this;
      var regex;
      for (var i = 0; i < find.length; i++) {
        regex = new RegExp(find[i], "g");
        replaceString = replaceString.replace(regex, replace[i]);
      }
      return replaceString;
    };
}

/**
 * retorna o numero delementos do objeto
 *
 * var foo = {b: 'bar', f: 'foo'};
 * Object.size(foo); // output: 2
 * */
Object.size = function (obj) {
    var size = 0, key;

    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }

    return size;
}

function camelCase (input) {
   return input.toLowerCase().replace(/_(.)/g, function(match, group1) {
       return group1.toUpperCase();
   });
}