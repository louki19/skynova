function TodasLasNaves(inicio)
{
	for (var nave in datos)
	{
		var naveActual=datos[nave];

		if(naveActual[0]<inicio)
		continue;

		var actual="nave"+naveActual[0];
		if (document.getElementsByName(actual)[0])
		{
			document.getElementsByName(actual)[0].value=naveActual[5];
		}
	}
	ActualizarInformacion();
}
function NingunaNave()
{
	for (contador=250; contador < 400; contador++)
	{
		var actual="nave"+contador;

		if (document.getElementsByName(actual)[0])
		{
			document.getElementsByName(actual)[0].value='';
		}
	}
	ActualizarInformacion();
}

function MostrarPlanetasAccesoRapido()
{
	elemento = document.getElementById('accesoRapido');
	if(elemento.clientHeight<=0)
	{
		ExpandirElemento("accesoRapido", elemento.scrollHeight);
		document.getElementById('linkAccesoRapido').innerHTML= "[-]";
	}
	else
	{
		ContraerElemento("accesoRapido");
		document.getElementById('linkAccesoRapido').innerHTML= "[+]";
	}
}

function EstablecerDestino(galaxia,sistema,posicion,tipo)
{
	document.getElementsByName('galaxiaD')[0].value=galaxia;
	document.getElementsByName('sistemaD')[0].value=sistema;
	document.getElementsByName('posicionD')[0].value=posicion;
	document.getElementsByName('tipoDestino')[0].value=tipo;
}

function ActualizarInformacion()
{
	/*
	Los arrays contenidos en la variable datos contienen:
	0 - ID de la nave
	1 - velocidad
	2 - capacidad
	3 - consumo
	4 - nombre
	5 - cantidad en el planeta
	6 - poder de ataque
	*/
	var capacidadDeCargaTotal=0;
	var velocidadMinima;
	var nombreNaveLenta="";
	var consumo=0;
	var galaxia=document.getElementsByName('galaxiaD')[0].value;
	var sistema=document.getElementsByName('sistemaD')[0].value;
	var posicion=document.getElementsByName('posicionD')[0].value;
	var distancia=CalcularDistancia(planetaActual,	Array(galaxia,sistema,posicion));
	var porcentajeVelocidad=document.getElementsByName('velocidad')[0].value;
	var poderAtaqueTotal=0;

	var tiposDeNaveEnvidadas=0;
	for(i=0;i<datos.length;i++)
	{
		var naveActual=datos[i];
		var cantidad=document.getElementsByName('nave'+naveActual[0])[0].value;

		if(isNaN(parseInt(cantidad)))
		continue;

		if(isNaN(parseInt(velocidadMinima)) || naveActual[1]<velocidadMinima)
		{
			velocidadMinima=naveActual[1];
			nombreNaveLenta=naveActual[4]
		}

		capacidadDeCargaTotal+=cantidad*naveActual[2];
		poderAtaqueTotal+=cantidad*naveActual[6];

		tiposDeNaveEnvidadas++;
	}

	recicladores=document.getElementsByName('nave302')[0];
	if(tipoDestinoCambiado==false && tiposDeNaveEnvidadas==1 && recicladores!=null && recicladores.value>0)
	{
		tipoDestinoCambiado=true;
		document.getElementsByName('tipoDestino')[0].value='escombros';
	}

	velocidadMinima=Math.round(velocidadMinima*(porcentajeVelocidad/100),0);

	document.getElementById('distancia').innerHTML=SeparadorMiles(distancia);
	document.getElementById('capacidadCargaNaves').innerHTML=SeparadorMiles(capacidadDeCargaTotal);
	document.getElementById('poderAtaque').innerHTML=SeparadorMiles(poderAtaqueTotal);

	for(i=0;i<datos.length;i++)//Calcular el consumo de las naves
	{
		var naveActual=datos[i];
		var cantidad=document.getElementsByName('nave'+naveActual[0])[0].value;
		if(isNaN(parseInt(cantidad)))
		continue;

		consumo+=cantidad*(1+Math.round(((distancia*naveActual[3]*velocidadMinima)/(naveActual[1]*137277000))));
	}

	if(isNaN(parseInt(velocidadMinima)))
	{
		document.getElementById('velocidadMaxima').innerHTML=0;
		document.getElementById('naveLenta').innerHTML="";
	}
	else
	{
		document.getElementById('velocidadMaxima').innerHTML=SeparadorMiles(velocidadMinima);
		document.getElementById('naveLenta').innerHTML="("+nombreNaveLenta+")";
	}

	if(isNaN(parseInt(distancia))==false && isNaN(parseInt(velocidadMinima))==false)
	{
		segundosVuelo=Math.round(distancia/velocidadMinima);
		document.getElementById('tiempoVuelo').innerHTML=SegundosATiempo(segundosVuelo);
		document.getElementById('consumo').innerHTML=SeparadorMiles(consumo);
	}
	else
	{
		document.getElementById('consumo').innerHTML=0;
		segundosVuelo=null;
		document.getElementById('tiempoVuelo').innerHTML=0;
	}

	MostrarFechasLlegada();
}
var segundosVuelo;

function MostrarFechasLlegada()
{
	if (isNaN(parseInt(segundosVuelo)) || segundosVuelo==0)
	{
		document.getElementById('horaLlegada').innerHTML='-';
		document.getElementById('horaRegreso').innerHTML='-';
	}
	else
	{
		fechaActual = new Date();
		fechaLlegada=new Date(fechaActual.getTime()+segundosVuelo*1000);
		fechaRegreso=new Date(fechaActual.getTime()+segundosVuelo*2000);

		elmLlegada=document.getElementById('horaLlegada');

		if(elmLlegada==null)
		return;

		if (fechaActual.getDate() != fechaLlegada.getDate())//Mostrar tambien el día
		elmLlegada.innerHTML = formatDate(fechaLlegada,formatoFecha);
		else//Mostrar solo la hora de llegada
		elmLlegada.innerHTML = formatDate(fechaLlegada,formatoTiempo);

		elmRegreso=document.getElementById('horaRegreso');

		if (fechaActual.getDate() != fechaRegreso.getDate())//Mostrar tambien el día
		elmRegreso.innerHTML = formatDate(fechaRegreso,formatoFecha);
		else
		elmRegreso.innerHTML = formatDate(fechaRegreso,formatoTiempo);

		setTimeout('MostrarFechasLlegada()',1000);
	}
}

function CalcularDistancia(origen,destino)
{
	if(origen[0]!=destino[0])//Distinta galaxia
	{
		galaxiasDistancia=Math.min(Math.abs(origen[0]+9-destino[0]),Math.abs(origen[0]-destino[0]));
		return 352609000+(116304500*(galaxiasDistancia-1));
	}
	else
	{
		if(origen[1]!=destino[1])//Distinto sistema
		{
			sistemasDistancia=Math.min(Math.abs(origen[1]+499-destino[1]),Math.abs(origen[1]-destino[1]));
			return 137277000+(1361100*(sistemasDistancia-1));
		}
		else
		{
			if(origen[2]!=destino[2])//Distinta posicion
			{
				return 78442800+(130740*(Math.abs(origen[2]-destino[2])-1));
			}
			else
			{
				return 384400;
			}
		}
	}
}

function EnviarFlota()
{
	destinoLuna=document.getElementsByName('tipoDestino')[0].value=='luna';
	destinoEscombros=document.getElementsByName('tipoDestino')[0].value=='escombros';

	if(destinoEscombros)
	{
		if(document.getElementsByName('nave302')[0]!=null && document.getElementsByName('nave302')[0].value>0)
		{
			OnSubmitForm(document.getElementsByName('flota')[0]);
		}
		else
		alert('Debes enviar recicladores para recolectar los escombros.');
	}
	else if(planetaActual[0]==document.getElementsByName('galaxiaD')[0].value &&
	planetaActual[1]==document.getElementsByName('sistemaD')[0].value &&
	planetaActual[2]==document.getElementsByName('posicionD')[0].value &&
	planetaEsLuna == destinoLuna)
	{
		//Mismo planeta de origen y destino
		alert('El planeta de origen y destino es el mismo');
	}
	else
	{
		//Comprobar si se han especificado naves
		var navesEnviadas=false;
		for (contador=250; contador < 400; contador++)
		{
			var actual="nave"+contador;
			if(document.getElementsByName(actual)[0]!=null && document.getElementsByName(actual)[0].value>0)
			{
				navesEnviadas=true;
				break;
			}
		}

		if(navesEnviadas)
		OnSubmitForm(document.getElementsByName('flota')[0]);
		else
		alert('No has especificado naves para enviar');
	}
}

function EstablecerRecurso(id)
{

	elm=document.getElementById('recurso'+id);
	capacidadRestante=parseInt(document.getElementById('unidadesRestantes').innerHTML.replace(/[.]/g,''));
	recursos=0;

	if(id==1)recursos=metalPlaneta;
	if(id==2)recursos=cristalPlaneta;
	if(id==3)recursos=antimateriaPlaneta;

	recursosAnadidos=0;
	if(capacidadRestante<=0)return;
	else if(capacidadRestante<recursos)
	recursosAnadidos=capacidadRestante;
	else
	recursosAnadidos=recursos;

	elm.value=recursosAnadidos;
	RecursosCambiados();
}

function RecursosCambiados()
{
	recursos=0;
	for(contador=1;contador<=3;contador++)
	{
		valor=parseInt(document.getElementById('recurso'+contador).value);
		if(isNaN(valor)==false)
		recursos+=valor;
	}
	restantes=capacidadCarga-recursos;
	elm=document.getElementById('unidadesRestantes');
	elm.innerHTML=SeparadorMiles(restantes);

	if(restantes<0)
	{
		document.getElementById('enviarFlota').disabled=true;
		elm.style.color='red';
	}
	else
	{
		document.getElementById('enviarFlota').disabled=false;
		elm.style.color='lime';
	}
}

function TodosLosRecursos()
{
	EstablecerRecurso(3);
	EstablecerRecurso(2);
	EstablecerRecurso(1);
}