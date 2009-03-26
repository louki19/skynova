function gid(id) {return(document.getElementById(id)); }

// Devuelve todos los campos y datos
// de un formulario tipo campo1=dato1&campo2=dato2&...
function httpFormFields(formObject) {
	var fields="";
	for (x=0;x<formObject.length;x++) {
		if (formObject[x].name) {
			switch (formObject[x].type) {
				case "checkbox":  fields+=(x>0?"&":"")+formObject[x].name+"="+(formObject[x].checked?"1":"0"); break;
				case "radio":if(formObject[x].checked)fields+=(x>0?"&":"")+formObject[x].name+"="+escape(formObject[x].value);break;
				default: fields+=(x>0?"&":"")+formObject[x].name+"="+escape(formObject[x].value);
			}
		}
	}
	return(fields);
}

function ControlNumerico(inp)
{
	if (isNaN(parseInt(inp.value)))
	{
		inp.value = '';
	}
	else
	{

		inp.value=parseInt(inp.value);
	}
}

function ControlDistintoCero(id)
{
	if (id.value == '' || id.value == '0')
	id.value = '1';
}

function ComprobarLimite(elm,limite)
{
	if (parseInt(elm.value)>limite)
	{
		elm.value = limite;
		elm.onkeyup();
	}
}

function SegundosATiempo(segundos)
{
	var resultado='';
	if(segundos>86400)
	{
		resultado=Math.floor(segundos/86400)+unidadesTiempo.split(' ')[0]+' ';
		segundos=Resto(segundos,86400);
	}
	if(segundos>3600)
	{
		resultado+=Math.floor(segundos/3600)+unidadesTiempo.split(' ')[1]+' ';
		segundos=Resto(segundos,3600);
	}
	if(segundos>60)
	{
		resultado+=Math.floor(segundos/60)+unidadesTiempo.split(' ')[2]+' ';
		segundos=Resto(segundos,60);
	}
	resultado+=segundos+unidadesTiempo.split(' ')[3];
	return resultado;
}

function Resto(a, b)
{
	return a - Math.floor(a / b) * b;
}

function SeparadorMiles(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + '.' + '$2');
	}
	return x1 + x2;
}

/*

Efectos para la web

*/

//Efectos para expandir y contraer elementos
function ContraerElemento(id)
{
	var elemento=gid(id);
	elemento.style.overflow='hidden';
	elemento.originalHeight=elemento.clientHeight;

	$('#'+id).animate(
	{
		height: '0px',
	},1000);
}

function ExpandirElemento(id,originalHeight)
{
	var elemento=gid(id);

	if (!isNaN(parseInt(originalHeight)))
	{
		elemento.originalHeight=originalHeight;
	}

	$('#'+id).animate(	{
		height: elemento.originalHeight+'px',
	},1000);
}

//Efecto fade para fondos
function BackgroundFade(totalTime,steps,elementID,startR, startG, startB,endR, endG, endB)
{
	stepTime=totalTime/steps;

	RedIncr=(startR-endR)/steps;
	GreenIncr=(startG-endG)/steps;
	BlueIncr=(startB-endB)/steps;

	Red=startR;
	Green=startG;
	Blue=startB;

	for(contador=0;contador<steps;contador++)
	{
		changeColor="if(document.getElementById('"+elementID+"')!=null)document.getElementById('"+elementID+"').style.backgroundColor='rgb("+Math.round(Red)+", "+Math.round(Green)+", "+Math.round(Blue)+")';";
		window.setTimeout(changeColor,contador*stepTime);

		Red-=RedIncr;
		Green-=GreenIncr;
		Blue-=BlueIncr;
	}

	//Quitar color de fondo
	window.setTimeout("if(document.getElementById('"+elementID+"')!=null)document.getElementById('"+elementID+"').style.backgroundColor='';",contador*stepTime);
}


// ------------------------------------------------------------------
// formatDate (date_object, format)
// Returns a date in the output format specified.
// The format string uses the same abbreviations as in getDateFromFormat()
// http://www.mattkruse.com/javascript/date/source.html
// ------------------------------------------------------------------
function LZ(x) {return(x<0||x>9?"":"0")+x}
function formatDate(date,format) {
	format=format+"";
	var result="";
	var i_format=0;
	var c="";
	var token="";
	var y=date.getYear()+"";
	var M=date.getMonth()+1;
	var d=date.getDate();
	var E=date.getDay();
	var H=date.getHours();
	var m=date.getMinutes();
	var s=date.getSeconds();
	var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
	// Convert real date parts into formatted versions
	var value=new Object();
	if (y.length < 4) {y=""+(y-0+1900);}
	value["y"]=""+y;
	value["yyyy"]=y;
	value["yy"]=y.substring(2,4);
	value["M"]=M;
	value["MM"]=LZ(M);
	value["d"]=d;
	value["dd"]=LZ(d);
	value["H"]=H;
	value["HH"]=LZ(H);
	if (H==0){value["h"]=12;}
	else if (H>12){value["h"]=H-12;}
	else {value["h"]=H;}
	value["hh"]=LZ(value["h"]);
	if (H>11){value["K"]=H-12;} else {value["K"]=H;}
	value["k"]=H+1;
	value["KK"]=LZ(value["K"]);
	value["kk"]=LZ(value["k"]);
	if (H > 11) { value["a"]="PM"; }
	else { value["a"]="AM"; }
	value["m"]=m;
	value["mm"]=LZ(m);
	value["s"]=s;
	value["ss"]=LZ(s);
	while (i_format < format.length) {
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
		}
		if (value[token] != null) { result=result + value[token]; }
		else { result=result + token; }
	}
	return result;
}