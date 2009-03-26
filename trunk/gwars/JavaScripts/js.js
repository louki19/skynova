window.onresize = AjustarLayout;

$(document).ready(Inicializar);

function Inicializar()
{
	lastPage='visiongeneral.php';
	AjustarLayout();

	ajaxTooltip.initialize();
	ajaxTooltip.loaderEvent='MostrarImagenCarga("toolTip");';

	setInterval("ActualizarDatosCabecera()",1000);

	filtroActual='todos';//Filtro para las pestañas de construccion
}

function AjustarLayout()
{
	var anchoDisponible = document.documentElement.clientWidth;
	var modoGrande=lastPage.split("?")[0]=='imperio.php' || lastPage.split("?")[0]=='galaxia.php';

	gid('controlesGalaxia').style.display='none';
	if(gid('left')==null)
	gid('main').style.marginLeft='0px';

	if(modoGrande)
	{
		document.body.style.paddingLeft=document.body.style.paddingRight='0%';
		if(gid('left')!=null)
		{
			gid('left').style.width='150px';
			gid('main').style.marginLeft='150px';
		}

		if(lastPage.split("?")[0]=='galaxia.php')
		{
			gid('logo').style.width='0px';
			gid('controlesGalaxia').style.display='';
		}
		else
		gid('logo').style.width='195px';
	}
	else
	{
		document.body.style.paddingLeft=document.body.style.paddingRight=Math.round(anchoDisponible/100-3)+'%';
		if(gid('left')!=null)
		{
			gid('left').style.width='';
			gid('main').style.marginLeft='';
		}
		if(anchoDisponible<780)//Ocultar logo
		gid('logo').style.width='0px';
		else if(anchoDisponible<1105)//Ajustar logo
		gid('logo').style.width=($('header').offsetWidth-$('cabecera').clientWidth-10)+'px';
		else
		gid('logo').style.width='';
	}
}

var lastPage;
var loadingRequest;
function Mostrar(url,addToHistory,postData)
{
	if(loadingRequest==true)
	return;

	loadingRequest=true;

	web=url.split("?")[0].length==0?lastPage.split("?")[0]:url.split("?")[0];
	data=url.split("?")[1];

	lastPage=typeof(data)!="undefined"?web+'?'+data:web;

	AjustarLayout();

	if(typeof(postData)!="undefined")
	{
		params={
			url: url,
			type: "POST",
			data:postData
		};
	}
	else
	{
		params={
			url: web,
			type: "GET",
			data:data
		};
	}

	params.success=function(msg){	$('#main').html(msg);PaginaCargada();loadingRequest=false; };
	params.error=function(ajaxObject,description){ ErrorAjax('main',ajaxObject,description); };

	$.ajax(params);
}

function PaginaCargada()
{
	AjustarLayout();

	$("form").each(function(i){
		if(this.getAttribute('onsubmit')==null)
		this.setAttribute('onsubmit','return OnSubmitForm(this)');
	});

	//Ocultar el tooltip
	if(gid('toolTip')!=null)
	gid('toolTip').style.display='none';

	//Buscar nuevos elementos para el tooltip
	ajaxTooltip.scanElements();
	ajaxWindow.scanElements();
}

function ActualizarCabecera()
{
	$('#cabecera').load("cabecera.php",	null, ajaxTooltip.scanElements() );
}

function CambiarPlaneta(planeta)
{
	Mostrar(lastPage,true,'planeta='+planeta);
	ActualizarCabecera();
}

function OnSubmitForm(form,showImage)
{
	$.ajax({
		type: form.method=='get'?"GET":"POST",
		url: form.action.length==0?lastPage:form.action,
		data: httpFormFields(form),
		success: function(msg){
			if($.trim(msg).length>0)
			{
				$('#main').html(msg);
				PaginaCargada();
			}
		}
	});

	if(showImage!=false)
	{
		imagenCarga=document.createElement('img');
		imagenCarga.src=loaderImage;
		imagenCarga.setAttribute('title',textoImagenPost);
		form.appendChild(document.createElement('br'));
		form.appendChild(imagenCarga);
	}
	return false;
}

function TeclaPulsada(e)
{
	var code= e.keyCode;
	if(gid('controlesGalaxia').style.display!='none')
	{
		if(e.keyCode==39)//Tecla derecha
		gid('SA').click();
		else if(e.keyCode==37)//Tecla izquierda
		gid('SS').click();
		else if(e.keyCode==38)//Tecla arriba
		gid('GS').click();
		else if(e.keyCode==40)//Tecla abajo
		gid('GA').click();
	}
}


//Comprueba si existen nuevos mensajes para el jugador
function ComprobarMensajes()
{
	try
	{
		$.ajax({
			type: "GET",
			url: 'comprobarMensajes.php',
			success: function(msg){
				mensajes=parseInt(msg);
				ActualizarMensajes(mensajes);
			}
		});
	}
	catch(e)
	{}
	finally
	{
		comprobarMensajesId=setTimeout("ComprobarMensajes()",30000);
	}
}
var mensajesActuales;
var comprobarMensajesId;

//Actualiza los nuevos mensajes disponibles
function ActualizarMensajes(sinLeer)
{
	if(isNaN(sinLeer)==false)
	{
		if(sinLeer==0)
		{
			gid('imagenMensajes').src=rutaImagenes+'noMessages.png';

			gid('textoMensajes').setAttribute('tip',sinMensajes);
			gid('imagenMensajes').setAttribute('tip',sinMensajes);
		}
		else if(sinLeer!=mensajesActuales)
		{
			gid('imagenMensajes').src=rutaImagenes+'unreadMessages.png';

			if(sinLeer==1)
			{
				gid('textoMensajes').setAttribute('tip',unMensaje);
				gid('imagenMensajes').setAttribute('tip',unMensaje);
			}
			else
			{
				gid('textoMensajes').setAttribute('tip',sinLeer+' '+mensajesNuevos);
				gid('imagenMensajes').setAttribute('tip',sinLeer+' '+mensajesNuevos);
			}
			//$('#imagenMensajes').Bounce(35);

			//Sonido
			document.getElementById('sonidos').innerHTML='<object type="application/x-shockwave-flash" data="dewplayer.swf?mp3='+sonidoNuevoMensaje+'&autoplay=1" width="1" height="1"><param name="movie" value="dewplayer.swf?mp3='+sonidoNuevoMensaje+'&autoplay=1" /></object>';
		}

		gid('textoMensajes').innerHTML=sinLeer;
		mensajesActuales=sinLeer;
	}
}

//Array de actualización de recursos:
//Array(IdElemento, CantidadInicial,ProduccionSegundo,CapacidadAlmacen)
var datosRecursos=new Array();
function ActualizarDatosCabecera()
{
	try
	{
		//Obtener segundos transcurridos desde la actualizacion de la cabecera
		var segundos=(new Date().getTime()/1000)-fechaActualizacionRecursos;

		for (clave in datosRecursos)
		{
			var datos=datosRecursos[clave];
			var elemento=document.getElementById(datos[0]);

			if(elemento==null)
			continue;

			var cantidadActual=datos[1]+Math.round(datos[2]*segundos);
			if(datos[3]<cantidadActual)//Almacen lleno
			{
				elemento.style.color='red';
				if(datos[3]*1.25>cantidadActual)//Aun no completado del todo
				elemento.innerHTML=SeparadorMiles(cantidadActual);
			}
			else
			{
				elemento.style.color='';
				elemento.innerHTML=SeparadorMiles(cantidadActual);
			}
		}
	}
	catch(e)
	{}
}

//Progreso de la cola de construccion o investigacion
function ProgresoCola()
{
	var fechaActual=Math.round(new Date().getTime()/1000);
	var tiempoRestante=fechaFinalizacion-fechaActual;
	var tiempoTranscurrido=tiempoProduccion-tiempoRestante;
	var porcentajeCompletado=Math.round(1000/tiempoProduccion*tiempoTranscurrido)/10;

	var spanTiempoRestante=document.getElementById("tiempoRestante");

	if(spanTiempoRestante==null)//Se ha cambiado de pestaña
	return;

	if(porcentajeCompletado>100 || porcentajeCompletado<0)//No se ha calculado bien el porcentage
	{
		setTimeout('ProgresoCola()', 1000);
		return;
	}

	var divProgreso=document.getElementById("progreso");
	spanTiempoRestante.innerHTML=SegundosATiempo(tiempoRestante);
	divProgreso.style.width=divProgreso.innerHTML=porcentajeCompletado+'%';

	if(tiempoRestante<=0)//Fin
	{
		BackgroundFade(1500,25,"currentQueueInfo",255,204,51,  37,36,42);
		finProduccion=true;
		if($('#restantes').length>0)//Pestaña en hangar o defensa
		{
			var unidadesRestantes=$('restantes');
			restantes=parseInt(unidadesRestantes.childNodes[1].innerHTML);

			if(restantes>0)
			{
				finProduccion=false;

				//Reducir el numero de unidades restantes
				unidadesRestantes.childNodes[1].innerHTML=restantes-1;

				//Calcular la fecha de finalizacion de la siguiente construccion
				fechaFinalizacion+=tiempoProduccion;
				dateFechaFinalizacion=new Date(fechaFinalizacion*1000);
				if (new Date().getDate() != dateFechaFinalizacion.getDate())
				document.getElementById("fechaFinalizacion").innerHTML = formatDate(dateFechaFinalizacion,formatoFecha);
				else
				document.getElementById("fechaFinalizacion").innerHTML = formatDate(dateFechaFinalizacion,formatoTiempo);

				if(restantes==1)//Solo queda la nave actual
				unidadesRestantes.style.display='none';
			}
		}
		if(finProduccion)//Fin de todas las construcciones
		{
			document.getElementById("tiempos").style.display='none';
			if(document.getElementById("cancelCurrentQueue")!=null)
			document.getElementById("cancelCurrentQueue").style.display='none';
			document.getElementById("accionCompletada").style.display='block';
			return;
		}
	}
	setTimeout('ProgresoCola()', 1000);
}

//Cuenta atras de los eventos de la vision general
function CuentaAtrasEventos(establecerFinal)
{
	if(gid('evento0')==null)
	return;

	for(i=0;i<finEventos.length;i++)
	{
		elemento=gid('evento'+i).firstChild;
		tiempoRestante=Math.round((finEventos[i].getTime()-new Date().getTime())/1000);//Segundos restantes para el evento
		if(tiempoRestante<=0 && elemento.innerHTML!=textoHecho)
		{
			//Evento finalizado
			elemento.innerHTML=textoHecho;
			BackgroundFade(1500,25,"evento"+i,255,204,51,  37,36,42);
		}
		else
		{
			if(establecerFinal==true)//Establecer la fecha de final del evento
			{
				if (new Date().getDate() != finEventos[i].getDate())//Mostrar el dia
				elemento.childNodes[2].innerHTML=formatDate(finEventos[i],formatoFecha);
				else//Mostrar solo la hora
				elemento.childNodes[2].innerHTML=formatDate(finEventos[i],formatoTiempo);
			}
			//Mostrar los segundos restantes
			elemento.firstChild.innerHTML=SegundosATiempo(tiempoRestante);
		}
	}
	setTimeout("CuentaAtrasEventos(false)",999);
}

//Expande o contrae el panel de eventos de la visión general
function ExpandirContraerPanelEventos()
{
	var elemento=gid('panelEventos');
	if(elemento.clientHeight<=0)
	ExpandirElemento('panelEventos');
	else
	ContraerElemento('panelEventos');
}

//Expande o contrae el panel de información de rango de alianza
function MostrarInformacionRango() {
	var elemento=gid('rango');
	if(elemento.clientHeight<=0)
	{
		ExpandirElemento('rango',elemento.scrollHeight);
		document.getElementById('infoRango').innerHTML ='[-]';
	}
	else
	{
		ContraerElemento('rango');
		document.getElementById('infoRango').innerHTML ='[+]';
	}
}

function GuardarNota(form)
{
	$.ajax({
		type: "POST",
		url:'notas.php',
		data: httpFormFields(form),
		success: function(msg){
			if($.trim(msg).length>0)
			form.getElementsByTagName('input')[0].value=$.trim(msg);

			$(imagenCarga).remove();

			if(lastPage.split("?")[0]=='notas.php')
			Mostrar('notas.php');
		}
	});

	var imagenCarga=document.createElement('img');
	imagenCarga.src=loaderImage;
	imagenCarga.setAttribute('title',textoImagenPost);
	form.appendChild(imagenCarga);

	return false;
}