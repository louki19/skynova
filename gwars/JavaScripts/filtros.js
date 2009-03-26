function MostrarElemento(id)
{
	elm=document.getElementById(id);
	if(elm!=null)
	elm.style.display='';
}
function OcultarElemento(id)
{
	elm=document.getElementById(id);
	if(elm!=null && elm.style.display!='none')
	elm.style.display='none';
}

function EstablecerTabActivaFiltro(filtro)
{
	filtroActual=filtro;

	elm=document.getElementById(filtro);

	if(elm!=null)
	{
		tabs=elm.parentNode.childNodes;
		for(contador=0;contador<tabs.length;contador++)
		{
			tabs[contador].className="";;
		}

		elm.className="tabSelected";
	}
}

//Muestra únicamente la cola de construcción. Devuelve false si no se debe mostrar el resto de tecnologias disponibles
function FiltroCola(filtro)
{
	if(filtro=='cola' && document.getElementById('tab').style.display!='none' &&
	(document.getElementById('tecnologiaActual')!=null || document.getElementById('tablaCola')!=null))
	{
		document.getElementById('tab').style.display='none';
		return false;
	}
	else
	document.getElementById('tab').style.display='';

	return true;
}

var filtroActual;

function FiltroEdificios(filtro)
{	
	EstablecerTabActivaFiltro(filtro);

	if(FiltroCola(filtro)==false)return;

	if(filtro=='todos')
	{
		for(contador=0;contador<100;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
		return;
	}

	for(contador=0;contador<100;contador++)
	{
		OcultarElemento('tecno'+contador);
	}

	if(filtro=='produccion')
	{
		for(contador=0;contador<20;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else if(filtro=='militares')
	{
		for(contador=20;contador<90;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else if(filtro=='almacenes')
	{
		for(contador=90;contador<100;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else
	FiltroEdificios('todos');
}

function FiltroInvestigaciones(filtro)
{
	EstablecerTabActivaFiltro(filtro);
	if(FiltroCola(filtro)==false)return;

	if(filtro=='todos')
	{
		for(contador=100;contador<250;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
		return;
	}

	for(contador=100;contador<250;contador++)
	{
		OcultarElemento('tecno'+contador);
	}

	if(filtro=='generales')
	{
		MostrarElemento('tecno100');
		MostrarElemento('tecno101');
		MostrarElemento('tecno105');
		MostrarElemento('tecno114');
		MostrarElemento('tecno115');
	}
	else if(filtro=='militares')
	{
		MostrarElemento('tecno102');
		MostrarElemento('tecno103');
		MostrarElemento('tecno104');
	}
	else if(filtro=='motores')
	{
		MostrarElemento('tecno108');
		MostrarElemento('tecno109');
		MostrarElemento('tecno110');
	}
	else if(filtro=='tecnologias')
	{
		MostrarElemento('tecno106');
		MostrarElemento('tecno107');
		MostrarElemento('tecno111');
		MostrarElemento('tecno112');
		MostrarElemento('tecno113');
	}
	else
	FiltroInvestigaciones('todos');
}

function FiltroHangar(filtro)
{
	EstablecerTabActivaFiltro(filtro);
	if(FiltroCola(filtro)==false)return;

	if(filtro=='todos')
	{
		for(contador=250;contador<400;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
		return;
	}

	for(contador=250;contador<400;contador++)
	{
		OcultarElemento('tecno'+contador);
	}

	if(filtro=='general')
	{
		for(contador=250;contador<305;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else if(filtro=='militar')
	{
		for(contador=305;contador<400;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else
	FiltroHangar('todos');
}

function FiltroDefensa(filtro)
{
	EstablecerTabActivaFiltro(filtro);
	if(FiltroCola(filtro)==false)return;

	if(filtro=='todos')
	{
		for(contador=400;contador<600;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
		return;
	}

	for(contador=400;contador<600;contador++)
	{
		OcultarElemento('tecno'+contador);
	}

	if(filtro=='canones')
	{
		for(contador=400;contador<500;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else if(filtro=='otros')
	{
		for(contador=500;contador<600;contador++)
		{
			MostrarElemento('tecno'+contador);
		}
	}
	else
	FiltroDefensa('todos');
}

function FiltroTipoTecnologia(filtro)
{
	EstablecerTabActivaFiltro(filtro);

	if(filtro=='todos')
	{
		MostrarElemento('tablaedificios');
		MostrarElemento('tablainvestigaciones');
		MostrarElemento('tablanaves');
		MostrarElemento('tabladefensas');
		MostrarElemento('tablaedificioslunares');
	}
	else
	{
		OcultarElemento('tablaedificios');
		OcultarElemento('tablainvestigaciones');
		OcultarElemento('tablanaves');
		OcultarElemento('tabladefensas');
		OcultarElemento('tablaedificioslunares');		

		MostrarElemento('tabla'+filtro);
	}
}

function FiltroAnalisisBatalla(filtro)
{
	OcultarElemento('parametros');
	OcultarElemento('resultado');
	OcultarElemento('informe');
	MostrarElemento(filtro);
	filtroActual=filtro;

	
	$('tparametros').className="";
	
	if($('tresultado')!=null)
	$('tresultado').className="";
	
	if($('tinforme')!=null)
	$('tinforme').className="";
	
	if($('t'+filtro)!=null)
	$('t'+filtro).className="tabSelected";
}