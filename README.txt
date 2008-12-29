
                                   _                                                        
 _ __   _ __  ___  (_)  ___   ___ | |_    __  __ _ __    ___ __   __ __ _     ___  ___       
| '_ \ | '__|/ _ \ | | / _ \ / __|| __|   \ \/ /| '_ \  / _ \\ \ / // _` |   / _ \/ __|      
| |_) || |  | (_) || ||  __/| (__ | |_  _  >  < | | | || (_) |\ V /| (_| | _|  __/\__ \      
| .__/ |_|   \___/_/ | \___| \___| \__|(_)/_/\_\|_| |_| \___/  \_/  \__,_|(_)\___||___/      
|_|              |__/                                                                        


LICENCE
=======
G.N.U. G.P.L.
(Please read LICENCE.txt for more informations)


INFORMATIONS
============
OGame Script based on UGamela and xnova france
Developed by Project.XNova.es Team

LINK
====
Site:    http://www.xnova.es/
Forum:   http://project.xnova.es
SVN:     http://svn.assembla.com/svn/projectxnovaes


=======
ESPAÑOL
=======


PRESENTACIÓN
============
XNova es un proyecto de servidor web(servidor web?), juego libre y gratuito para servidores multiplataforma (Windows, Linux, Mac, etc...).
Este servidor es instalable en servidores Apache. El cliente deberá de usar un navegador web (Internet Explorer, Mozilla Firefox, Netscape, etc...). 
Es un juego de guerra en el espacio y es sustancialmente similar Starcraft (Blizzard Entertainment).
Su objetivo es:
	Construcción de edificios (la eficacia de cada uno es determinada por su nivel construido).
	Gracias a los recursos, de los materiales y la energía creada (dependiendo de los edificios construidos). 
	Con los recursos se pueden construir naves de combate y defensas para el planeta. 
	Atacar a otro jugador o defenderse.
	La batalla entre jugadores, dan o quitan puntos entre los implicados. Con este fin, El juego depende de la estrategia en la utilización de sus recursos, energías, la evolución de los edificios, tecnologías y la organización de las flotas.

En suma, XNova es un juego MMOSTR lo que significa principalmente que los jugadores interactúan entre ellos. Un nuevo universo virtual esta abierto al descubrimiento de cada jugador!

Para instalar XNova, es necesario:
    * Un servidor Apache con PHP 4.x (mínimo).
    * Opción de correo activo (recomendado).
    * Una base de datos MySQL (o Postgre).
    * Poder cambiar los derechos de acceso a los ficheros del servidor (posibilidad de cambiar el CHMOD).

XNova utiliza cuatro lenguajes de programación web:
    * PHP
    * Java script
    * SQL
    * Ajax


Distribución
============
Antes que nada, es necesario entender que XNova no es un repack (es decir, que no es una mezcla de códigos encontrados y recopilados). El equipo tiene el objeto de completar y reforzar el núcleo del juego para crear un nuevo núcleo.
Los miembros del equipo XNova trabajan coordinándose en un SVN a fin de facilitar los cambios y modificaciones del código interno. A causa del aumento del juegos y cambios de código que ya se comienza a distribuir por internet.
Por lo tanto. Las actualizaciones son diarias, en forma de archivos comprimidos que se publican regularmente, en espacial en el Foro. Es importante que sobre todo XNova este centralizado en el Foro oficial.
Si tiene cualquier pregunta o sugerencia, por favor postéela. Si es para mí, para ser informando de un bug o proponer una solución para resolverlo.


Funcionamiento
==============
Diferencias de las carpetas que componen le juego. Funciones de cada una.
    * Admin (ficheros PHP): código fuente para las funciones del administrador.
    * CSS (ficheros CSS): contiene las bases de las estructuras de los style.
    * DB (ficheros PHP): permite la utilización de consultas SQL de su base de datos.
    * Images (ficheros GIF, PNG, JPG): imágenes base, smileys, background de las páginas de conexión.
    * Includes (ficheros PHP): contienen diversas variables incluidas dentro de las funciones de los códigos.
    * Install (ficheros PHP): permite la instalación y configuración del fichero config.php y su base de datos.
    * Language (ficheros MO): todos los textos o comentarios del juego, para facilitar su traducción.
    * Scripts (ficheros JS): ficheros creados con acciones java script.
    * Skins (ficheros CSS, GIF, JPG, PNG): contiene el diseño que permite a los jugadores interactuar visualmente en las páginas (imágenes, colores de las letras, etc...).
    * Templates (ficheros TPL): estructura de las páginas, programadas en HTML.
    * En la raíz (ficheros PHP): códigos del servidor (corazón del juego), permite la interacción entre las funciones, las variables, la base de datos, las acciones java script, los template, el skin y los ficheros de texto.

Es necesario determinar algunos puntos:
    * Los archivos de la raíz y la posibilidad del administrador para la utilización de cada template así como los textos de cada idioma, estos ficheros están sin texto o código HTML!
    * Es posible la traducción del contenido de XNova copiando directorio es, o renombrándolo por las siglas del país, y traducir los ficheros con extensión MO que contiene.
    * Solo el fichero config.php contiene los datos de su base de datos.


Servicios
=========
Antes que nada, el equipo de XNova proporciona un servicio gratuito, también decir que los creadores no tienen la obligación de atenderles. 
Cualquier soporte se realizara mediante el Foro y con sus reglas.
El contenido de XNova, el equipo hace lo posible para proporcionar actualizaciones regularmente y adjunta ficheros comprimidos para resolver los posibles problemas.
A demás de corregir los posibles problemas, intentan innovar en términos del juego. De hecho, de vez en cuando, se crean nuevos edificios, naves, tecnologías, etc...), y las nuevas acciones (nuevos oficiales, colonizaciones, etc...).
XNova seguira existiendo hasta que los errores sean corregidos y la imaginación de los programadores no de más. Esto significa que quedan al menos un par de años antes de que XNova quede perfecto.


Obligaciones
============
XNova está bajo la licencia GNU GPL (GNU General Public License), esto implica:
    * A la libertad de prestación de uso para la creación XNova crea un servidor.
    * A la libertad de estudiar su funcionamiento y adaptarlo a sus necesidades.
    * A la libertad de redistribuir las copias (no modificadas para su utilización), indicando el lugar de su sitio oficial.
    * A la libertad de realizar cambios para la comunidad del juego.
    * La prohibición de utilizar XNova para el propósito comercial de este.
    * La prohibición de eliminar los derechos de autor de los miembros del equipo.

De carácter moral, tiene usted ciertas obligaciones:
    * Informar de un error o una vulnerabilidad o fallo de seguridad.
    * Si conoce la solución del error, comunicar esta a los miembros..
    * No atribuirse los trabajos realizados por el equipo de XNova.
    * Antes de la instalación remota y su utilización, usted tiene que estar desacuerdo con todos los puntos anteriormente relacionados en este documento.
	
	
	
	
========
FRANÇAIS
========


PRÉSENTATION
============

XNova est un projet libre et gratuit de serveur de jeu web multiplateforme (Windows, Linux, Mac, etc...).
Le serveur proprement dit est installable sur n'importe quel serveur Apache. Le joueur lui, n'aura besoin d'utiliser qu'un navigateur web (Internet Explorer, Mozilla Firefox, Netscape, etc...). Le jeu à pour thème la guerre et l'espace, et est, sur le fond, comparable au célèbre Starcraft (version logicielle développée par Blizzard Entertainment). Le but est, sur une planète donnée à l'inscription, d'établir une base en construisant des bâtiments (plus ou moins efficace en fonction de leur niveau d'amélioration) grâce à des ressources énergétiques et minérales (variables principalement selon les bâtiments). Grâce à la base construite, le joueur aura accès à la création de vaisseaux de combat et de défense dont il usera pour se déplacer, attaquer un autre joueur ou s'en défendre. A chaque combat entre les joueurs, des points et des ressources sont réparties. Afin d'être supérieur à son adversaire, le joueur devra faire preuve de stratégie en gérant ses ressources, son énergie, l'évolution de ses bâtiments, des technologies, et l'organisation de ses flottes.
En somme, XNova est un jeu MMOSTR ce qui signifie principalement que les joueurs interagissent entres eux. Un nouveau monde virtuel est donc à découvrir!
Pour installer XNova vous devez posséder:
    * Un serveur Apache avec PHP 4.x (minimum).
    * L'option mail() active (recommandé) et short open tags activé.
    * Une base de donnée MySQL (ou Postgre).
    * Des droits d'accès sur les fichiers (possibilité de changer le CHMOD)
XNova utilise quatre langages de programmation web:
    * PHP
    * Javascript
    * SQL
    * Ajax


Distributions
=============
Avant tout, il est nécessaire de comprendre que XNova n'est pas un repack (c'est-à-dire qu'il ne s'agit pas d'un mix de plusieurs bouts de codes trouvés à droite et à gauche). L'équipe à l'ambition de reforger complètement un nouveau noyau, il s'agit donc d'un tout autre système de jeu.
Les membres de l'équipe d'XNova travaillent en commun sur un système de subversion (dit SVN) afin de faciliter les échanges et la modification des sources. Cependant, le subversion est devenu privé à cause du nombre croissant de problèmes liés aux voles et aux modifications barbares du code source qui commencent déjà à circuler sur internet.
Nous assurerons donc des mises-à-jour quotidienne sous forme d'archives compressées qui seront postées régulièrement sur le forum (partie release). Il est important que tout ce qui soit relatif à XNova soit centralisé sur le forum officiel. Si vous avez donc la moindre question ou suggestion, n'hésitez pas à poster. Il en est de même pour signaler un bug ou bien proposer une solution pour en résoudre un (dit un fix).


Fonctionnement
==============
Différents type des fichiers sont présents dans le dossier qui compose le jeu. Voici donc quelles sont leurs utilités.
    * Admin (fichiers PHP): code source de la partie administrateur.
    * CSS (fichiers CSS): contenant le style de base et régie la structure des cadres.
    * DB (fichiers PHP): permet l'utilisation de requêtes SQL sur votre base de donnée.
    * Images (fichiers GIF, PNG, JPG): images de base, smileys, background de la page de connexion.
    * Includes (fichiers PHP): contenant diverses variables incluses dans les sources ainsi que les fonctions.
    * Install (fichiers PHP): permet l'édition du fichier config.php ainsi que l'installation de la base de donnée.
    * Language (fichiers MO): Tout les textes affichés sont contenus dans ces fichiers, facilite la traduction.
    * Scripts (fichiers JS): contenant certaines actions gérées en javascript.
    * Skins (fichiers CSS, GIF, JPG, PNG): contient le design, permet aux joueurs de choisir la façon dont sont affichées les pages (images, couleurs des cadres, etc...).
    * Templates (fichiers TPL): gèrent la structure des pages, essentiellement les cadres, programmés en HTML.
    * A la racine (fichiers PHP): code source du serveur (côté joueur), permet l'interaction entre les fonctions, les variables, la base de donnée, les actions javascript, le template, le skin et le texte affiché.
Il est nécessaire de préciser quelques points:
    * Les fichiers sources à la racine et dans le dossier admin possèdent chacun un template et utilisent tous les textes des fichiers langues. Aucun texte ou code HTML n'est contenu dans ces fichiers!
    * Il est possible de traduire le contenu d'XNova en copier le dossier fr, en le renommant avec les initiales de la langue dans laquelle sera traduit les textes, et enfin d'éditer les fichiers MO le contenant.
    * Seul le fichier config.php contient les informations de votre base de donnée.


Services
========
Avant tout, l'équipe d'XNova propose un service gratuit, il faut donc vous dire qu'ils n'ont aucune obligation envers les créateurs de serveurs et qu'ils ne sont pas à leur disposition. Dans le cas d'un problème avec un membre du forum, il sera normal qu'il soit sanctionné (exemple: bannissement) et devra donc se débrouiller seul.
Autrement, XNova propose son forum comme terrain d'entre-aide. Il vous est possible de poster vos questions et vos suggestions. Bien entendu, en cas de problème, vous pouvez poster dans la section appropriée et un membre de l'équipe ou du forum tentera d'y répondre.
Du côté du contenu d'XNova, l'équipe fait de son mieux pour apporter des mises-à-jours régulières et de résoudre le plus de problèmes possible.
De plus, outre le fait de corriger des problèmes, nous innovons sur le plan du jeu. En effet, l'équipe proposera de temps en temps une nouvelle fonction au jeu (nouveaux bâtiments, vaisseaux, technologies, etc...) ainsi que de nouveaux systèmes (officiers, colonisations, etc...).
XNova continuera de d'exister jusqu'à ce que les bugs soient corrigés et que l'imagination des programmeurs soient épuisées. Autant dire qu'il reste encore quelques années de travail avant de rendre XNova parfait.


Obligations
===========
XNova est sous licence GNU GPL (GNU General Public License), ce qui implique:
    * Une liberté d'utiliser XNova, pour la création d'un serveur.
    * Une liberté d'étudier son fonctionnement et de l'adapter à ses besoins.
    * Une liberté de redistribuer des copies (non modifiées bien sur) en indiquant l'adresse du site officiel.
    * Une liberté d'améliorer et de rendre publiques les modifications afin que l'ensemble de la communauté en bénéficie.
    * Une interdiction d'user d'XNova pour un but commercial.
    * Une interdiction de supprimer les copyright des membres de l'équipe.
Du côté moral, vous avez tout de même certaines obligations:
    * Signaler un bug ou une faille de sécurité.
    * Après résolution d'un bug, communiquer la solution aux membres.
    * Ne pas s'attribuer les mérites du travail de l'équipe d'XNova.
    * Avant téléchargement et utilisation, vous devez avoir lu cette charte et être en accord avec tout les points qui la constitue.
