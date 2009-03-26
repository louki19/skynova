<?php
include(dirname(dirname(__FILE__)).'/Language/spanish.php');

$configuracion=array(
//Configuración general
'Universo'=>1,//Numero o nombre de universo
'TinyMCElanguage'=>'es',
'ServImg'=>'./Imagenes/imagenPlaneta.php',//Url del archivo php que fusiona los fondos con las imagenes de los planetas

//Configuración de la base de datos
'HostDB'=>'localhost',
'userDB'=>'root',
'passDB'=>'root',
'tableDB'=>'gwars',
'Backup'=>'',//Destino de la backup

//Configuración de las opciones del juego

//Produccion
'TiempoProduccion'=>1,//Divide el resultado de el tiempo de producción de una tecnologia entre este numero
'CosteTecnologia'=>1,//Divide el coste de una tecnologia por este numero
'NivelProduccion'=>1,//Multiplica la produccion de una mina por este numero
'VelocidadVuelo'=>200,//Multiplica la velocidad de vuelo de todas las naves por este numero

//Batallas
'PorcentageEscombros'=>50,//Porcentage de perdidas que van a escombros
'PorcentageDefensaEscombros'=>0,//Porcentage de las defensas perdidas que van a escombros
'PorcentageRecursosRobados'=>55,//Porcentage de recursos que se roban en una batalla
);

require('DataBase.php');

$DB= ConnectDataBase($configuracion['HostDB'],$configuracion['userDB'],$configuracion['passDB'],$configuracion['tableDB']);
?>