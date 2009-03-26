<?php
/*Technologies with ID between:

� 1 and 10 - are mines
� 10 and 20 - are energy producers
� 20 and 80 - are builds
� 80 and 90 - lunar builds
� 90 and 100 - are store
� 100 and 250 - are investigations
� 250 and 305 - are general ships
� 305 and 400 - are war ships
� 400 and 500 - are defense
� 500 and 600 - are others, like misils and cupula
*/

function GetTechnology($ID)
{
	switch ($ID)
	{
		default:
			return;

			//Production Builds

		case 1:
			return new Technology($ID,
			'Mina de metal',
			'Proporcionan metal, el recurso b&aacute;sico para el imperio.',
			'El metal es el recurso b�sico para la construcci�n de eficios, naves y sistemas de defensa. Su recolecci�n se realiza bajo la superficie terrestre, por lo tanto es un recurso barato y disponible. Sin embargo, cada vez se requiere de m�s energ�a y m�s coste econ�mico para acceder a minas m�s profundas que permitan una recolecci�n mas abundate de este material.');

		case 2:
			return new Technology($ID,
			'Mina de cristal',
			'Las minas de cristal proporcionan el recurso base para la construcci�n de circuitos y elementos electr�nicos.',
			'El cristal es el elemento m�s b�sico de cualquier estructura electr�nica, como los microprocesadores incorporados en las computadoras. Su uso por tanto es elevado, y es necesario para la construcci�n y la investigaci�n de casi cualquier tecnolog�a y nave. Al igual que el metal, el cristal tambi�n es un mineral, y debe ser obtenido en minas subterr�neas. Sin embargo, una vez obtenido debe ser tratado qu�micamente para otorgarle las propiedades necesarias para su uso industrial, lo que implica un mayor gasto energ�tico y econ�mico que la producci�n metal�rgica.');

		case 3:
			return new Technology($ID,
			'Acelerador de part�culas',
			'El acelerador de part�culas es un instrumento que se encarga de acelerar part�culas subat�micas hasta velocidades cercanas a la de la luz para producir Antimateria.',
			'Los aceleradores de part�culas son enormes m�quinas que mediante impulos magn�ticos aceleran las part�culas subat�micas hasta unos 290.000 kil�metros por segundo, y producen choques entre ellas. De esta colisi�n se produce antimateria, o lo que es lo mismo, materia compuesta de antipart�culas de las part�culas que constituyen la materia normal, como el antihidr�geno.<br /><br />
En las colisiones entre materia y antimateria, se convierte toda la masa posible de las part�culas en energ�a. Esta forma de producir energ�a es extremadamente efectiva, con un rendimiento del 100%, pero a la vez es muy escasa, pues s�lo se obtienen unos pocos nanogramos de estas colisiones.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Acelerador_de_part%C3%ADculas\')">Wikipedia</a>');

		case 10:
			return new Technology($ID,
			'Planta de fusi�n nuclear',
			'Las plantas de fusi�n producen energ�a mediante procesos at�micos de fusi�n.',
			'Las plantas de fusi�n son una fuente de energ�a limpia y econ�mica, basadas en los procesos nucleares que ocurren en el interior de las estrellas que les permiten brillar y aportar calor. Proporcionan la energ�a el�ctrica que requieren los edificios para su funcionamiento, como las minas, a partir de procesos de fusi�n nuclear, en el que dos �tomos se juntan en uno s�lo, desprendiendo de esta acci�n una gran cantidad de energ�a. En estas plantas se necesitan enormes campos magn�ticos para poder soportar las elevad�simas temperaturas y presiones que se necesitan en el proceso.<br /><br />Las plantas de fusi�n dejan de ser rentables cuando la demanda de energ�a es extremadamente alta, para ello deben usarse formas de energ�a a�n mas eficaces, como la antimateria.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Fusi%C3%B3n_nuclear\')">Wikipedia</a>');

		case 11:
			return new Technology($ID,
			'Planta de antimateria',
			'La planta de antimateria utiliza peque�as cantidades de antimateria para producir energ�a el�ctrica en grandes proporciones.',
			'La antimateria es el combustible m�s eficaz que existe, con un rendimiento del 100%. Con un s�lo nanogramo de antimateria se podr�a abastecer la demanda energ�tica de un planeta entero en un d�a.
<br /><br />Al entrar en contacto antimateria con la materia normal, ambas se aniquilan produciendo un estallido de energ�a pura, como un Big-Bang en peque�as proporciones. Las plantas de antimaterian se encargan de mezclar ambos elementos y transformar la energ�a emitida del proceso en energ�a el�ctrica.');

		case 12:
			return new Technology($ID,
			'Planta de microondas',
			'Las plantas de microondas producen energ�a a partir de radiaciones microondas emitidas por sat�lites espaciales.',
			'Las plantas de microondas se complementan con sat�lites solares colocados orbitando alrededor del planeta. Estos sat�lites son los encargados de capturar la energ�a proveniente del sol, mediante paneles fotovoltaicos, y enviarla al planeta en forma de microondas. Las plantas de microondas capturan la energ�a transmitida y la convierten en corriente el�ctrica �til para minas y edificios.<br /><br />Por desgracia, estos sat�lites no disponen de dispositivos defensivos, por lo que en caso de ataque son destruidos con relativa facilidad.<br/><br/>Ver tambi�n <a onclick="Mostrar(\'descripcion.php?id=299\')">Sat�lite Solar</a><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Energ%C3%ADa_solar\')">Wikipedia</a>');

			// Militar builds
		case 20:
			return new Technology($ID,
			'Laboratorio de investigaci�n',
			'En el laboratorio se investigan y descubren las nuevas tecnolog�as.',
			'El laboratorio es el lugar donde se re�nen las mentes m�s avanzadas para investigar las nuevas tecnolog�as que permitir�n el avance del imperio. Cada nivel de ampliaci�n supone una mayor inversi�n en el laboratorio, permitiendo as� una mayor velocidad en la investigaci�n, y la apertura de nuevos campos que investigar.');

		case 21:
			return new Technology($ID,
			'F�brica de Robots',
			'Los robots ayudan en la construcci�n y ampliaci�n de los edificios planetarios, disminuyendo el tiempo de construcci�n de los edificios.',
			'Desde sus or�genes el ser humano se ha servido de si mismo para realizar tareas y trabajos que requiriesen el uso de su inteligencia. Con la aparici�n de los primeros robots y aut�matas, el ser humano se vi� liberado de tareas tales como la construcci�n, la agricultura, la pesca, etc. Estas tareas eran ahora desempe�ados por los robots, lo que permiti� al ser humano centrarse en actividades tales como la investigaci�n, en la que importaba el conocimiento y no la destreza manual.<br /><br/>Fue la aparici�n de los robots el hecho que propici� el avance significativo de la raza humana durante el sigo XXII, as� como la conquista del espacio.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Robot\')">Wikipedia</a>');


		case 22:
			return new Technology($ID,
			'F�brica de Nanobots',
			'Los nanobots son el avance de los robots, mejorando su velocidad de construcci�n en edificios y naves.',
			'Los nanobots son el resultado de la fusi�n entre <a onclick="window.open(\'http://es.wikipedia.org/wiki/Robotica\')">rob�tica</a> y <a onclick="window.open(\'http://es.wikipedia.org/wiki/Nanotecnolog%C3%ADa\')">nanotecnolog�a</a>. Estos dispositivos inteligentes con un tama�o similar al de c�lulas biol�gicas, son programados para trabajar conjuntamente en la construcci�n de edificios y naves.<br/><br/>Debido a su min�sculo tama�o, los nanobots operan en grandes redes, formadas por varios millones de unidades, que consiguen velocidades de construcci�n muy superior a las conseguidas mediante robots normales.');

		case 23:
			return new Technology($ID,
			'Hangar',
			'En el hangar se construyen y reparan las naves y estructuras de defensa planetaria.',
			'El hangar es el centro neur�lgico del ej�rcito de un planeta. Desde el hangar se construyen las naves y los sistemas defensivos de un planeta y se reparan las estructuras da�adas. Sin el hangar no se pueden construir dispositivos de defensa planetaria.<br /><br />Si se combina con los nanobots, el hangar puede construir naves y defensas a velocidades muy superiores a las normales.');

		case 24:
			return new Technology($ID,
			'Estaci�n espacial',
			'La estaci�n espacial permite a las flotas aliadas que est�n en �rbita repostar combustible.',
			'La estaci�n espacial es una estructura artificial colocada en �rbita alrededor del planeta que permite a las flotas aliadas respostar las naves para aumentar su tiempo de espera en la �rbita planetaria. Cada nivel de mejora de la espaci�n permite una ampliaci�n de �sta y, por lo tanto, una mayor capacidad de repostaje para las naves aliadas.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Estaci%C3%B3n_espacial\')">Wikipedia</a>');


		case 25:
			return new Technology($ID,
			'Silo de misiles',
			'Desde el silo se pueden lanzar y almacenar misiles planetarios.',
			'Desde el silo se pueden construir, almacenar y lanzar misiles planetarios. Existen diferentes tipos de misiles, cada uno con su funci�n espec�fica.<br/><br/>Cada nivel del silo ampliaci�n permite una mayor velocidad de construcci�n de misiles, as� como un mayor espacio de almacionamiento de misiles.');
			//Lunar builds

		case 80:
			return new Technology($ID,
			'Base lunar',
			'Permite la construcci�n de estructuras en la superficie lunar.',
			'La base lunar aporta las condiciones necesarias para la vida biol�gica de un ser vivo en un sat�lite lunar. Aporta el ox�geno, la gravedad y la temperatura adecuadas para la vida, as� como la energ�a el�ctrica necesaria para el funcionamiento de las estructuras m�s b�sicas.');

		case 81:
			return new Technology($ID,
			'Sensor espacial',
			'Estos sensores son capaces de analizar un gran espacio para detectar flotas y movimientos de naves enemigas.',
			'Los sensores espaciales est�n dotados de las mismas tecnolog�as que las sondas de espionaje, y permiten analizar mediante radiofrecuencia el espacio alrededor del satelite. Estos sensores son capaces de percibir cualquier frecuencia proveniente de un planeta para detectar las naves y flotas enemigas en vuelo.<br><br>Cada nivel aumentado del sensor, o cada nivel aumentado de la tecnolog�a de espionaje, permite que estos sensores alcancen un mayor espacio de rastreo.');


		case 82:
			return new Technology($ID,
			'Salto cu�ntico',
			'El salto cu�ntico realiza movimientos de flota de manera instant�nea entre dos puntos distintos del espacio.',
			'Los portales de salto cu�ntico utilizan la m�s novedosa tecnolog�a de hiperespacio para "comprimir" la distancia entre dos puntos del universo y hacer as� el viaje entre ambos inst�ntaneo. Con esta tecnolog�a se consigue que cualquier flota, independientemente de su tama�o, pueda viajar entre portales en cuesti�n de d�cimas de segundo.<br><br>Estos portales necesitan la m�s alta tecnolog�a y una gran inversi�n para su consrucci�n.');

			//Stores

		case 90:
			return new Technology($ID,
			'Almac�n de metal',
			'Permite almacenar grandes cantidades de metal.',
			'El almac�n de metal permite almacenar en sus bodegas grandes cantidades de �ste material. Cada nivel de ampliaci�n supone un mayor espacio disponible para almacenar este recurso.<br/><br/>Cuando los almacenes se llenan, la producci�n de metal se detiene hasta que haya espacio de nuevo disponible para su almacenaje.');

		case 91:
			return new Technology($ID,
			'Almac�n de cristal',
			'Permite almacenar grandes cantidades de cristal.',
			'El almac�n de cristal permite almacenar en sus bodegas grandes cantidades de �ste material. Cada nivel de ampliaci�n supone un mayor espacio disponible para almacenar este recurso.<br/><br/>Cuando los almacenes se llenan, la producci�n de cristal se detiene hasta que haya espacio de nuevo disponible para su almacenaje.');

		case 92:
			return new Technology($ID,
			'Trampa de antimateria',
			'Las trampas de antimateria permiten almacenar este recurso impidiendo que entre en contacto con la materia normal.',
			'La antimateria es un material peligroso de manejar. Simplemente entrando en contacto con el aire, producir�a una explosi�n 100 veces superior a una explosi�n nuclear.<br/><br/>En las trampas de antimateria, se a�sla a �sta completamente de cualquier forma de materia, mediante la combinaci�n de campos magn�ticos y el�ctricos, reteniendo as� las part�culas en el centro del contenedor, suspendida en el vac�o. Para poder realizar este proceso satisfactoriamente, se requieren grandes cantidades de energ�a.');

			//Investigations

		case 100:
			return new Technology($ID,
			'Tecnolog�a de espionaje',
			'El espionaje permite obtener informaci�n sobre otros planetas e imperios.',
			'La tecnolog�a avanza, y la forma de espiar con ella. La tecnolog�a de espionaje investiga los sensores, las sondas y la inteligencia artificial para equipar naves con la misi�n de obtener la mayor informaci�n posible sobre un imperio, as� como las herramientas para la defensa ante espionajes enemigos.<br/><br/>Cada nivel de esta tecnolog�a proporciona sondas de espionaje m�s r�pidas y con capacidad para obtener y procesar m�s informaci�n.');

		case 101:
			return new Technology($ID,
			'Tecnolog�a de computaci�n',
			'Investiga las mejoras en los microprocesadores y computadoras. Cada nivel proporciona mayor capacidad de env�o de flota.',
			'La tecnolog�a de computaci�n se encarga de estudiar y mejorar el rendimiento y la velocidad de los microprocesadores usados en las computadoras.<br/><br/>El aumento de esta tecnolog�a permite procesar m�s r�pidamente la informaci�n sobre las flotas enviadas, y por tanto permite que un mayor n�mero de �stas puedan viajar a la vez.');

		case 102:
			return new Technology($ID,
			'Tecnolog�a militar',
			'La tecnolog�a militar se encarga de aumentar la eficacia de los sistemas de armamento de naves y defensas.',
			'La tecnolog�a militar es la encargada de mejorar los sistemas de armamento. Cada mejora de esta tecnolog�a permite la creaci�n de armas para naves y defensas m�s potentes y mort�feras.');

		case 103:
			return new Technology($ID,
			'Tecnolog�a de defensa',
			'Esta tecnolog�a incrementa la eficacia de los sistemas defensivos. Cada nivel de aumento produce mejores sistemas de defensa.',
			'Esta tecnolog�a se encarga de justamente lo contrario que la militar: incrementar la eficacia de los sistemas defensivos. Esta tecnolog�a investiga y mejora los escudos de energ�a usados en las naves para protegerse, permitiendo por cada nivel investigado escudos m�s densos y resistentes.');

		case 104:
			return new Technology($ID,
			'Tecnolog�a de blindaje',
			'Investiga mejoras en los metales que forman las naves y las defensas, permitiendo estructuras m�s resistentes.',
			'La tecnolog�a de blindaje investiga nuevas aleaciones de metal que sean m�s resistentes ante ataques y golpes. Esto permite naves y defensas con m�s defensa f�sica, cuando no disponen de escudo protector. Cada nivel investigado produce naves y defensas mas resistentes.');

		case 105:
			return new Technology($ID,
			'Tecnolog�a de energ�a',
			'Esta tecnolog�a investiga nuevas formas de aprovechar la energ�a, y descubre otras fuentes de energ�a nuevas.',
			'La energ�a es una necesidad b�sica en cualquier imperio, y por tanto, es una de las tecnolog�as m�s avanzadas. La tecnolog�a de energ�a investiga sobre nuevas formas de aprovechar las fuentes de energ�a conocidas, permitiendo aumentos de su rendimiento; as� como tambi�n se encarga del descubrimiento de nuevas formas de energ�a m�s rentables.<br/><br/>Cada nivel que se aumenta esta tecnolog�a permite un aumento de la producci�n energ�tica en 1% en plantas y sat�lites solares; y la disminuci�n en un 1% del consumo en naves.');

		case 106:
			return new Technology($ID,
			'Tecnolog�a de antimateria',
			'Investiga el uso de antimateria como forma de obtenci�n de energ�a y como arma de destrucci�n masiva.',
			'La antimateria es posiblemente la mejor forma de energ�a del universo. Sin embargo, su manejo es complicado, puesto que al entrar en contacto con materia normal ambas se aniquilan y producen una explosi�n de energ�a devastadora. Su coste de producci�n tambi�n es muy elevado, lo que hace que sea un recurso escaso.<br/><br/>La antimateria se emplea como fuente de energ�a en plantas de antimateria y tambi�n como sistemas de armamento para naves y defensas como el ca�on de antimateria o el interceptor.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Antimateria\')">Wikipedia</a>');

		case 107:
			return new Technology($ID,
			'Tecnolog�a de hiperespacio',
			'Esta tecnolog�a logra curvar la dimensi�n espacio-tiempo, permitiendo as� viajes a velocidades mayores que las de la luz.',
			'El hiperespacio es la zona del universo que posee m�s de tres dimensiones. Para poder acceder a �l, es necesario curvar el espacio-tiempo mediante grandes fuerzas gravitacionales, permitiendo as� que dos distancias lejanas se acercasen. Este m�todo tambi�n permitir�a poder superar la barrera de la luz en velocidad de viaje, lo que supone una considerable disminuci�n de los tiempos de vuelo entre planetas.<br/><br/>Esta tecnolog�a es la base para la creaci�n de motores que aprovechen el hiperespacio para desplazarse.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Hiperespacio_%28geometr%C3%ADa%29\')">Wikipedia</a>');

		case 108:
			return new Technology($ID,
			'Motor de combusti�n',
			'El motor de combusti�n es el motor m�s rudimentario usado para viajes espaciales. Cada nivel investigado produce motores m�s eficientes.',
			'El motor de combusti�n se basa en las leyes f�sicas de acci�n y reacci�n. Las part�culas son aceleradas en su interior y expulsadas hacia fuera, creando una fuerza de repulsi�n que mueve la nave. Estos motores permiten velocidades de viaje bajas, pero son econ�micos, fiables y consumen poca energ�a.<br/><br/>Cada nivel que se investiga de esta tecnolog�a permite la producci�n de motores de combusti�n un 5% m�s eficaces y r�pidos.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Motor_de_combusti%C3%B3n\')">Wikipedia</a>');

		case 109:
			return new Technology($ID,
			'Motor de impulso',
			'El motor de impulso es una evoluci�n del motor de combusti�n, usando antimateria como forma de energ�a para desplazarse.',
			'Los motores de impulso son la evoluci�n del motor de combusti�n tradicional. Utilizan antimateria para desplazar las part�culas, lo que permiten que las naves se desplacen a velocidades mayores con un menor gasto de energ�a.<br/><br/>Cada nivel que se investiga de esta tecnolog�a permite la producci�n de motores de impulso un 10% m�s eficaces y r�pidos.');

		case 110:
			return new Technology($ID,
			'Propulsor hiperespacial',
			'Los propulsores hiperespaciales se basan en la teor�a del hiperespacio para desplazar cuerpos a velocidades mayores que la de la luz.',
			'El propulsor hiperespacial utiliza la tecnolog�a del hiperespacio para curvar el espacio-tiempo y entrar en un entorno de m�s de tres dimensiones. El espacio se comprime, permitiendo "acercar" las distancias lejanas, y acortando por tanto el trayecto.<br/><br/>Estos propulsores se basan de las m�s �ltimas investigaciones sobre antimateria para producir la energ�a necesaria para el vuelo y proveen de velocidades muy superiores a las de el resto de motores.<br><br>Cada nivel investigado de esta tecnolog�a proporciona motores un 15% m�s r�pidos y eficaces.');

		case 111:
			return new Technology($ID,
			'Tecnolog�a l�ser',
			'La tecnolog�a del l�ser supone un avance en varios campos, tales como computaci�n, armament�stica, navegaci�n, etc.',
			'El l�ser es haz de luz monocrom�tico, coherente y definido. Posee m�ltiples usos, tales como la electr�nica y los dispositivos �pticos, armamento, medicina, investigaci�n, etc.<br/><br/>Como sistema defensivo, el l�ser supone una evoluci�n a los misiles bal�sticos, con una mayor precisi�n y poder destructivo. Tambi�n son la base necesaria para la investigaci�n de otras tecnolog�as como la i�ncia o el plasma.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Laser\')">Wikipedia</a>');


		case 112:
			return new Technology($ID,
			'Tecnolog�a i�nica',
			'La tecnolog�a i�nica es una evoluci�n de la l�ser, que permite lanzar rayos de iones cargados sobre un objetivo.',
			'Los rayos l�ser son poderosos, pero al basarse en luz no pueden causar tanto da�o como otros tipos de armas. La tecnolog�a i�nica se basa en muchos de los mismos principios que la l�ser, pero utiliza part�culas i�nicas cargadas en lugar de fotones de luz. Esto provoca mayor da�o sobre el objetivo que otras tecnolog�as, pero a�n as� no poseen poder suficiente contra grandes naves.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Ion\')">Wikipedia</a>');


		case 113:
			return new Technology($ID,
			'Tecnolog�a de plasma',
			'El uso del plasma como arma permite la creaci�n de sistemas armament�sticos con un poder destructivo muy elevado.',
			'El plasma es uno de los cuatro estados de la materia, el m�s abundante en el universo, y poseedor de una gran naturaleza agresiva. El plasma consiste en un n�mero igual de part�culas positivas y negativas, y se puede obtener a partir de gases a los que se les proporciona la energ�a suficiente para dividir sus �tomos en �tomos cargados positivamente y electrones cargados negativamente. Un ejemplo de plasma puede ser el fuego o un rayo de una tormenta.<br/><br/>El plasma como arma posee un elevado poder destructivo, muy superior al obtenido mediante l�seres o rayos de iones, sin embargo no es tan poderoso como una explosi�n de antimateria.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Plasma_%28estado_de_la_materia%29\')">Wikipedia</a>');


		case 114:
			return new Technology($ID,
			'Tecnolog�a de gravit�n',
			'Usando la part�cula del gravit�n se puede generar un campo gravitacional artificial con poder para destruir naves y lunas enteras.',
			'El gravit�n es un bos�n (una part�cula fundamental de la materia) que se encarga de las fuerzas gravitatorias que se dan entre dos masas. �l mismo es su antipart�cula; y carece de masa y carga.<br/><br/>Mediante el disparo de part�culas de gravit�n, se puede crear un campo gravitatorio artificial capaz de atraer y destruir naves y lunas completamente y en cuesti�n de segundos. La investigaci�n de esta tecnolog�a requiere de una cantidad de energ�a desproporcionada.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Gravit%C3%B3n\')">Wikipedia</a>');

		case 115:
			return new Technology($ID,
			'Red de investigaci�n intergal�ctica',
			'La red intergal�ctica utiliza todos los laboratorios de investigaci�n del imperio para investigar las nuevas tecnolog�as con mayor rapidez.',
			'La red intergal�ctica conlleva un gasto enorme para el imperio en su construcci�n, pues supone crear portales de comunicaci�n entre todos los laboratorios del imperio y herramientas de procesado de datos en paralelo. Una vez establecida la red los investigadores de todos los planetas pueden trabajar conjuntamente para investigar a velocidades m�s altas.<br/><br/>Al construir la red, los tiempos de investigaci�n de cada tecnolog�a ser�n la suma de todos los laboratorios enlazados a la red. La red s�lo se puede establecer una vez, una vez construida no se pueden ampliar su nivel.');

			//Ships

		case 299:
			return new Technology($ID,
			'Sat�lite solar',
			'Los sat�lites solares env�an energ�a a las plantas de microondas.',
			'Los sat�lites solares env�an energ�a desde el espacio a las plantas de microondas. Estos sat�lites son los encargados de capturar la energ�a proveniente del sol, mediante paneles fotovoltaicos, y enviarla al planeta en forma de microondas. Las plantas de microondas capturan la energ�a transmitida y la convierten en corriente el�ctrica �til para minas y edificios.<br /><br />Por desgracia, estos sat�lites no disponen de dispositivos defensivos, por lo que en caso de ataque son destruidos con relativa facilidad.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Energ%C3%ADa_solar\')">Wikipedia</a>');

		case 300:
			return new Technology($ID,
			'Nave peque�a de carga',
			'Las naves peque�as de carga son naves �giles y baratas, dise�adas para transportar recursos entre planetas.',
			'Las naves peque�as de carga son naves �giles, dotadas de un sistema de propulsi�n basado en el motor de combusti�n, que carecen de sistemas defensivos y armament. Estas naves est�n dise�adas exclusivamente para el transporte de recursos entre planetas lo m�s rapidamente posible. Las naves peque�as son el tipo de nave de transporte m�s b�sico que puede emplear un imperio, con una capacidad de carga de 20000 unidades.<br><br>Al investigar el nivel 7 del motor de impulso, �stas naves son adaptadas para poder utilizar ese motor en su sistema de propulsi�n, logrando una mayor velocidad de vuelo.');

		case 301:
			return new Technology($ID,
			'Nave grande de carga',
			'Las naves de carga grandes son la evoluci�n de las naves peque�as, permitiendo albergar m�s recursos y viajar a una mayor velocidad.',
			'Las naves de carga grandes poseen una capacidad de almacenaje de 100.000 unidades, 5 veces superior a las de sus antecesoras. A diferencia de las peque�as, las naves grandes de carga incorporan un moderno motor de impulso, lo que permite que puedan viajar a m�s velocidad con el mismo gasto de combustible. Al igual que su predecesora carece de sistemas defensivos, puesto que est� optimizada para poder transportar la mayor cantidad de recursos posibles en el m�nimo espacio.');

		case 302:
			return new Technology($ID,
			'Reciclador',
			'Los recicladores son naves de carga blindadas, preparadas para poder viajar entre campos de asteroides sin sufrir graves da�os.',
			'Tras un combate, las naves destruidas se quedan flotando en el espacio en forma de escombros y campos de asteroides. Debido a las fuerzas gravitatorias e inerciales, estos escombros giran en la �rbita del planeta, alcanzando velocidades cercanas a los 27000 km./h. Para cualquier tipo de nave acercarse a un campo de asteroides supondr�a su destrucci�n o la posibilidad de sufrir graves da�os estructurales.<br/><br/>Los recicladores est�n dise�ados para ser capaces de viajar a trav�s de estos campos de asteroides. La evoluci�n de las aleaciones de metales blindados permiti� la creaci�n de este tipo de naves a partir de naves de carga grandes. Debido a que necesitan un casco m�s resistente, la capacidad de carga de los recicladores es un poco inferior a la de las naves de carga.');

		case 303:
			return new Technology($ID,
			'Sonda de espionaje',
			'Las sondas de espionaje son peque�os droides no tripulados dotados de sensores de alta tecnolog�a para poder espiar los planetas enemigos.',
			'Las sondas de espionaje son peque�os droides inteligentes, encargados de recoger datos sobre un planeta enemigo y elaborar un informe de espionaje para los centros de inteligencia imperiales. Estas sondas est�n equipadas con la �ltima tecnolog�a en sensores de detecci�n y cuentan con un extraordinario sistema de propulsi�n que aprovecha las fuerzas gravitatorias para desplazarse y recorrer grandes distancias en poco tiempo.<br/><br/>Debido a su espec�fica misi�n de recogida de datos y a su peque�o tama�o, estas sondas carecen de cualquier tipo de blindaje o sistema defensivo. Si son detectadas por el enemigo, �ste puede destruirlas con relativa facilidad.');

		case 304:
			return new Technology($ID,
			'Colonizador',
			'Los colonizadores son naves dise�adas para explorar un planeta inhabitado y adaptarlo a las condiciones necesarias para la vida.',
			'Los colonizadores son naves encargadas de explorar un planeta a�n no habitado y de adaptarlo a las condiciones necesarias para la vida humana. Est�n dotados de los requisitos m�s b�sicos para los seres humanos, como pueden ser comida, agua y descanso. A su vez, transportan los materiales necesarios para establecer una base de operaciones que se encargue de la construcci�n de los edificios planetarios.');

		case 305:
			return new Technology($ID,
			'Cazador ligero',
			'Los cazadores ligeros son las naves de guerra m�s b�sicas que componen la flota de un imperio, con pocas armas y fr�gil escudo.',
			'Los cazadores ligeros son las naves m�s b�sicas de una flota. Normalmente luchan en grandes n�meros, a modo de barrera para naves m�s pesadas. Su precio es realmente bajo, as� como su armamento y escudo. Su mejor caracter�stica es su maniobrabilidad, que le permite esquivar disparos que otras naves no podr�an.<br/><br/>Los cazadores ligeros est�n equipados con un motor de combusti�n y lanzadores de misiles bal�sticos, s�lo eficaces contra otros cazadores ligeros o contra lanzamisiles.');

		case 306:
			return new Technology($ID,
			'Cazador pesado',
			'Los cazadores pesados son la evoluci�n del ligero, incorporando un motor m�s avanzado y mejora de los escudos y las armas.',
			'Los cazadores pesados surgieron ante la necesidad de un nuevo tipo de nave m�s poderosa y resistente que su predecesora, el cazador ligero. Dotados de un avanzado motor de impulso y de armas de tipo l�ser, el cazador pesado es un enemigo bastante m�s potente en cuanto a velocidad y armamento se refiere. Los avances en blindaje y escudos tambi�n fueron aplicadas a esta nave, haciendola m�s robusta y resistente para las batallas.');

		case 307:
			return new Technology($ID,
			'Crucero',
			'Los cruceros suponen un gran salto cualitativo en cuento a naves de combate se refiere, siendo tres veces m�s poderosas que sus antecesores.',
			'Los cruceros fueron durante largo tiempo las naves dominantes en el universo. Estas naves, dotadas de ca�ones l�seres e i�nicos, eran tremendamente efectivas contra los ya obsoletos cazadores. Su optimizado motor de impulso permite a �stas naves desplazarse con velocidades muy superiores a las de los cazadores, sin embargo, no son tan maniobrales como �stos, debido sobre todo a su mayor tama�o.<br/><br/>Los cruceros son naves de coste medio, sin embargo, han demostrado ser bastante resistentes contra naves peque�as y escurridizas.');

		case 308:
			return new Technology($ID,
			'Nave de batalla',
			'Las naves de batalla son la base de cualquier flota, naves bien equipadas y eficaces contra la mayor�a de naves pesadas y ligeras.',
			'Las naves de batalla son la espina dorsal de cualquier flota. Sin un coste excesivamente alto, y aplicando las m�s �ltimas tecnolog�as militares, se consigui� crear esta nave, una fusi�n entre naves ligeras y pesadas. Equipada con l�seres pesados, ca�ones i�nicos y ca�ones gauss, capaces de destruir cruceros e incluso bombarderos, as� como un escudo y blindaje altamente resistentes, hacen de esta nave un enemigo dif�cil de abatir.<br/><br/>La nave de batalla se desplaza mediante un modern�simo propulsor hiperespacial, que hace que la velocidad de vuelo de esta nave est� entre las m�s altas. Por si fuera poco, su espacio de carga permite almacenar grandes cantidades de combustible, �til para trayectos largos.');

		case 309:
			return new Technology($ID,
			'Bombardero',
			'Los bombarderos son naves dise�adas para destruir las defensas enemigas, equipados con bombas de plasma y de antimateria.',
			'La feroz defensa planetaria llevada a cabo por algunos imperios di� lugar a la investigaci�n y creaci�n de naves capaces de resistir y destruir estas defensas. As� surgi� el bombardero, una nave equipada con potentes bombas de plasma y antimateria, guiadas mediante l�ser de alta precisi�n. Es eficaz contra todos los sistemas defensivos, exceptuando los ca�ones de antimateria.<br><br>A pesar de su obsoleto sistema de propulsi�n basado en el motor de impulso, a partir del nivel 8 de la tecnolog�a de propulsi�n hiperespacial estas pesadas naves pueden ser equipadas con esos motores, consiguiendo as� aumentar su velocidad notablemente.');


		case 310:
			return new Technology($ID,
			'Destructor',
			'El destructor es una nave de combate muy avanzada, con un escudo y un poder de ataque superior a todos los vistos anteriormente.',
			'Los destructores son la evoluci�n de la nave de batalla. M�s grandes (casi dos kil�metros de extremo a extremo), m�s poderosos, m�s resistentes, y tambi�n, con un mayor gasto de combustible. Est�n equipados con ca�ones gauss y de plasma, altamente eficaces contra naves peque�as y r�pidas, como los cazadores ligeros.');

		case 311:
			return new Technology($ID,
			'Interceptor',
			'El interceptor es la nave de combate m�s avanzada, con un tama�o y una potencia de ataque que le hace eficaz contra objetivos pesados.',
			'La investigaci�n de la antimateria di� lugar a t�cnicas capaces de aumentar su poder destructivo. Combinando una nave robusta, grande y equipada con ca�ones de antimateria (m�s poderosos que los campos de gravitones) se cre� el Interceptor, una nave muy superior a los destructores y a cualquier otra nave de combate.<br/><br/>Con unas pocas de estas naves se pueden destruir estrellas de la muerte y destructores con relativa facilidad, sin embargo, son muy d�biles contra naves ligeras, debido a su escasa velocidad de disparo. Adem�s es una nave que consume mucho combustible, debido a que lo usa como proyectil; y su precio es tambien muy elevado.');

		case 312:
			return new Technology($ID,
			'Estrella de la muerte',
			'La estrella de la muerte es la culminaci�n tecnol�gica de un imperio, la nave m�s temida y poderosa jam�s creada.',
			'La estrella de la muerte es la culminaci�n tecnol�gica de un imperio. Esta estaci�n de combate est� equipada con poderosos ca�ones de gravitones, capaces de destruir cualquier nave o luna de un s�lo disparo. Su tama�o, casi como el de una luna, con un di�metro de 120km., le permite albergar m�s de un mill�n de tripulantes y 50 millones de unidades de recursos.<br/><br/>La estrella de la muerte posee en su propia estructura un acelerador de part�culas que le permite generar la antimateria necesaria para desplazarse y generar los campos de gravitones. Su velocidad de desplazamiento es su principal inconveniente, debido a su enorme tama�o s�lo puede desplazarse mediante un arcaico motor de impulso, pero al investigar el nivel 12 del propulsor hiperespacial se consigue acoplar en la estaci�n de combate para darle m�s velocidad.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Estrella_de_la_Muerte\')">Wikipedia</a>');

			//Defenses

		case 400:
			return new Technology($ID,
			'Lanzamisiles',
			'El sistema defensivo m�s b�sico, basado en misiles bal�sticos. Su precio, as� como su poder de ataque, es muy bajo.',
			'El lanzamisiles es el sistema defensivo m�s arcaico con el que cuenta un imperio. Basado en la ya obsoleta tecnolog�a de misiles bal�sticos, el lanzamisiles es eficaz contra objetivos ligeros, sobre todo si act�a en grandes n�meros.<br/><br/>El bajo coste de construcci�n, hace que el lanzamisiles sea una de las defensas m�s utilizadas del universo.');

		case 401:
			return new Technology($ID,
			'L�ser peque�o',
			'Los l�seres peque�os utilizan un rayo l�ser concentrado, que proporciona un poder de ataque mayor que los misiles bal�sticos.',
			'Aplicando la tecnolog�a l�ser al campo militar, se consigui� crear un arma m�s poderosa que el obsoleto lanzamisiles. El l�ser peque�o combina potencia, bajo coste y un bueno escudo, creando un arma digna de las defensas de cualquier imperio.');

		case 402:
			return new Technology($ID,
			'L�ser grande',
			'Los l�seres grandes proporcionan rayos l�seres m�s poderosos, as� como un mayor escudo y casco.',
			'La continua evoluci�n de la tecnolog�a, y su aplicaci�n al campo militar, permit�a la creaci�n de nuevas armas y naves m�s potentes. El l�ser grande es un claro ejemplo de ello: m�s potencia que los peque�os, m�s integridad f�sica, y un mejor escudo permitieron la creaci�n de esta nueva defensa m�s efectiva contra las nuevas amenazas que iban apareciendo en el universo, como los cruceros.');

		case 403:
			return new Technology($ID,
			'Ca��n i�nico',
			'Los ca�ones i�nicos disparan rayos de iones contra los objetivos, paralizando los escudos y equipos electr�nicos.',
			'Los ca�ones i�nicos se basan en los pulsos electromagn�ticos, un efecto f�sico capaz de destruir todos los equipamientos el�ctricos o electr�nicos dentro de su radio de acci�n. El avance de la tecnolog�a i�nica permiti� concentrar esos pulsos en rayos que pudieran ser dirigidos con precisi�n, creando as� el ca��n i�nico.<br/><br/>El ca��n i�nico posee un poder de ataque inferior al de los l�seres grandes, sin embargo es m�s efectivo contra naves como cruceros o naves de batalla.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Pulso_electromagn%C3%A9tico\')">Wikipedia</a>');


		case 404:
			return new Technology($ID,
			'Ca��n Gauss',
			'Los ca�ones gauss utilizan misiles como proyectiles, aceler�ndolos mediante una inmensa fuerza electromagn�tica.',
			'Los ca�ones gauss se basan en el funcionamiento de los aceleradores de part�culas para acelerar, mediante fuerzas electromagn�ticas, grandes proyectiles de varias toneladas de peso. Con esto se consigue que los proyectiles salgan disparados a varias decenas de miles de kil�metros por hora, provocando alrededor del ca��n una gran devastaci�n.<br/><br/>La velocidad a la que son disparados los proyectiles hacen que los escudos y blindajes poco puedan hacer para evitar la colisi�n, consiguiendo provocar as� graves da�os en la estructura de la nave.<br/><br/>M�s informaci�n en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Ca%C3%B1%C3%B3n_Gauss\');">Wikipedia</a>');

		case 405:
			return new Technology($ID,
			'Ca��n de plasma',
			'Los ca�ones de plasma combinan la tecnolog�a l�ser e i�nica para crear un rayo de plasma altamente peligroso.',
			'La combinaci�n de las dos tecnolog�as de defensa m�s usadas dio como resultado la creaci�n de un nuevo tipo de arma: el ca��n de plasma. Usando tecnolog�a l�ser para calentar las part�culas y la tecnolog�a i�nica para darles carga el�ctrica, se consigue crear un rayo de plasma altamente peligroso, capaz de atravesar escudos y blindajes con relativa facilidad.<br/><br/>Los ca�ones de plasma tienen un alto poder de ataque, y tambi�n un coste elevado.');

		case 406:
			return new Technology($ID,
			'Ca��n de antimateria',
			'Los ca�ones de antimateria es la culminaci�n tecnol�gica en materia defensiva, la defensa m�s mort�fera y eficaz.',
			'Desde su descubrimiento se conoc�a el potencial de la antimateria como arma, sin embargo, no hab�a los medios suficientes para su manejo. Al avanzar las investigaciones en este campo, los investigadores dieron con la f�rmula para poder usar la antimateria como proyectil sin el riesgo que supondr�a para un planeta entero el disparo de la antimateria desde su superficie. As� surgieron los ca�ones de antimateria, armas altamente mort�feras y eficaces contra cualquier tipo de nave.<br/><br/>El funcionamiento de los ca�ones de antimateria es sencillo: se almacenan grandes cantidades de este elemento en trampas adaptadas para que no entre en contacto con materia ordinaria, se aceleran mediante fuerzas electromagn�ticas estas trampas, y se disparan contra los objetivos. La fuerza a la que son disparadas estas trampas hacen que atraviesen con facilidad los escudos enemigos, haciendo explotar su carga contra el blindaje de la nave. La fatal explosi�n afecta a varios cientos de kil�metros a la redonda, destruyendo toda la materia que se encuentra en su radio de acci�n.<br/><br/>El coste de estos ca�ones es muy elevado, adem�s, cada disparo de un ca��n supone un coste de 25000 unidades de antimateria.');

		case 502:
			return new Technology($ID,
			'C�pula de protecci�n',
			'La c�pula de protecci�n genera un escudo alrededor del planeta que es capaz de absorver grandes cantidades de energ�a.',
			'La c�pula de protecci�n genera alrededor de la superficie planetaria un escudo capaz de absorver grandes cantidades de energ�a. �stos escudos son capaces de soportar ataques enemigos continuados, hasta que se colapsan y se deshacen.<br/><br/>Las c�pulas requieren de grandes cantidades de energ�a para poder crear los escudos, y en cada nivel aumentado, la demanda de energ�a es mayor.');

		case 500:
			return new Technology($ID,
			'Misil de intercepci�n',
			'Los misiles de intercepci�n se encargan de destruir en pleno vuelo los misiles interplanetarios lanzados contra el planeta.',
			'La continua amenaza que supon�a para un imperio el ser alcanzado por misiles interplanetarios provoc� la necesidad de dise�ar una manera eficaz de destruir los misiles antes de que alcanzasen su objetivo. As� surgieron los misiles de intercepci�n, capaces de detectar y destruir los misiles antes de que entren en la �rbita planetaria.');

		case 501:
			return new Technology($ID,
			'Misil interplanetario',
			'Los misiles interplanetarios se encargan de destruir los sistemas defensivos de un planeta de manera efectiva y a distancia.',
			'La idea de poder destruir las defensas enemigas sin necesidad de enviar naves de ataque di� lugar a la investigaci�n de misiles interplanetarios. Estos misiles pueden ser lanzados desde el silo y programados para alcanzar un planeta objetivo. Para ello cuentan con su propio sistema de propulsi�n, altamente eficaz, as� como de un alto poder destructivo contra las defensas.');
	}
}

?>