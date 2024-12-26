var moduloTipoPrint = 1; // impresora de 58 mm

function infoModuloPrint(url)
{
    url = url || '../restaurantes/ajax.php?c=pedidosActivos&f=moduloTipoPrint';
    var respuesta = {};
    $.ajax({
        url : url,
        type : 'POST',
        dataType : 'json',
        async:false,
    }).done(function(resp) {
        respuesta = resp;
    });

    return respuesta;
}

// regresa un objecto con datos referentes a la impresora indicada
function datosImpresora(impresora)
{
    impresora = String(impresora);
    switch (impresora)
    {
        case '2': // Impresora de 80 mm
            return {tipo: parseInt(impresora), caracteresPorLinea: 44, caracteresProducto: 30}
            break;

        default: // Impresora de 58 mm
            return {tipo: 1, caracteresPorLinea: 32, caracteresProducto: 18}
    }
}

///////////////// ******** ----     generarTicket     ------ ************ //////////////////
//////// Imprime los pedidos de la comanda en una nueva ventana
// Como parametros puede recibir:
// vararray -> array ticket
// varstring -> string a convertir
// bold -> 0: normal, 1: negrita
// underlined -> 0: no, 1: si
// tipo -> 1: left, 2: center, 3: right
function generarTicket(vararray, varstring, bold, underlined, tipo)
{
    var caracteresPorLinea = datosImpresora(moduloTipoPrint).caracteresPorLinea;
    varstring = String(varstring).trim();
    var varstring_copia = varstring.split(" ");
    var string = '';
    var ant_string = '';

    if (varstring.length > caracteresPorLinea)
    {
        for (var x = 0; x < varstring_copia.length; x++)
        {
            ant_string = string.trim();
            string += ' ' + varstring_copia[x];
            if (string.length > caracteresPorLinea)
            {
                //centrado
                var string_spaces = '';
                if (tipo == 2)
                {
                    var spaces = Math.floor((caracteresPorLinea - ant_string.length) / 2);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                }
                else if (tipo == 3)
                {
                    var spaces = caracteresPorLinea - varstring.length;
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                }

                vararray.push({
                    'texto': string_spaces + ant_string,
                    'bold': bold,
                    'underlined': underlined
                });
                string = '';
                x--;
            }
            else if (x == (varstring_copia.length - 1))
            {
                string = string.trim();
                var string_spaces = '';
                if (tipo == 2)
                {
                    var spaces = Math.floor((caracteresPorLinea - string.length) / 2);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                }
                else if (tipo == 3)
                {
                    var spaces = caracteresPorLinea - varstring.length;
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                }

                vararray.push({
                    'texto': string_spaces + string,
                    'bold': bold,
                    'underlined': underlined
                });
            }
        }
    }
    else
    {
        //centrado
        var string_spaces = '';
        if (tipo == 2)
        {
            var spaces = Math.floor((caracteresPorLinea - varstring.length) / 2);
            for (var i = 0; i < spaces; i++)
            {
                string_spaces += ' ';
            }
        }
        else if (tipo == 3)
        {
            var spaces = caracteresPorLinea - varstring.length;
            for (var i = 0; i < spaces; i++)
            {
                string_spaces += ' ';
            }
        }

        vararray.push({
            'texto': string_spaces + varstring,
            'bold': bold,
            'underlined': underlined
        });
    }

    return vararray;
}

///////////////// ******** ----     FIN generarTicket     ------ ************ //////////////////

///////////////// ******** ----     formatearTicketProducts     ------ ************ //////////////////
//////// Imprime los pedidos de la comanda en una nueva ventana
// Como parametros puede recibir:
// vararray -> array ticket
// cant -> cantidad producto
// producto -> descripcion producto
// total -> total
// product -> string a convertir
// bold -> 0: normal, 1: negrita
// underlined -> 0: no, 1: si
// tipo -> 1: left, 2: center, 3: right, 4: products
function formatearTicketProducts(vararray, cant, varproduct, total, bold, underlined)
{
    var caracteresProducto = datosImpresora(moduloTipoPrint).caracteresProducto;
    cant = String(cant).trim();
    varproduct = String(varproduct).trim();
    total = String(total).trim();
    var product_copia = varproduct.split(" ");
    var product = '';
    var ant_product = '';
    var ya = 0;

    if (varproduct.length > caracteresProducto)
    {
        for (var x = 0; x < product_copia.length; x++)
        {
            ant_product = product.trim();
            product += ' ' + product_copia[x];
            if (product.length > caracteresProducto)
            {
                //centrado
                var string_spaces = '';
                var spaces = 0;
                spaces = (caracteresProducto - ant_product.length);
                for (var i = 0; i < spaces; i++)
                {
                    string_spaces += ' ';
                }
                ant_product += string_spaces;
                if (ya == 0)
                {
                    string_spaces = '';
                    spaces = (7 - cant.length);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                    cant += string_spaces;
                    string_spaces = '';
                    spaces = (7 - total.length);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                    total = string_spaces + total;
                    vararray.push({
                        'texto': cant + ant_product + total,
                        'bold': bold,
                        'underlined': underlined
                    });
                    ya = 1;
                }
                else
                {
                    vararray.push({
                        'texto': '       ' + ant_product,
                        'bold': bold,
                        'underlined': underlined
                    });
                }
                product = '';
                x--;
            }
            else if (x == (product_copia.length - 1))
            {
                product = product.trim();
                var string_spaces = '';
                var spaces = 0;
                spaces = (caracteresProducto - product.length);
                for (var i = 0; i < spaces; i++)
                {
                    string_spaces += ' ';
                }
                product += string_spaces;
                if (ya == 0)
                {
                    string_spaces = '';
                    spaces = (7 - cant.length);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                    cant += string_spaces;
                    string_spaces = '';
                    spaces = (7 - total.length);
                    for (var i = 0; i < spaces; i++)
                    {
                        string_spaces += ' ';
                    }
                    total = string_spaces + total;
                    vararray.push({
                        'texto': cant + product + total,
                        'bold': bold,
                        'underlined': underlined
                    });
                    ya = 1;
                }

                vararray.push({
                    'texto': '       ' + product,
                    'bold': bold,
                    'underlined': underlined
                });
            }

        }
    }
    else
    {
        //centrado
        var string_spaces = '';
        var spaces = 7 - cant.length;
        for (var i = 0; i < spaces; i++)
        {
            string_spaces += ' ';
        }
        cant += string_spaces;
        string_spaces = '';
        spaces = caracteresProducto - varproduct.length;
        for (var i = 0; i < spaces; i++)
        {
            string_spaces += ' ';
        }
        varproduct += string_spaces;
        string_spaces = '';
        spaces = 7 - total.length;
        for (var i = 0; i < spaces; i++)
        {
            string_spaces += ' ';
        }
        total = string_spaces + total;
        vararray.push({
            'texto': cant + varproduct + total,
            'bold': bold,
            'underlined': underlined
        });
    }

    return vararray;
}

///////////////// ******** ----     formatearTicket     ------ ************ //////////////////
//////// Imprime los pedidos de la comanda en una nueva ventana
// Como parametros puede recibir:
//  vararray -> array ticket
//  varstring -> string a convertir
//  contador -> contador
function formatearTicket(vararray, varstring, bold, underlined)
{
    var caracteresPorLinea = datosImpresora(moduloTipoPrint).caracteresPorLinea;
    varstring = String(varstring).trim();
    var varstring_copia = varstring.split(" ");
    var string = '';
    var ant_string = '';

    if (varstring.length > caracteresPorLinea)
    {
        for (var x = 0; x < varstring_copia.length; x++)
        {
            ant_string = string.trim();
            string += ' ' + varstring_copia[x];
            if (string.length > caracteresPorLinea)
            {
                vararray.push({
                    'texto': ant_string,
                    'bold': bold,
                    'underlined': underlined
                });
                string = '';
                x--;
            }
            else if (x == (varstring_copia.length - 1))
            {
                vararray.push({
                    'texto': string.trim(),
                    'bold': bold,
                    'underlined': underlined
                });
            }

        }
    }
    else
    {
        vararray.push({
            'texto': varstring,
            'bold': bold,
            'underlined': underlined
        });
    }

    return vararray;
}
///////////////// ******** ----     FIN formatearTicket     ------ ************ //////////////////
