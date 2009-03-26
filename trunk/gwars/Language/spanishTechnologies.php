<?php
/*Technologies with ID between:

· 1 and 10 - are mines
· 10 and 20 - are energy producers
· 20 and 80 - are builds
· 80 and 90 - lunar builds
· 90 and 100 - are store
· 100 and 250 - are investigations
· 250 and 305 - are general ships
· 305 and 400 - are war ships
· 400 and 500 - are defense
· 500 and 600 - are others, like misils and cupula
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
			'El metal es el recurso básico para la construcción de eficios, naves y sistemas de defensa. Su recolección se realiza bajo la superficie terrestre, por lo tanto es un recurso barato y disponible. Sin embargo, cada vez se requiere de más energía y más coste económico para acceder a minas más profundas que permitan una recolección mas abundate de este material.');

		case 2:
			return new Technology($ID,
			'Mina de cristal',
			'Las minas de cristal proporcionan el recurso base para la construcción de circuitos y elementos electrónicos.',
			'El cristal es el elemento más básico de cualquier estructura electrónica, como los microprocesadores incorporados en las computadoras. Su uso por tanto es elevado, y es necesario para la construcción y la investigación de casi cualquier tecnología y nave. Al igual que el metal, el cristal también es un mineral, y debe ser obtenido en minas subterráneas. Sin embargo, una vez obtenido debe ser tratado químicamente para otorgarle las propiedades necesarias para su uso industrial, lo que implica un mayor gasto energético y económico que la producción metalúrgica.');

		case 3:
			return new Technology($ID,
			'Acelerador de partículas',
			'El acelerador de partículas es un instrumento que se encarga de acelerar partículas subatómicas hasta velocidades cercanas a la de la luz para producir Antimateria.',
			'Los aceleradores de partículas son enormes máquinas que mediante impulos magnéticos aceleran las partículas subatómicas hasta unos 290.000 kilómetros por segundo, y producen choques entre ellas. De esta colisión se produce antimateria, o lo que es lo mismo, materia compuesta de antipartículas de las partículas que constituyen la materia normal, como el antihidrógeno.<br /><br />
En las colisiones entre materia y antimateria, se convierte toda la masa posible de las partículas en energía. Esta forma de producir energía es extremadamente efectiva, con un rendimiento del 100%, pero a la vez es muy escasa, pues sólo se obtienen unos pocos nanogramos de estas colisiones.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Acelerador_de_part%C3%ADculas\')">Wikipedia</a>');

		case 10:
			return new Technology($ID,
			'Planta de fusión nuclear',
			'Las plantas de fusión producen energía mediante procesos atómicos de fusión.',
			'Las plantas de fusión son una fuente de energía limpia y económica, basadas en los procesos nucleares que ocurren en el interior de las estrellas que les permiten brillar y aportar calor. Proporcionan la energía eléctrica que requieren los edificios para su funcionamiento, como las minas, a partir de procesos de fusión nuclear, en el que dos átomos se juntan en uno sólo, desprendiendo de esta acción una gran cantidad de energía. En estas plantas se necesitan enormes campos magnéticos para poder soportar las elevadísimas temperaturas y presiones que se necesitan en el proceso.<br /><br />Las plantas de fusión dejan de ser rentables cuando la demanda de energía es extremadamente alta, para ello deben usarse formas de energía aún mas eficaces, como la antimateria.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Fusi%C3%B3n_nuclear\')">Wikipedia</a>');

		case 11:
			return new Technology($ID,
			'Planta de antimateria',
			'La planta de antimateria utiliza pequeñas cantidades de antimateria para producir energía eléctrica en grandes proporciones.',
			'La antimateria es el combustible más eficaz que existe, con un rendimiento del 100%. Con un sólo nanogramo de antimateria se podría abastecer la demanda energética de un planeta entero en un día.
<br /><br />Al entrar en contacto antimateria con la materia normal, ambas se aniquilan produciendo un estallido de energía pura, como un Big-Bang en pequeñas proporciones. Las plantas de antimaterian se encargan de mezclar ambos elementos y transformar la energía emitida del proceso en energía eléctrica.');

		case 12:
			return new Technology($ID,
			'Planta de microondas',
			'Las plantas de microondas producen energía a partir de radiaciones microondas emitidas por satélites espaciales.',
			'Las plantas de microondas se complementan con satélites solares colocados orbitando alrededor del planeta. Estos satélites son los encargados de capturar la energía proveniente del sol, mediante paneles fotovoltaicos, y enviarla al planeta en forma de microondas. Las plantas de microondas capturan la energía transmitida y la convierten en corriente eléctrica útil para minas y edificios.<br /><br />Por desgracia, estos satélites no disponen de dispositivos defensivos, por lo que en caso de ataque son destruidos con relativa facilidad.<br/><br/>Ver también <a onclick="Mostrar(\'descripcion.php?id=299\')">Satélite Solar</a><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Energ%C3%ADa_solar\')">Wikipedia</a>');

			// Militar builds
		case 20:
			return new Technology($ID,
			'Laboratorio de investigación',
			'En el laboratorio se investigan y descubren las nuevas tecnologías.',
			'El laboratorio es el lugar donde se reúnen las mentes más avanzadas para investigar las nuevas tecnologías que permitirán el avance del imperio. Cada nivel de ampliación supone una mayor inversión en el laboratorio, permitiendo así una mayor velocidad en la investigación, y la apertura de nuevos campos que investigar.');

		case 21:
			return new Technology($ID,
			'Fábrica de Robots',
			'Los robots ayudan en la construcción y ampliación de los edificios planetarios, disminuyendo el tiempo de construcción de los edificios.',
			'Desde sus orígenes el ser humano se ha servido de si mismo para realizar tareas y trabajos que requiriesen el uso de su inteligencia. Con la aparición de los primeros robots y autómatas, el ser humano se vió liberado de tareas tales como la construcción, la agricultura, la pesca, etc. Estas tareas eran ahora desempeñados por los robots, lo que permitió al ser humano centrarse en actividades tales como la investigación, en la que importaba el conocimiento y no la destreza manual.<br /><br/>Fue la aparición de los robots el hecho que propició el avance significativo de la raza humana durante el sigo XXII, así como la conquista del espacio.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Robot\')">Wikipedia</a>');


		case 22:
			return new Technology($ID,
			'Fábrica de Nanobots',
			'Los nanobots son el avance de los robots, mejorando su velocidad de construcción en edificios y naves.',
			'Los nanobots son el resultado de la fusión entre <a onclick="window.open(\'http://es.wikipedia.org/wiki/Robotica\')">robótica</a> y <a onclick="window.open(\'http://es.wikipedia.org/wiki/Nanotecnolog%C3%ADa\')">nanotecnología</a>. Estos dispositivos inteligentes con un tamaño similar al de células biológicas, son programados para trabajar conjuntamente en la construcción de edificios y naves.<br/><br/>Debido a su minúsculo tamaño, los nanobots operan en grandes redes, formadas por varios millones de unidades, que consiguen velocidades de construcción muy superior a las conseguidas mediante robots normales.');

		case 23:
			return new Technology($ID,
			'Hangar',
			'En el hangar se construyen y reparan las naves y estructuras de defensa planetaria.',
			'El hangar es el centro neurálgico del ejército de un planeta. Desde el hangar se construyen las naves y los sistemas defensivos de un planeta y se reparan las estructuras dañadas. Sin el hangar no se pueden construir dispositivos de defensa planetaria.<br /><br />Si se combina con los nanobots, el hangar puede construir naves y defensas a velocidades muy superiores a las normales.');

		case 24:
			return new Technology($ID,
			'Estación espacial',
			'La estación espacial permite a las flotas aliadas que estén en órbita repostar combustible.',
			'La estación espacial es una estructura artificial colocada en órbita alrededor del planeta que permite a las flotas aliadas respostar las naves para aumentar su tiempo de espera en la órbita planetaria. Cada nivel de mejora de la espación permite una ampliación de ésta y, por lo tanto, una mayor capacidad de repostaje para las naves aliadas.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Estaci%C3%B3n_espacial\')">Wikipedia</a>');


		case 25:
			return new Technology($ID,
			'Silo de misiles',
			'Desde el silo se pueden lanzar y almacenar misiles planetarios.',
			'Desde el silo se pueden construir, almacenar y lanzar misiles planetarios. Existen diferentes tipos de misiles, cada uno con su función específica.<br/><br/>Cada nivel del silo ampliación permite una mayor velocidad de construcción de misiles, así como un mayor espacio de almacionamiento de misiles.');
			//Lunar builds

		case 80:
			return new Technology($ID,
			'Base lunar',
			'Permite la construcción de estructuras en la superficie lunar.',
			'La base lunar aporta las condiciones necesarias para la vida biológica de un ser vivo en un satélite lunar. Aporta el oxígeno, la gravedad y la temperatura adecuadas para la vida, así como la energía eléctrica necesaria para el funcionamiento de las estructuras más básicas.');

		case 81:
			return new Technology($ID,
			'Sensor espacial',
			'Estos sensores son capaces de analizar un gran espacio para detectar flotas y movimientos de naves enemigas.',
			'Los sensores espaciales están dotados de las mismas tecnologías que las sondas de espionaje, y permiten analizar mediante radiofrecuencia el espacio alrededor del satelite. Estos sensores son capaces de percibir cualquier frecuencia proveniente de un planeta para detectar las naves y flotas enemigas en vuelo.<br><br>Cada nivel aumentado del sensor, o cada nivel aumentado de la tecnología de espionaje, permite que estos sensores alcancen un mayor espacio de rastreo.');


		case 82:
			return new Technology($ID,
			'Salto cuántico',
			'El salto cuántico realiza movimientos de flota de manera instantánea entre dos puntos distintos del espacio.',
			'Los portales de salto cuántico utilizan la más novedosa tecnología de hiperespacio para "comprimir" la distancia entre dos puntos del universo y hacer así el viaje entre ambos instántaneo. Con esta tecnología se consigue que cualquier flota, independientemente de su tamaño, pueda viajar entre portales en cuestión de décimas de segundo.<br><br>Estos portales necesitan la más alta tecnología y una gran inversión para su consrucción.');

			//Stores

		case 90:
			return new Technology($ID,
			'Almacén de metal',
			'Permite almacenar grandes cantidades de metal.',
			'El almacén de metal permite almacenar en sus bodegas grandes cantidades de éste material. Cada nivel de ampliación supone un mayor espacio disponible para almacenar este recurso.<br/><br/>Cuando los almacenes se llenan, la producción de metal se detiene hasta que haya espacio de nuevo disponible para su almacenaje.');

		case 91:
			return new Technology($ID,
			'Almacén de cristal',
			'Permite almacenar grandes cantidades de cristal.',
			'El almacén de cristal permite almacenar en sus bodegas grandes cantidades de éste material. Cada nivel de ampliación supone un mayor espacio disponible para almacenar este recurso.<br/><br/>Cuando los almacenes se llenan, la producción de cristal se detiene hasta que haya espacio de nuevo disponible para su almacenaje.');

		case 92:
			return new Technology($ID,
			'Trampa de antimateria',
			'Las trampas de antimateria permiten almacenar este recurso impidiendo que entre en contacto con la materia normal.',
			'La antimateria es un material peligroso de manejar. Simplemente entrando en contacto con el aire, produciría una explosión 100 veces superior a una explosión nuclear.<br/><br/>En las trampas de antimateria, se aísla a ésta completamente de cualquier forma de materia, mediante la combinación de campos magnéticos y eléctricos, reteniendo así las partículas en el centro del contenedor, suspendida en el vacío. Para poder realizar este proceso satisfactoriamente, se requieren grandes cantidades de energía.');

			//Investigations

		case 100:
			return new Technology($ID,
			'Tecnología de espionaje',
			'El espionaje permite obtener información sobre otros planetas e imperios.',
			'La tecnología avanza, y la forma de espiar con ella. La tecnología de espionaje investiga los sensores, las sondas y la inteligencia artificial para equipar naves con la misión de obtener la mayor información posible sobre un imperio, así como las herramientas para la defensa ante espionajes enemigos.<br/><br/>Cada nivel de esta tecnología proporciona sondas de espionaje más rápidas y con capacidad para obtener y procesar más información.');

		case 101:
			return new Technology($ID,
			'Tecnología de computación',
			'Investiga las mejoras en los microprocesadores y computadoras. Cada nivel proporciona mayor capacidad de envío de flota.',
			'La tecnología de computación se encarga de estudiar y mejorar el rendimiento y la velocidad de los microprocesadores usados en las computadoras.<br/><br/>El aumento de esta tecnología permite procesar más rápidamente la información sobre las flotas enviadas, y por tanto permite que un mayor número de éstas puedan viajar a la vez.');

		case 102:
			return new Technology($ID,
			'Tecnología militar',
			'La tecnología militar se encarga de aumentar la eficacia de los sistemas de armamento de naves y defensas.',
			'La tecnología militar es la encargada de mejorar los sistemas de armamento. Cada mejora de esta tecnología permite la creación de armas para naves y defensas más potentes y mortíferas.');

		case 103:
			return new Technology($ID,
			'Tecnología de defensa',
			'Esta tecnología incrementa la eficacia de los sistemas defensivos. Cada nivel de aumento produce mejores sistemas de defensa.',
			'Esta tecnología se encarga de justamente lo contrario que la militar: incrementar la eficacia de los sistemas defensivos. Esta tecnología investiga y mejora los escudos de energía usados en las naves para protegerse, permitiendo por cada nivel investigado escudos más densos y resistentes.');

		case 104:
			return new Technology($ID,
			'Tecnología de blindaje',
			'Investiga mejoras en los metales que forman las naves y las defensas, permitiendo estructuras más resistentes.',
			'La tecnología de blindaje investiga nuevas aleaciones de metal que sean más resistentes ante ataques y golpes. Esto permite naves y defensas con más defensa física, cuando no disponen de escudo protector. Cada nivel investigado produce naves y defensas mas resistentes.');

		case 105:
			return new Technology($ID,
			'Tecnología de energía',
			'Esta tecnología investiga nuevas formas de aprovechar la energía, y descubre otras fuentes de energía nuevas.',
			'La energía es una necesidad básica en cualquier imperio, y por tanto, es una de las tecnologías más avanzadas. La tecnología de energía investiga sobre nuevas formas de aprovechar las fuentes de energía conocidas, permitiendo aumentos de su rendimiento; así como también se encarga del descubrimiento de nuevas formas de energía más rentables.<br/><br/>Cada nivel que se aumenta esta tecnología permite un aumento de la producción energética en 1% en plantas y satélites solares; y la disminución en un 1% del consumo en naves.');

		case 106:
			return new Technology($ID,
			'Tecnología de antimateria',
			'Investiga el uso de antimateria como forma de obtención de energía y como arma de destrucción masiva.',
			'La antimateria es posiblemente la mejor forma de energía del universo. Sin embargo, su manejo es complicado, puesto que al entrar en contacto con materia normal ambas se aniquilan y producen una explosión de energía devastadora. Su coste de producción también es muy elevado, lo que hace que sea un recurso escaso.<br/><br/>La antimateria se emplea como fuente de energía en plantas de antimateria y también como sistemas de armamento para naves y defensas como el cañon de antimateria o el interceptor.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Antimateria\')">Wikipedia</a>');

		case 107:
			return new Technology($ID,
			'Tecnología de hiperespacio',
			'Esta tecnología logra curvar la dimensión espacio-tiempo, permitiendo así viajes a velocidades mayores que las de la luz.',
			'El hiperespacio es la zona del universo que posee más de tres dimensiones. Para poder acceder a él, es necesario curvar el espacio-tiempo mediante grandes fuerzas gravitacionales, permitiendo así que dos distancias lejanas se acercasen. Este método también permitiría poder superar la barrera de la luz en velocidad de viaje, lo que supone una considerable disminución de los tiempos de vuelo entre planetas.<br/><br/>Esta tecnología es la base para la creación de motores que aprovechen el hiperespacio para desplazarse.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Hiperespacio_%28geometr%C3%ADa%29\')">Wikipedia</a>');

		case 108:
			return new Technology($ID,
			'Motor de combustión',
			'El motor de combustión es el motor más rudimentario usado para viajes espaciales. Cada nivel investigado produce motores más eficientes.',
			'El motor de combustión se basa en las leyes físicas de acción y reacción. Las partículas son aceleradas en su interior y expulsadas hacia fuera, creando una fuerza de repulsión que mueve la nave. Estos motores permiten velocidades de viaje bajas, pero son económicos, fiables y consumen poca energía.<br/><br/>Cada nivel que se investiga de esta tecnología permite la producción de motores de combustión un 5% más eficaces y rápidos.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Motor_de_combusti%C3%B3n\')">Wikipedia</a>');

		case 109:
			return new Technology($ID,
			'Motor de impulso',
			'El motor de impulso es una evolución del motor de combustión, usando antimateria como forma de energía para desplazarse.',
			'Los motores de impulso son la evolución del motor de combustión tradicional. Utilizan antimateria para desplazar las partículas, lo que permiten que las naves se desplacen a velocidades mayores con un menor gasto de energía.<br/><br/>Cada nivel que se investiga de esta tecnología permite la producción de motores de impulso un 10% más eficaces y rápidos.');

		case 110:
			return new Technology($ID,
			'Propulsor hiperespacial',
			'Los propulsores hiperespaciales se basan en la teoría del hiperespacio para desplazar cuerpos a velocidades mayores que la de la luz.',
			'El propulsor hiperespacial utiliza la tecnología del hiperespacio para curvar el espacio-tiempo y entrar en un entorno de más de tres dimensiones. El espacio se comprime, permitiendo "acercar" las distancias lejanas, y acortando por tanto el trayecto.<br/><br/>Estos propulsores se basan de las más últimas investigaciones sobre antimateria para producir la energía necesaria para el vuelo y proveen de velocidades muy superiores a las de el resto de motores.<br><br>Cada nivel investigado de esta tecnología proporciona motores un 15% más rápidos y eficaces.');

		case 111:
			return new Technology($ID,
			'Tecnología láser',
			'La tecnología del láser supone un avance en varios campos, tales como computación, armamentística, navegación, etc.',
			'El láser es haz de luz monocromático, coherente y definido. Posee múltiples usos, tales como la electrónica y los dispositivos ópticos, armamento, medicina, investigación, etc.<br/><br/>Como sistema defensivo, el láser supone una evolución a los misiles balísticos, con una mayor precisión y poder destructivo. También son la base necesaria para la investigación de otras tecnologías como la ióncia o el plasma.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Laser\')">Wikipedia</a>');


		case 112:
			return new Technology($ID,
			'Tecnología iónica',
			'La tecnología iónica es una evolución de la láser, que permite lanzar rayos de iones cargados sobre un objetivo.',
			'Los rayos láser son poderosos, pero al basarse en luz no pueden causar tanto daño como otros tipos de armas. La tecnología iónica se basa en muchos de los mismos principios que la láser, pero utiliza partículas iónicas cargadas en lugar de fotones de luz. Esto provoca mayor daño sobre el objetivo que otras tecnologías, pero aún así no poseen poder suficiente contra grandes naves.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Ion\')">Wikipedia</a>');


		case 113:
			return new Technology($ID,
			'Tecnología de plasma',
			'El uso del plasma como arma permite la creación de sistemas armamentísticos con un poder destructivo muy elevado.',
			'El plasma es uno de los cuatro estados de la materia, el más abundante en el universo, y poseedor de una gran naturaleza agresiva. El plasma consiste en un número igual de partículas positivas y negativas, y se puede obtener a partir de gases a los que se les proporciona la energía suficiente para dividir sus átomos en átomos cargados positivamente y electrones cargados negativamente. Un ejemplo de plasma puede ser el fuego o un rayo de una tormenta.<br/><br/>El plasma como arma posee un elevado poder destructivo, muy superior al obtenido mediante láseres o rayos de iones, sin embargo no es tan poderoso como una explosión de antimateria.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Plasma_%28estado_de_la_materia%29\')">Wikipedia</a>');


		case 114:
			return new Technology($ID,
			'Tecnología de gravitón',
			'Usando la partícula del gravitón se puede generar un campo gravitacional artificial con poder para destruir naves y lunas enteras.',
			'El gravitón es un bosón (una partícula fundamental de la materia) que se encarga de las fuerzas gravitatorias que se dan entre dos masas. Él mismo es su antipartícula; y carece de masa y carga.<br/><br/>Mediante el disparo de partículas de gravitón, se puede crear un campo gravitatorio artificial capaz de atraer y destruir naves y lunas completamente y en cuestión de segundos. La investigación de esta tecnología requiere de una cantidad de energía desproporcionada.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Gravit%C3%B3n\')">Wikipedia</a>');

		case 115:
			return new Technology($ID,
			'Red de investigación intergaláctica',
			'La red intergaláctica utiliza todos los laboratorios de investigación del imperio para investigar las nuevas tecnologías con mayor rapidez.',
			'La red intergaláctica conlleva un gasto enorme para el imperio en su construcción, pues supone crear portales de comunicación entre todos los laboratorios del imperio y herramientas de procesado de datos en paralelo. Una vez establecida la red los investigadores de todos los planetas pueden trabajar conjuntamente para investigar a velocidades más altas.<br/><br/>Al construir la red, los tiempos de investigación de cada tecnología serán la suma de todos los laboratorios enlazados a la red. La red sólo se puede establecer una vez, una vez construida no se pueden ampliar su nivel.');

			//Ships

		case 299:
			return new Technology($ID,
			'Satélite solar',
			'Los satélites solares envían energía a las plantas de microondas.',
			'Los satélites solares envían energía desde el espacio a las plantas de microondas. Estos satélites son los encargados de capturar la energía proveniente del sol, mediante paneles fotovoltaicos, y enviarla al planeta en forma de microondas. Las plantas de microondas capturan la energía transmitida y la convierten en corriente eléctrica útil para minas y edificios.<br /><br />Por desgracia, estos satélites no disponen de dispositivos defensivos, por lo que en caso de ataque son destruidos con relativa facilidad.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Energ%C3%ADa_solar\')">Wikipedia</a>');

		case 300:
			return new Technology($ID,
			'Nave pequeña de carga',
			'Las naves pequeñas de carga son naves ágiles y baratas, diseñadas para transportar recursos entre planetas.',
			'Las naves pequeñas de carga son naves ágiles, dotadas de un sistema de propulsión basado en el motor de combustión, que carecen de sistemas defensivos y armament. Estas naves están diseñadas exclusivamente para el transporte de recursos entre planetas lo más rapidamente posible. Las naves pequeñas son el tipo de nave de transporte más básico que puede emplear un imperio, con una capacidad de carga de 20000 unidades.<br><br>Al investigar el nivel 7 del motor de impulso, éstas naves son adaptadas para poder utilizar ese motor en su sistema de propulsión, logrando una mayor velocidad de vuelo.');

		case 301:
			return new Technology($ID,
			'Nave grande de carga',
			'Las naves de carga grandes son la evolución de las naves pequeñas, permitiendo albergar más recursos y viajar a una mayor velocidad.',
			'Las naves de carga grandes poseen una capacidad de almacenaje de 100.000 unidades, 5 veces superior a las de sus antecesoras. A diferencia de las pequeñas, las naves grandes de carga incorporan un moderno motor de impulso, lo que permite que puedan viajar a más velocidad con el mismo gasto de combustible. Al igual que su predecesora carece de sistemas defensivos, puesto que está optimizada para poder transportar la mayor cantidad de recursos posibles en el mínimo espacio.');

		case 302:
			return new Technology($ID,
			'Reciclador',
			'Los recicladores son naves de carga blindadas, preparadas para poder viajar entre campos de asteroides sin sufrir graves daños.',
			'Tras un combate, las naves destruidas se quedan flotando en el espacio en forma de escombros y campos de asteroides. Debido a las fuerzas gravitatorias e inerciales, estos escombros giran en la órbita del planeta, alcanzando velocidades cercanas a los 27000 km./h. Para cualquier tipo de nave acercarse a un campo de asteroides supondría su destrucción o la posibilidad de sufrir graves daños estructurales.<br/><br/>Los recicladores están diseñados para ser capaces de viajar a través de estos campos de asteroides. La evolución de las aleaciones de metales blindados permitió la creación de este tipo de naves a partir de naves de carga grandes. Debido a que necesitan un casco más resistente, la capacidad de carga de los recicladores es un poco inferior a la de las naves de carga.');

		case 303:
			return new Technology($ID,
			'Sonda de espionaje',
			'Las sondas de espionaje son pequeños droides no tripulados dotados de sensores de alta tecnología para poder espiar los planetas enemigos.',
			'Las sondas de espionaje son pequeños droides inteligentes, encargados de recoger datos sobre un planeta enemigo y elaborar un informe de espionaje para los centros de inteligencia imperiales. Estas sondas están equipadas con la última tecnología en sensores de detección y cuentan con un extraordinario sistema de propulsión que aprovecha las fuerzas gravitatorias para desplazarse y recorrer grandes distancias en poco tiempo.<br/><br/>Debido a su específica misión de recogida de datos y a su pequeño tamaño, estas sondas carecen de cualquier tipo de blindaje o sistema defensivo. Si son detectadas por el enemigo, éste puede destruirlas con relativa facilidad.');

		case 304:
			return new Technology($ID,
			'Colonizador',
			'Los colonizadores son naves diseñadas para explorar un planeta inhabitado y adaptarlo a las condiciones necesarias para la vida.',
			'Los colonizadores son naves encargadas de explorar un planeta aún no habitado y de adaptarlo a las condiciones necesarias para la vida humana. Están dotados de los requisitos más básicos para los seres humanos, como pueden ser comida, agua y descanso. A su vez, transportan los materiales necesarios para establecer una base de operaciones que se encargue de la construcción de los edificios planetarios.');

		case 305:
			return new Technology($ID,
			'Cazador ligero',
			'Los cazadores ligeros son las naves de guerra más básicas que componen la flota de un imperio, con pocas armas y frágil escudo.',
			'Los cazadores ligeros son las naves más básicas de una flota. Normalmente luchan en grandes números, a modo de barrera para naves más pesadas. Su precio es realmente bajo, así como su armamento y escudo. Su mejor característica es su maniobrabilidad, que le permite esquivar disparos que otras naves no podrían.<br/><br/>Los cazadores ligeros están equipados con un motor de combustión y lanzadores de misiles balísticos, sólo eficaces contra otros cazadores ligeros o contra lanzamisiles.');

		case 306:
			return new Technology($ID,
			'Cazador pesado',
			'Los cazadores pesados son la evolución del ligero, incorporando un motor más avanzado y mejora de los escudos y las armas.',
			'Los cazadores pesados surgieron ante la necesidad de un nuevo tipo de nave más poderosa y resistente que su predecesora, el cazador ligero. Dotados de un avanzado motor de impulso y de armas de tipo láser, el cazador pesado es un enemigo bastante más potente en cuanto a velocidad y armamento se refiere. Los avances en blindaje y escudos también fueron aplicadas a esta nave, haciendola más robusta y resistente para las batallas.');

		case 307:
			return new Technology($ID,
			'Crucero',
			'Los cruceros suponen un gran salto cualitativo en cuento a naves de combate se refiere, siendo tres veces más poderosas que sus antecesores.',
			'Los cruceros fueron durante largo tiempo las naves dominantes en el universo. Estas naves, dotadas de cañones láseres e iónicos, eran tremendamente efectivas contra los ya obsoletos cazadores. Su optimizado motor de impulso permite a éstas naves desplazarse con velocidades muy superiores a las de los cazadores, sin embargo, no son tan maniobrales como éstos, debido sobre todo a su mayor tamaño.<br/><br/>Los cruceros son naves de coste medio, sin embargo, han demostrado ser bastante resistentes contra naves pequeñas y escurridizas.');

		case 308:
			return new Technology($ID,
			'Nave de batalla',
			'Las naves de batalla son la base de cualquier flota, naves bien equipadas y eficaces contra la mayoría de naves pesadas y ligeras.',
			'Las naves de batalla son la espina dorsal de cualquier flota. Sin un coste excesivamente alto, y aplicando las más últimas tecnologías militares, se consiguió crear esta nave, una fusión entre naves ligeras y pesadas. Equipada con láseres pesados, cañones iónicos y cañones gauss, capaces de destruir cruceros e incluso bombarderos, así como un escudo y blindaje altamente resistentes, hacen de esta nave un enemigo difícil de abatir.<br/><br/>La nave de batalla se desplaza mediante un modernísimo propulsor hiperespacial, que hace que la velocidad de vuelo de esta nave esté entre las más altas. Por si fuera poco, su espacio de carga permite almacenar grandes cantidades de combustible, útil para trayectos largos.');

		case 309:
			return new Technology($ID,
			'Bombardero',
			'Los bombarderos son naves diseñadas para destruir las defensas enemigas, equipados con bombas de plasma y de antimateria.',
			'La feroz defensa planetaria llevada a cabo por algunos imperios dió lugar a la investigación y creación de naves capaces de resistir y destruir estas defensas. Así surgió el bombardero, una nave equipada con potentes bombas de plasma y antimateria, guiadas mediante láser de alta precisión. Es eficaz contra todos los sistemas defensivos, exceptuando los cañones de antimateria.<br><br>A pesar de su obsoleto sistema de propulsión basado en el motor de impulso, a partir del nivel 8 de la tecnología de propulsión hiperespacial estas pesadas naves pueden ser equipadas con esos motores, consiguiendo así aumentar su velocidad notablemente.');


		case 310:
			return new Technology($ID,
			'Destructor',
			'El destructor es una nave de combate muy avanzada, con un escudo y un poder de ataque superior a todos los vistos anteriormente.',
			'Los destructores son la evolución de la nave de batalla. Más grandes (casi dos kilómetros de extremo a extremo), más poderosos, más resistentes, y también, con un mayor gasto de combustible. Están equipados con cañones gauss y de plasma, altamente eficaces contra naves pequeñas y rápidas, como los cazadores ligeros.');

		case 311:
			return new Technology($ID,
			'Interceptor',
			'El interceptor es la nave de combate más avanzada, con un tamaño y una potencia de ataque que le hace eficaz contra objetivos pesados.',
			'La investigación de la antimateria dió lugar a técnicas capaces de aumentar su poder destructivo. Combinando una nave robusta, grande y equipada con cañones de antimateria (más poderosos que los campos de gravitones) se creó el Interceptor, una nave muy superior a los destructores y a cualquier otra nave de combate.<br/><br/>Con unas pocas de estas naves se pueden destruir estrellas de la muerte y destructores con relativa facilidad, sin embargo, son muy débiles contra naves ligeras, debido a su escasa velocidad de disparo. Además es una nave que consume mucho combustible, debido a que lo usa como proyectil; y su precio es tambien muy elevado.');

		case 312:
			return new Technology($ID,
			'Estrella de la muerte',
			'La estrella de la muerte es la culminación tecnológica de un imperio, la nave más temida y poderosa jamás creada.',
			'La estrella de la muerte es la culminación tecnológica de un imperio. Esta estación de combate está equipada con poderosos cañones de gravitones, capaces de destruir cualquier nave o luna de un sólo disparo. Su tamaño, casi como el de una luna, con un diámetro de 120km., le permite albergar más de un millón de tripulantes y 50 millones de unidades de recursos.<br/><br/>La estrella de la muerte posee en su propia estructura un acelerador de partículas que le permite generar la antimateria necesaria para desplazarse y generar los campos de gravitones. Su velocidad de desplazamiento es su principal inconveniente, debido a su enorme tamaño sólo puede desplazarse mediante un arcaico motor de impulso, pero al investigar el nivel 12 del propulsor hiperespacial se consigue acoplar en la estación de combate para darle más velocidad.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Estrella_de_la_Muerte\')">Wikipedia</a>');

			//Defenses

		case 400:
			return new Technology($ID,
			'Lanzamisiles',
			'El sistema defensivo más básico, basado en misiles balísticos. Su precio, así como su poder de ataque, es muy bajo.',
			'El lanzamisiles es el sistema defensivo más arcaico con el que cuenta un imperio. Basado en la ya obsoleta tecnología de misiles balísticos, el lanzamisiles es eficaz contra objetivos ligeros, sobre todo si actúa en grandes números.<br/><br/>El bajo coste de construcción, hace que el lanzamisiles sea una de las defensas más utilizadas del universo.');

		case 401:
			return new Technology($ID,
			'Láser pequeño',
			'Los láseres pequeños utilizan un rayo láser concentrado, que proporciona un poder de ataque mayor que los misiles balísticos.',
			'Aplicando la tecnología láser al campo militar, se consiguió crear un arma más poderosa que el obsoleto lanzamisiles. El láser pequeño combina potencia, bajo coste y un bueno escudo, creando un arma digna de las defensas de cualquier imperio.');

		case 402:
			return new Technology($ID,
			'Láser grande',
			'Los láseres grandes proporcionan rayos láseres más poderosos, así como un mayor escudo y casco.',
			'La continua evolución de la tecnología, y su aplicación al campo militar, permitía la creación de nuevas armas y naves más potentes. El láser grande es un claro ejemplo de ello: más potencia que los pequeños, más integridad física, y un mejor escudo permitieron la creación de esta nueva defensa más efectiva contra las nuevas amenazas que iban apareciendo en el universo, como los cruceros.');

		case 403:
			return new Technology($ID,
			'Cañón iónico',
			'Los cañones iónicos disparan rayos de iones contra los objetivos, paralizando los escudos y equipos electrónicos.',
			'Los cañones iónicos se basan en los pulsos electromagnéticos, un efecto físico capaz de destruir todos los equipamientos eléctricos o electrónicos dentro de su radio de acción. El avance de la tecnología iónica permitió concentrar esos pulsos en rayos que pudieran ser dirigidos con precisión, creando así el cañón iónico.<br/><br/>El cañón iónico posee un poder de ataque inferior al de los láseres grandes, sin embargo es más efectivo contra naves como cruceros o naves de batalla.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Pulso_electromagn%C3%A9tico\')">Wikipedia</a>');


		case 404:
			return new Technology($ID,
			'Cañón Gauss',
			'Los cañones gauss utilizan misiles como proyectiles, acelerándolos mediante una inmensa fuerza electromagnética.',
			'Los cañones gauss se basan en el funcionamiento de los aceleradores de partículas para acelerar, mediante fuerzas electromagnéticas, grandes proyectiles de varias toneladas de peso. Con esto se consigue que los proyectiles salgan disparados a varias decenas de miles de kilómetros por hora, provocando alrededor del cañón una gran devastación.<br/><br/>La velocidad a la que son disparados los proyectiles hacen que los escudos y blindajes poco puedan hacer para evitar la colisión, consiguiendo provocar así graves daños en la estructura de la nave.<br/><br/>Más información en la <a onclick="window.open(\'http://es.wikipedia.org/wiki/Ca%C3%B1%C3%B3n_Gauss\');">Wikipedia</a>');

		case 405:
			return new Technology($ID,
			'Cañón de plasma',
			'Los cañones de plasma combinan la tecnología láser e iónica para crear un rayo de plasma altamente peligroso.',
			'La combinación de las dos tecnologías de defensa más usadas dio como resultado la creación de un nuevo tipo de arma: el cañón de plasma. Usando tecnología láser para calentar las partículas y la tecnología iónica para darles carga eléctrica, se consigue crear un rayo de plasma altamente peligroso, capaz de atravesar escudos y blindajes con relativa facilidad.<br/><br/>Los cañones de plasma tienen un alto poder de ataque, y también un coste elevado.');

		case 406:
			return new Technology($ID,
			'Cañón de antimateria',
			'Los cañones de antimateria es la culminación tecnológica en materia defensiva, la defensa más mortífera y eficaz.',
			'Desde su descubrimiento se conocía el potencial de la antimateria como arma, sin embargo, no había los medios suficientes para su manejo. Al avanzar las investigaciones en este campo, los investigadores dieron con la fórmula para poder usar la antimateria como proyectil sin el riesgo que supondría para un planeta entero el disparo de la antimateria desde su superficie. Así surgieron los cañones de antimateria, armas altamente mortíferas y eficaces contra cualquier tipo de nave.<br/><br/>El funcionamiento de los cañones de antimateria es sencillo: se almacenan grandes cantidades de este elemento en trampas adaptadas para que no entre en contacto con materia ordinaria, se aceleran mediante fuerzas electromagnéticas estas trampas, y se disparan contra los objetivos. La fuerza a la que son disparadas estas trampas hacen que atraviesen con facilidad los escudos enemigos, haciendo explotar su carga contra el blindaje de la nave. La fatal explosión afecta a varios cientos de kilómetros a la redonda, destruyendo toda la materia que se encuentra en su radio de acción.<br/><br/>El coste de estos cañones es muy elevado, además, cada disparo de un cañón supone un coste de 25000 unidades de antimateria.');

		case 502:
			return new Technology($ID,
			'Cúpula de protección',
			'La cúpula de protección genera un escudo alrededor del planeta que es capaz de absorver grandes cantidades de energía.',
			'La cúpula de protección genera alrededor de la superficie planetaria un escudo capaz de absorver grandes cantidades de energía. Éstos escudos son capaces de soportar ataques enemigos continuados, hasta que se colapsan y se deshacen.<br/><br/>Las cúpulas requieren de grandes cantidades de energía para poder crear los escudos, y en cada nivel aumentado, la demanda de energía es mayor.');

		case 500:
			return new Technology($ID,
			'Misil de intercepción',
			'Los misiles de intercepción se encargan de destruir en pleno vuelo los misiles interplanetarios lanzados contra el planeta.',
			'La continua amenaza que suponía para un imperio el ser alcanzado por misiles interplanetarios provocó la necesidad de diseñar una manera eficaz de destruir los misiles antes de que alcanzasen su objetivo. Así surgieron los misiles de intercepción, capaces de detectar y destruir los misiles antes de que entren en la órbita planetaria.');

		case 501:
			return new Technology($ID,
			'Misil interplanetario',
			'Los misiles interplanetarios se encargan de destruir los sistemas defensivos de un planeta de manera efectiva y a distancia.',
			'La idea de poder destruir las defensas enemigas sin necesidad de enviar naves de ataque dió lugar a la investigación de misiles interplanetarios. Estos misiles pueden ser lanzados desde el silo y programados para alcanzar un planeta objetivo. Para ello cuentan con su propio sistema de propulsión, altamente eficaz, así como de un alto poder destructivo contra las defensas.');
	}
}

?>