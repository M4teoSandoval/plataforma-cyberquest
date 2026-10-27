<?php

/*
 |--------------------------------------------------------------------------
 | Guía paso a paso de las misiones (Operación RASCASSE)
 |--------------------------------------------------------------------------
 | Contenido PEDAGÓGICO para la interfaz. Reglas de seguridad aplicadas:
 |  - NO contiene ninguna flag RASCASSE{...}.
 |  - NO contiene contraseñas, passphrases ni claves privadas: el estudiante
 |    las descubre en el laboratorio (hydra, john, pistas del correo, etc.).
 |    Los comandos que las necesitan las piden de forma interactiva.
 |  - Los comandos se ejecutan en el entorno autorizado del laboratorio; la
 |    plataforma solo guía y valida. La IP de ejemplo (192.168.56.103) es la
 |    documentada, pero puede variar según la instalación local (ver "si falla").
 |
 | Estructura de cada paso:
 |   title, cmd, where (kali|ssh|web|local), what(A), why(B), parts(C: [token,desc]),
 |   where_text(D), expect(E), next(F), fail(G: array), meaning(H, opcional)
 */

$IP = '192.168.56.103'; // IP documentada del laboratorio (host-only). Puede variar.

return [

    /* ================= MISIÓN 1 · Reconocimiento ================= */
    1 => [
        'intro' => 'El reconocimiento es la primera fase de cualquier evaluación de seguridad: '
            .'antes de tocar nada, un atacante (o un analista) dibuja el mapa del objetivo: qué máquina '
            .'hay, qué puertos y servicios expone y qué información deja a la vista. Aquí localizarás el '
            .'servidor del laboratorio, enumerarás su sitio web y seguirás el primer rastro que dejó el responsable.',
        'steps' => [
            [
                'title' => 'Descubrir qué máquina hay en la red',
                'cmd' => "sudo nmap -sn 192.168.56.0/24",
                'where' => 'kali',
                'what' => 'Hace un barrido de la red del laboratorio para ver qué equipos están encendidos y responden, sin escanear aún sus puertos.',
                'why' => 'Todavía no conoces la dirección exacta del servidor víctima. Este barrido te la revela para poder enfocar el resto de la investigación.',
                'parts' => [
                    ['sudo', 'ejecuta el comando con permisos de administrador (nmap los necesita para este tipo de barrido).'],
                    ['nmap', 'herramienta de exploración de redes y puertos.'],
                    ['-sn', 'modo "ping scan": solo descubre equipos vivos, no escanea sus puertos.'],
                    ['192.168.56.0/24', 'rango de la red host-only del laboratorio (las 254 direcciones 192.168.56.1–254).'],
                ],
                'where_text' => 'En la terminal de Kali Linux, conectada a la red host-only del laboratorio.',
                'expect' => 'Una lista de equipos que respondieron, cada uno con su dirección IP. Uno de ellos (que no sea tu propio Kali ni la puerta de enlace) es el servidor de UNAB Racing.',
                'next' => 'Anota esa IP: será el objetivo de todos los comandos siguientes. En los ejemplos usamos la documentada 192.168.56.103.',
                'fail' => [
                    'Si no aparece ningún equipo: comprueba que la VM del laboratorio esté encendida y que tu Kali esté en la misma red host-only (adaptador VirtualBox).',
                    '«command not found»: instala nmap con sudo apt install -y nmap.',
                    'Si tu red host-only usa otro rango, ajústalo (por ejemplo 192.168.57.0/24).',
                ],
            ],
            [
                'title' => 'Identificar los servicios del servidor',
                'cmd' => "nmap -sV -p- 192.168.56.103",
                'where' => 'kali',
                'what' => 'Escanea todos los puertos del servidor e intenta identificar qué servicio y versión corre en cada uno que esté abierto.',
                'why' => 'Necesitas saber por dónde se puede interactuar con la máquina. Aquí descubrirás el servicio web (para investigar el sitio) y el de acceso remoto SSH (que usarás más adelante).',
                'parts' => [
                    ['nmap', 'la misma herramienta de exploración.'],
                    ['-sV', 'detecta la versión del servicio detrás de cada puerto abierto.'],
                    ['-p-', 'analiza los 65535 puertos, no solo los comunes.'],
                    ['192.168.56.103', 'la IP del servidor que encontraste en el paso anterior.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Una tabla de puertos abiertos. Verás al menos un servicio web (HTTP) y el servicio SSH en el puerto 22.',
                'next' => 'Abre el servicio web en el navegador y empieza a enumerarlo (siguiente paso).',
                'fail' => [
                    'Tarda bastante al usar -p-. Si tienes prisa, prueba primero nmap -sV 192.168.56.103 (puertos comunes).',
                    'Si no hay respuesta, confirma la IP con el barrido anterior y la conectividad con ping 192.168.56.103.',
                ],
                'meaning' => 'Cada puerto abierto es una posible puerta de entrada. El web te dará pistas públicas; el SSH será tu acceso una vez tengas credenciales.',
            ],
            [
                'title' => 'Buscar rutas ocultas en el sitio web',
                'cmd' => "curl -s http://192.168.56.103/robots.txt",
                'where' => 'kali',
                'what' => 'Descarga el archivo robots.txt del sitio, donde los administradores indican qué rutas no deberían indexar los buscadores… y que a veces revelan carpetas internas.',
                'why' => 'Es una de las primeras cosas que se revisan en una web: suele delatar directorios que el responsable preferiría mantener ocultos.',
                'parts' => [
                    ['curl', 'herramienta para pedir contenido por HTTP desde la terminal.'],
                    ['-s', 'modo silencioso: no muestra la barra de progreso.'],
                    ['http://192.168.56.103/robots.txt', 'la URL del archivo robots.txt del servidor.'],
                ],
                'where_text' => 'En la terminal de Kali Linux (también puedes abrir la URL en el navegador).',
                'expect' => 'Unas pocas líneas. Fíjate en una entrada Disallow: que apunta a una carpeta interna.',
                'next' => 'Visita esa carpeta interna para ver qué guarda.',
                'fail' => [
                    'Si da 404, revisa el puerto del web en la salida de nmap y ajústalo (por ejemplo http://192.168.56.103:PUERTO/robots.txt).',
                    'Revisa también el código fuente de la portada: puede haber un comentario HTML con una pista (curl -s http://192.168.56.103/ | grep -i interno).',
                ],
                'meaning' => 'Una regla Disallow no protege nada: solo pide "no indexar". Para un investigador es justo donde mirar.',
            ],
            [
                'title' => 'Leer la nota de la carpeta interna',
                'cmd' => "curl -s http://192.168.56.103/interno/nota.txt",
                'where' => 'kali',
                'what' => 'Descarga el archivo nota.txt que está dentro de la carpeta interna que delató robots.txt.',
                'why' => 'Ese archivo contiene la primera evidencia, pero no en texto claro: está codificada.',
                'parts' => [
                    ['curl -s', 'descarga el contenido en modo silencioso.'],
                    ['/interno/nota.txt', 'la ruta de la nota dentro de la carpeta interna (usa el nombre real que viste en robots.txt).'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Una línea larga de texto con letras, números y quizá signos = al final. No es legible a simple vista: está en Base64.',
                'next' => 'Decodifica esa línea Base64 para leer la evidencia real (siguiente paso).',
                'fail' => [
                    'Ajusta el nombre de la carpeta/archivo al que realmente apareció en robots.txt.',
                    'Si ves una página HTML en vez de texto plano, estás pidiendo la carpeta y no el archivo: añade el nombre del .txt.',
                ],
            ],
            [
                'title' => 'Decodificar la evidencia (Base64)',
                'cmd' => "curl -s http://192.168.56.103/interno/nota.txt | base64 -d",
                'where' => 'kali',
                'what' => 'Descarga la nota y la pasa por base64 -d, que convierte ese texto codificado de vuelta a su contenido legible.',
                'why' => 'Base64 no es cifrado: solo una forma de representar datos como texto. Decodificándola aparece la primera flag y una pista sobre la siguiente misión.',
                'parts' => [
                    ['curl -s ...', 'vuelve a descargar la nota.'],
                    ['|', 'tubería: envía la salida de curl directamente al siguiente comando.'],
                    ['base64 -d', 'decodifica (-d = decode) el texto Base64 a su forma original.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un texto legible en español que incluye una bandera con el formato RASCASSE{...} y una referencia a una fotografía del equipo.',
                'next' => 'Copia la flag y envíala abajo en el formulario de esta misión. Guarda la pista de la foto: abre la Misión 2.',
                'fail' => [
                    '«base64: invalid input»: probablemente copiaste la línea incompleta o con espacios. Vuelve a descargarla con curl y pásala tal cual por la tubería.',
                ],
                'meaning' => 'Que una evidencia esté en Base64 (y no cifrada) enseña una lección: codificar NO es proteger. Cualquiera puede revertirlo.',
            ],
        ],
        'hints' => [
            'Un reconocimiento es como estudiar el circuito antes de la carrera: primero el mapa (qué máquina y qué puertos), luego los detalles del sitio web (robots.txt y el código fuente).',
            'El sitio web esconde una carpeta que el propio servidor pide "no indexar". Dentro hay un archivo de texto que no se entiende a primera vista.',
            'Ese texto ilegible está en Base64. Decodifícalo con base64 -d y leerás la evidencia y la pista hacia la fotografía.',
        ],
    ],

    /* ================= MISIÓN 2 · Esteganografía ================= */
    2 => [
        'intro' => 'La esteganografía es el arte de ocultar información dentro de algo que parece inofensivo, '
            .'como una fotografía. A diferencia del cifrado (que hace evidente que algo está protegido), aquí el objetivo '
            .'es que nadie sospeche que hay un mensaje. El responsable escondió las credenciales de un ingeniero dentro de '
            .'una foto de prensa del monoplaza.',
        'steps' => [
            [
                'title' => 'Descargar la fotografía sospechosa',
                'cmd' => "wget http://192.168.56.103/img/unab_w15_montecarlo.jpg",
                'where' => 'kali',
                'what' => 'Descarga a tu Kali la foto del monoplaza que publica el sitio web del equipo.',
                'why' => 'Para analizar una imagen necesitas tenerla en local. Esta foto aparentemente de prensa es la que oculta datos.',
                'parts' => [
                    ['wget', 'herramienta para descargar archivos por HTTP.'],
                    ['http://.../img/unab_w15_montecarlo.jpg', 'la URL de la fotografía dentro de la galería del sitio.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un archivo unab_w15_montecarlo.jpg guardado en tu carpeta actual. Compruébalo con ls -l.',
                'next' => 'Extrae lo que esté oculto dentro de la imagen (siguiente paso).',
                'fail' => [
                    'Ajusta la ruta/puerto si el web no está en el 80 (míralo en la salida de nmap).',
                    'Si la galería usa otro nombre de carpeta, lístala en el navegador o con curl para dar con la imagen.',
                ],
            ],
            [
                'title' => 'Extraer el contenido oculto con steghide',
                'cmd' => "steghide extract -sf unab_w15_montecarlo.jpg -p ''",
                'where' => 'kali',
                'what' => 'Intenta sacar de la imagen el archivo que steghide pudiera tener incrustado, probando una contraseña vacía.',
                'why' => 'steghide es la herramienta típica para ocultar/recuperar datos dentro de imágenes JPG. Si el responsable la usó sin contraseña, el contenido sale directamente.',
                'parts' => [
                    ['steghide', 'herramienta de esteganografía para JPG/BMP/WAV.'],
                    ['extract', 'acción de extraer datos ocultos.'],
                    ['-sf unab_w15_montecarlo.jpg', '"stego file": la imagen que contiene el mensaje.'],
                    ["-p ''", 'la passphrase a probar; aquí, vacía (dos comillas simples seguidas).'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un mensaje tipo "wrote extracted data to ..." y un archivo nuevo (por ejemplo un .txt) en tu carpeta.',
                'next' => 'Lee ese archivo extraído (siguiente paso).',
                'fail' => [
                    '«steghide: command not found»: instálalo con sudo apt install -y steghide.',
                    '«could not extract any data»: la contraseña no es vacía o la imagen no es la correcta; confirma que descargaste la foto del sitio y no una genérica.',
                ],
            ],
            [
                'title' => 'Leer las credenciales extraídas',
                'cmd' => "cat steg_payload.txt",
                'where' => 'kali',
                'what' => 'Muestra en pantalla el contenido del archivo que steghide sacó de la imagen.',
                'why' => 'Ahí están los datos que el responsable escondió: un nombre de usuario, una huella (hash) y la flag de esta misión.',
                'parts' => [
                    ['cat', 'muestra el contenido de un archivo de texto.'],
                    ['steg_payload.txt', 'el nombre del archivo extraído (usa el que indicó steghide; puede llamarse distinto).'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un texto con el usuario de un ingeniero (lo necesitarás en la Misión 3), un hash SHA-256 y una bandera RASCASSE{...}.',
                'next' => 'Envía la flag abajo. Antes de pasar a la Misión 3, confirma el nombre en clave del monoplaza con el hash (siguiente paso).',
                'fail' => [
                    'Si no sabes el nombre exacto del archivo, lista la carpeta con ls -lt y abre el más reciente.',
                ],
                'meaning' => 'El usuario que aparece aquí es la identidad del ingeniero: la puerta de la siguiente fase.',
            ],
            [
                'title' => 'Verificar el nombre en clave con su huella (SHA-256)',
                'cmd' => "printf 'PALABRA' | sha256sum",
                'where' => 'kali',
                'what' => 'Calcula la huella SHA-256 de la palabra que escribas y la imprime, para compararla con el hash que venía en el payload.',
                'why' => 'Un hash permite verificar sin revelar: no puedes "descifrarlo", pero sí comprobar qué palabra lo produce. Así confirmas el nombre en clave correcto del monoplaza.',
                'parts' => [
                    ['printf \'PALABRA\'', 'imprime la palabra candidata SIN salto de línea (importante: si usas echo, añade -n).'],
                    ['|', 'envía esa palabra al siguiente comando.'],
                    ['sha256sum', 'calcula la huella SHA-256 del texto recibido.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Sustituye PALABRA por cada nombre candidato que sospeches.',
                'expect' => 'Una cadena de 64 caracteres hexadecimales. Cuando coincida exactamente con el hash del payload, esa palabra es el nombre en clave correcto.',
                'next' => 'Guarda ese nombre en clave: será parte de la contraseña que necesitas en la Misión 3. Abre la Misión 3.',
                'fail' => [
                    'Si ninguna coincide, revisa mayúsculas/minúsculas y que uses printf (o echo -n) para no añadir un salto de línea, que cambiaría el hash.',
                ],
                'meaning' => 'Misma huella ⇒ misma palabra. Así funciona la verificación de integridad: comparando hashes, no contenidos.',
            ],
        ],
        'hints' => [
            'Una foto normal puede contener datos escondidos sin que se note. Eso es esteganografía: busca "más" dentro de la imagen, no solo la imagen.',
            'La herramienta clásica para JPG es steghide. Prueba a extraer con contraseña vacía (-p \'\').',
            'En el archivo extraído hay un usuario, un hash y la flag. Para saber cuál es el nombre en clave del monoplaza, calcula sha256sum de tus candidatos hasta que coincida con el hash.',
        ],
    ],

    /* ================= MISIÓN 3 · Análisis de red ================= */
    3 => [
        'intro' => 'Con la identidad del ingeniero puedes intentar entrar al servidor. Una vez dentro encontrarás '
            .'capturas de tráfico (archivos PCAP) de las comunicaciones internas del equipo. Un PCAP guarda, paquete a '
            .'paquete, lo que viajó por la red; si algo se envió sin cifrar, se puede leer tal cual, como una radio a boxes '
            .'abierta. Ahí alguien habló de más.',
        'steps' => [
            [
                'title' => 'Construir un diccionario con las palabras del sitio',
                'cmd' => "cewl -d 2 -m 5 -w dic.txt http://192.168.56.103/",
                'where' => 'kali',
                'what' => 'Recorre el sitio web y crea una lista (dic.txt) con las palabras que aparecen en él, para usarlas como candidatas de contraseña.',
                'why' => 'La pista de la misión anterior indica que la contraseña nace del vocabulario del equipo. CeWL automatiza recoger ese vocabulario del propio sitio.',
                'parts' => [
                    ['cewl', 'genera diccionarios a partir del contenido de una web.'],
                    ['-d 2', 'profundidad de rastreo: sigue enlaces hasta 2 niveles.'],
                    ['-m 5', 'longitud mínima de palabra: 5 caracteres.'],
                    ['-w dic.txt', 'guarda el resultado en el archivo dic.txt.'],
                    ['http://192.168.56.103/', 'el sitio del que extraer las palabras.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un archivo dic.txt con decenas de palabras del sitio, incluido el nombre en clave que verificaste en la Misión 2.',
                'next' => 'Combina esas palabras con el año del Gran Premio para formar candidatos de contraseña (siguiente paso).',
                'fail' => [
                    '«cewl: command not found»: instálalo con sudo apt install -y cewl.',
                    'Ajusta el puerto si el web no está en el 80.',
                ],
            ],
            [
                'title' => 'Añadir el año del Gran Premio a cada palabra',
                'cmd' => "sed 's/\$/2025/' dic.txt > dic_anio.txt",
                'where' => 'kali',
                'what' => 'Crea un nuevo diccionario (dic_anio.txt) en el que a cada palabra del anterior se le añade al final el año del Gran Premio.',
                'why' => 'La pista decía: vocabulario del equipo + año del Gran Premio. Este paso construye exactamente esos candidatos combinados.',
                'parts' => [
                    ['sed', 'editor de texto por línea de comandos.'],
                    ["'s/\$/2025/'", 'regla de sustitución: $ representa el final de la línea, así que añade 2025 al final de cada palabra. Ajusta el año si la narrativa indica otro.'],
                    ['dic.txt', 'el diccionario de entrada.'],
                    ['> dic_anio.txt', 'guarda el resultado en un archivo nuevo.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un dic_anio.txt con las mismas palabras pero terminadas en el año. Compruébalo con head dic_anio.txt.',
                'next' => 'Prueba ese diccionario contra el SSH del servidor (siguiente paso).',
                'fail' => [
                    'El año sale de la narrativa (Gran Premio de Mónaco). Si no encuentras la contraseña luego, prueba también con el diccionario sin modificar y con años cercanos.',
                ],
            ],
            [
                'title' => 'Probar las credenciales contra SSH (hydra)',
                'cmd' => "hydra -l pmendez -P dic_anio.txt ssh://192.168.56.103 -t 4",
                'where' => 'kali',
                'what' => 'Prueba de forma automática y controlada cada contraseña del diccionario para el usuario del ingeniero en el servicio SSH, hasta encontrar la válida.',
                'why' => 'Ya tienes el usuario (Misión 2) y una lista de contraseñas candidatas; hydra comprueba cuál funciona sin que tengas que hacerlo a mano.',
                'parts' => [
                    ['hydra', 'herramienta de pruebas de credenciales sobre servicios de red.'],
                    ['-l pmendez', 'el usuario a probar (sustitúyelo por el que hallaste en la foto).'],
                    ['-P dic_anio.txt', 'la lista de contraseñas candidatas (mayúscula = archivo).'],
                    ['ssh://192.168.56.103', 'el servicio y la máquina objetivo.'],
                    ['-t 4', 'usa 4 intentos en paralelo (suave, para no saturar el SSH).'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Solo contra la máquina del laboratorio autorizada.',
                'expect' => 'Una línea resaltada con "login: ... password: ..." cuando dé con la correcta.',
                'next' => 'Conéctate por SSH con esas credenciales (siguiente paso). No hace falta que apuntes la contraseña en ningún sitio.',
                'fail' => [
                    '«hydra: command not found»: sudo apt install -y hydra.',
                    'Si no encuentra nada: confirma el usuario exacto, que el SSH acepte contraseña y revisa que el diccionario contenga la combinación palabra+año.',
                ],
                'meaning' => 'Que una contraseña se adivine con un diccionario del propio sitio enseña por qué las contraseñas no deben derivarse de datos públicos del equipo.',
            ],
            [
                'title' => 'Conectarte al servidor por SSH',
                'cmd' => "ssh pmendez@192.168.56.103",
                'where' => 'ssh',
                'what' => 'Abre una sesión remota en el servidor con el usuario del ingeniero; te pedirá la contraseña que acabas de descubrir.',
                'why' => 'Las evidencias (la captura de red y, más adelante, el contenedor cifrado) están dentro del servidor. Necesitas una sesión real para llegar a ellas.',
                'parts' => [
                    ['ssh', 'cliente de acceso remoto seguro.'],
                    ['pmendez@192.168.56.103', 'usuario@IP: con quién y a qué máquina te conectas.'],
                ],
                'where_text' => 'Escríbelo en Kali. A partir de que entres, tu terminal estará DENTRO del servidor víctima (fíjate en que el prompt cambia a pmendez@...).',
                'expect' => 'Tras aceptar la huella del host y escribir la contraseña, un prompt del servidor. Ya estás dentro.',
                'next' => 'Lee tu flag de acceso y localiza la captura de red (siguientes pasos).',
                'fail' => [
                    '«Permission denied»: contraseña incorrecta; revísala con hydra.',
                    'Si se queja de la clave del host tras recrear la VM: ssh-keygen -R 192.168.56.103 y reconéctate.',
                ],
            ],
            [
                'title' => 'Localizar la captura de tráfico',
                'cmd' => "ls -l /opt/evidence/",
                'where' => 'ssh',
                'what' => 'Lista el contenido de la carpeta de evidencias del servidor para encontrar el archivo de captura de red.',
                'why' => 'Necesitas saber el nombre y la ruta exacta del PCAP antes de copiarlo a Kali para analizarlo con comodidad.',
                'parts' => [
                    ['ls -l', 'lista archivos con detalle (permisos, tamaño, fecha).'],
                    ['/opt/evidence/', 'carpeta donde el laboratorio guardó las evidencias de red.'],
                ],
                'where_text' => 'Dentro de la sesión SSH (en el servidor víctima).',
                'expect' => 'Un archivo con extensión .pcap (por ejemplo team_radio.pcap).',
                'next' => 'Cópialo a tu Kali para analizarlo (siguiente paso).',
                'fail' => [
                    'Si la carpeta no existe o está vacía, busca el pcap: find / -name "*.pcap" 2>/dev/null.',
                ],
            ],
            [
                'title' => 'Copiar la captura a Kali y seguir la conversación',
                'cmd' => "scp pmendez@192.168.56.103:/opt/evidence/team_radio.pcap . && tshark -r team_radio.pcap -q -z follow,tcp,ascii,0",
                'where' => 'kali',
                'what' => 'Primero copia el PCAP del servidor a tu Kali (scp) y luego reconstruye con tshark la conversación TCP para leerla como texto.',
                'why' => 'Dentro de esa captura hay un correo enviado sin cifrar. Reconstruir el flujo te deja leerlo entero: revela a un segundo usuario, la ruta del material cifrado, una pista para su contraseña y la flag.',
                'parts' => [
                    ['scp usuario@IP:ruta .', 'copia segura del archivo remoto a tu carpeta actual (el punto final).'],
                    ['&&', 'ejecuta el segundo comando solo si el primero tuvo éxito.'],
                    ['tshark', 'versión de consola de Wireshark para analizar PCAP.'],
                    ['-r team_radio.pcap', 'lee ese archivo de captura.'],
                    ['-q -z follow,tcp,ascii,0', 'reconstruye (follow) el flujo TCP número 0 y lo muestra como texto ASCII.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. (Alternativa gráfica: abrir el .pcap en Wireshark → clic derecho → Follow → TCP Stream.)',
                'expect' => 'El texto de un correo entre dos personas del equipo: verás un segundo usuario, la ruta de un contenedor cifrado, una pista sobre su contraseña (la sección más icónica del circuito + el año) y una flag RASCASSE{...}.',
                'next' => 'Envía la flag abajo. Apunta la ruta del contenedor y la pista de la contraseña: abre la Misión 4.',
                'fail' => [
                    '«tshark: command not found»: sudo apt install -y tshark (o usa Wireshark gráfico).',
                    'Si el stream 0 no es el correo, prueba otros índices: ...follow,tcp,ascii,1, o filtra por el puerto de correo: tshark -r team_radio.pcap -Y "tcp.port==25".',
                ],
                'meaning' => 'Un correo interno viajó sin cifrar y quedó grabado. Lección: el tráfico en claro es legible por cualquiera que capture la red.',
            ],
        ],
        'hints' => [
            'Primero necesitas entrar al servidor. La pista anterior dice que la contraseña nace del vocabulario del equipo más el año del Gran Premio: construye una lista de candidatos.',
            'Usa CeWL sobre el sitio para recoger las palabras, añádeles el año y pruébalas con hydra contra el usuario del ingeniero en SSH.',
            'Ya dentro, la evidencia es un .pcap en /opt/evidence. Reconstruye el flujo TCP (Follow TCP Stream / tshark follow) para leer el correo sin cifrar: ahí está la flag y la pista del contenedor.',
        ],
    ],

    /* ================= MISIÓN 4 · Criptografía ================= */
    4 => [
        'intro' => 'El Proyecto Rascasse está guardado en una caja fuerte digital de doble capa. Cifrar es transformar '
            .'datos con una clave para que solo quien la tenga pueda leerlos (a diferencia de codificar, como Base64, que no '
            .'protege nada). Aquí abrirás una capa simétrica (un contenedor con contraseña) y dentro otra asimétrica (un PDF '
            .'cifrado con GPG y una clave privada protegida), y verificarás con una firma digital quién preparó el paquete.',
        'steps' => [
            [
                'title' => 'Copiar el contenedor cifrado a Kali',
                'cmd' => "scp pmendez@192.168.56.103:/home/dcastro/evidence/aero_vault.hc .",
                'where' => 'kali',
                'what' => 'Copia a tu Kali el contenedor cifrado que mencionaba el correo, desde la carpeta de la ingeniera de datos.',
                'why' => 'Para trabajar con las herramientas de criptografía te conviene tener el archivo en local.',
                'parts' => [
                    ['scp usuario@IP:ruta .', 'copia segura del archivo remoto a tu carpeta actual.'],
                    ['/home/dcastro/evidence/aero_vault.hc', 'la ruta exacta que indicaba el correo (ajústala a lo que leíste en la captura).'],
                ],
                'where_text' => 'En la terminal de Kali Linux (usa las credenciales del ingeniero de la Misión 3).',
                'expect' => 'El archivo del contenedor (p. ej. aero_vault.hc) en tu carpeta.',
                'next' => 'Ábrelo con la contraseña que dedujiste de la pista del correo (siguiente paso).',
                'fail' => [
                    '«Permission denied» al copiar: confirma la ruta y que el archivo sea legible por tu usuario SSH.',
                    'Si tu laboratorio usa un contenedor OpenSSL (.enc) en lugar de VeraCrypt (.hc), ábrelo con: openssl enc -d -aes-256-cbc -in aero_vault.enc -out vault.tar y continúa extrayendo el .tar. (Ver nota al final de la misión.)',
                ],
            ],
            [
                'title' => 'Abrir el contenedor (capa simétrica)',
                'cmd' => "veracrypt --text --mount aero_vault.hc /mnt/vault",
                'where' => 'kali',
                'what' => 'Monta el contenedor cifrado como si fuera un disco, pidiéndote la contraseña. Al montarse, su contenido aparece en /mnt/vault.',
                'why' => 'Es la primera capa de la caja fuerte: una contraseña simétrica. La dedujiste de la pista del correo (la sección icónica del circuito + el año).',
                'parts' => [
                    ['veracrypt', 'herramienta de contenedores cifrados.'],
                    ['--text', 'modo texto, sin interfaz gráfica.'],
                    ['--mount aero_vault.hc', 'monta ese contenedor.'],
                    ['/mnt/vault', 'carpeta donde quedará accesible el contenido (créala antes con sudo mkdir -p /mnt/vault si no existe).'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Cuando pregunte la contraseña, escribe la que dedujiste del correo (PIM vacío, sin keyfiles).',
                'expect' => 'Sin errores, y /mnt/vault con tres archivos: un PDF cifrado (.gpg), una clave privada (.asc) y una firma (.sig).',
                'next' => 'Como el PDF está cifrado con GPG y la clave privada tiene su propia contraseña, primero recupera esa contraseña (siguiente paso).',
                'fail' => [
                    '«veracrypt: command not found»: instala VeraCrypt en Kali, o usa la variante OpenSSL indicada en la nota de la misión.',
                    '«Incorrect password»: revisa la pista del correo (sección del circuito + año) y mayúsculas/minúsculas.',
                ],
                'meaning' => 'Una capa simétrica protege con una sola contraseña compartida. Dentro verás que aún hay una segunda capa, asimétrica.',
            ],
            [
                'title' => 'Recuperar la contraseña de la clave privada (John)',
                'cmd' => "gpg2john /mnt/vault/dcastro_priv.asc > hash.txt && john hash.txt --wordlist=/usr/share/wordlists/rockyou.txt",
                'where' => 'kali',
                'what' => 'Extrae la "huella" de la contraseña que protege la clave privada GPG y la intenta recuperar probando un diccionario conocido.',
                'why' => 'La clave privada de la ingeniera está protegida por una passphrase. Sin ella no puedes descifrar el PDF. John la recupera por fuerza con diccionario.',
                'parts' => [
                    ['gpg2john archivo.asc', 'convierte la clave privada GPG a un formato que John pueda atacar.'],
                    ['> hash.txt', 'guarda esa huella en un archivo.'],
                    ['john hash.txt', 'John the Ripper intenta recuperar la contraseña.'],
                    ['--wordlist=.../rockyou.txt', 'usa el diccionario rockyou (muy habitual en Kali).'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Cuando John la encuentre, mostrará la contraseña junto al nombre de la clave. (Puedes volver a verla con john --show hash.txt.)',
                'next' => 'Usa esa passphrase para importar la clave y descifrar el PDF (siguientes pasos).',
                'fail' => [
                    'Si rockyou no está descomprimido: sudo gunzip /usr/share/wordlists/rockyou.txt.gz.',
                    'Si no aparece, prueba otro diccionario o reglas; la passphrase sigue el patrón del equipo.',
                ],
            ],
            [
                'title' => 'Importar la clave y descifrar el PDF',
                'cmd' => "gpg --import /mnt/vault/dcastro_priv.asc && gpg -o aero_package_v3.pdf -d /mnt/vault/aero_package_v3.pdf.gpg",
                'where' => 'kali',
                'what' => 'Carga la clave privada en tu GPG (te pedirá la passphrase recuperada) y después descifra el PDF protegido, guardándolo legible.',
                'why' => 'Es la segunda capa (asimétrica): solo la clave privada de la destinataria puede descifrar el PDF. Dentro está la flag y el material de la última misión.',
                'parts' => [
                    ['gpg --import archivo.asc', 'añade la clave privada a tu llavero GPG.'],
                    ['gpg -d archivo.gpg', 'descifra (decrypt) el archivo.'],
                    ['-o aero_package_v3.pdf', 'guarda el resultado descifrado en ese PDF.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Introduce la passphrase que recuperaste con John cuando la pida.',
                'expect' => 'Un PDF descifrado. Ábrelo: contiene la flag de esta misión y un pequeño reto de criptografía (unos números RSA) para la Misión 5.',
                'next' => 'Envía la flag abajo. Verifica la firma para cerrar la misión y guarda los números RSA: abre la Misión 5.',
                'fail' => [
                    '«No secret key»: la importación falló; repite gpg --import y confirma la passphrase.',
                    'Si el visor no abre el PDF, prueba con otro lector; el archivo es un PDF estándar.',
                ],
            ],
            [
                'title' => 'Verificar quién firmó el paquete',
                'cmd' => "gpg --verify /mnt/vault/aero_package_v3.sig aero_package_v3.pdf",
                'where' => 'kali',
                'what' => 'Comprueba la firma digital del PDF: confirma que el archivo no se alteró y quién lo firmó con su clave.',
                'why' => 'La prueba del caso no es solo el contenido, sino demostrar su autoría: la firma acredita que el paquete salió de UNAB Racing.',
                'parts' => [
                    ['gpg --verify', 'verifica una firma digital.'],
                    ['archivo.sig', 'la firma separada (detached) del documento.'],
                    ['aero_package_v3.pdf', 'el documento que debe corresponder a esa firma.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un mensaje "Good signature from ..." con el nombre de la ingeniera de datos. Eso confirma la autoría.',
                'next' => 'Con la firma verificada y la flag enviada, pasa al reto RSA de la Misión 5.',
                'fail' => [
                    '«Can\'t check signature: No public key»: importa primero la clave (paso anterior) para tener la parte pública.',
                    '«BAD signature»: el PDF que verificas no es el mismo que se firmó; usa exactamente el que acabas de descifrar.',
                ],
                'meaning' => 'Una firma digital = autenticidad + integridad: prueba quién firmó y que nada se tocó después.',
            ],
        ],
        'hints' => [
            'La caja fuerte tiene dos capas: primero una simétrica (un contenedor con contraseña), luego una asimétrica (un PDF GPG que necesita una clave privada protegida).',
            'La contraseña del contenedor sale de la pista del correo (la sección más icónica del circuito + el año). Dentro hallarás el PDF cifrado, la clave privada y la firma.',
            'La clave privada tiene su propia passphrase: recupérala con gpg2john + John. Luego importa la clave, descifra el PDF con gpg -d y comprueba la autoría con gpg --verify.',
        ],
    ],

    /* ================= MISIÓN 5 · Escalada de privilegios ================= */
    5 => [
        'intro' => 'El último mensaje está cifrado "a mano" con RSA, el cifrado asimétrico que usa dos claves matemáticamente '
            .'ligadas. Cuando los números son pequeños, se puede romper con matemáticas básicas. El resultado te dirá cómo tomar '
            .'el control total del servidor: la escalada de privilegios consiste en pasar de un usuario normal a administrador '
            .'(root) aprovechando una mala configuración.',
        'steps' => [
            [
                'title' => 'Romper el reto RSA del documento',
                'cmd' => "python3 - <<'PY'\n# Reto del PDF: n, e y los números cifrados c\nn = 23707\ne = 7\nc = [5017, 2302, 12377]\n# n es pequeño: se factoriza para hallar la clave privada d\nfor p in range(2, int(n**0.5)+1):\n    if n % p == 0:\n        q = n // p; break\nphi = (p-1)*(q-1)\nd = pow(e, -1, phi)              # inverso modular de e\nmsg = ''.join(chr(pow(x, d, n)) for x in c)\nprint('Mensaje:', msg)\nPY",
                'where' => 'local',
                'what' => 'Toma los números del reto RSA que venían en el PDF, factoriza el módulo (que es pequeño), calcula la clave privada y descifra el mensaje.',
                'why' => 'RSA es seguro solo si el módulo n es enorme. Aquí n es minúsculo, así que se factoriza al instante y el "secreto" cae. El mensaje revela por dónde escalar a root.',
                'parts' => [
                    ['python3 - <<\'PY\' ... PY', 'ejecuta el bloque de Python que va entre PY y PY (un "here-document").'],
                    ['n, e, c', 'los parámetros del reto tal como aparecen en el PDF (módulo, exponente público y números cifrados).'],
                    ['factorización', 'el bucle busca los dos factores primos p y q de n.'],
                    ['pow(e, -1, phi)', 'calcula d, la clave privada, como inverso de e.'],
                    ['pow(x, d, n)', 'descifra cada número elevándolo a d módulo n.'],
                ],
                'where_text' => 'En la terminal de Kali Linux (o cualquier equipo con Python 3). No necesita estar dentro del servidor.',
                'expect' => 'Imprime un mensaje corto: el nombre de un programa de Linux. Ese binario es tu vía de escalada.',
                'next' => 'Vuelve a tu sesión SSH en el servidor y comprueba qué puedes ejecutar como administrador (siguiente paso).',
                'fail' => [
                    'Si da error de sintaxis, copia el bloque completo con el botón (incluye las líneas PY de inicio y fin).',
                    'Comprueba que los números n, e y c coincidan con los del PDF que descifraste en la Misión 4.',
                ],
                'meaning' => 'Lección de RSA: la seguridad depende del tamaño de las claves. Con números pequeños, es trivial de romper.',
            ],
            [
                'title' => 'Ver qué puedes ejecutar como root',
                'cmd' => "sudo -l",
                'where' => 'ssh',
                'what' => 'Muestra qué comandos tiene permitido ejecutar tu usuario con privilegios de administrador (sudo).',
                'why' => 'Una escalada de privilegios suele aprovechar un permiso sudo mal configurado. Aquí verás que puedes ejecutar como root, sin contraseña, justo el programa que reveló el reto RSA.',
                'parts' => [
                    ['sudo', 'ejecuta comandos como otro usuario, normalmente root.'],
                    ['-l', 'lista (list) los privilegios sudo de tu usuario actual.'],
                ],
                'where_text' => 'Dentro de la sesión SSH en el servidor (como el usuario del ingeniero).',
                'expect' => 'Una línea del tipo "(root) NOPASSWD: /usr/bin/tar": puedes ejecutar ese binario como root sin contraseña.',
                'next' => 'Abusa de ese permiso para obtener una shell de root (siguiente paso).',
                'fail' => [
                    'Si pide contraseña y no la tienes, confirma que estás con el usuario correcto (el del ingeniero).',
                    'Si no aparece nada NOPASSWD, revisa que resolviste bien el reto RSA: el binario permitido es justo ese.',
                ],
            ],
            [
                'title' => 'Escalar a root abusando del permiso sudo',
                'cmd' => "sudo tar -cf /dev/null /dev/null --checkpoint=1 --checkpoint-action=exec=/bin/sh",
                'where' => 'ssh',
                'what' => 'Usa una función legítima de tar (ejecutar una acción en un "checkpoint") para lanzar una shell. Como tar corre como root vía sudo, esa shell también es de root.',
                'why' => 'Es la técnica documentada en GTFOBins: cuando puedes ejecutar tar como root, su opción de checkpoint-action te da un intérprete con privilegios de administrador.',
                'parts' => [
                    ['sudo tar', 'ejecuta tar como root (permitido por la mala configuración).'],
                    ['-cf /dev/null /dev/null', 'crea un archivo "a la nada": es solo la excusa para que tar trabaje.'],
                    ['--checkpoint=1', 'pide a tar que haga un punto de control tras el primer registro.'],
                    ['--checkpoint-action=exec=/bin/sh', 'en ese punto de control, ejecuta una shell (/bin/sh).'],
                ],
                'where_text' => 'Dentro de la sesión SSH en el servidor.',
                'expect' => 'Un nuevo prompt de shell. Comprueba con whoami: debe responder root.',
                'next' => 'Lee la prueba final del caso (siguiente paso).',
                'fail' => [
                    'Si whoami no dice root, confirma que usaste sudo y que el permiso NOPASSWD era sobre tar.',
                    'Si el binario permitido fuera otro, consulta su técnica equivalente en gtfobins.github.io.',
                ],
                'meaning' => 'Un permiso sudo demasiado amplio sobre un binario común se convierte en control total. Por eso sudo debe concederse al mínimo imprescindible.',
            ],
            [
                'title' => 'Leer la prueba final',
                'cmd' => "cat /root/proof.txt",
                'where' => 'ssh',
                'what' => 'Muestra el contenido del archivo que solo root puede leer: la prueba que cierra la investigación.',
                'why' => 'Es la evidencia final de que el responsable tomó el control total del servidor. Contiene la última flag.',
                'parts' => [
                    ['cat', 'muestra el contenido de un archivo.'],
                    ['/root/proof.txt', 'archivo en el directorio de root, accesible solo con privilegios de administrador.'],
                ],
                'where_text' => 'En la shell de root que acabas de obtener (dentro del servidor).',
                'expect' => 'Un texto con la flag final RASCASSE{...}.',
                'next' => 'Envía la flag abajo. Con las cinco flags validadas, cerrarás la Operación RASCASSE y podrás descargar tu certificado.',
                'fail' => [
                    '«Permission denied»: no eres root; vuelve al paso anterior y relanza la shell con la técnica de tar.',
                ],
                'meaning' => 'Caso cerrado: has demostrado cómo entró el responsable, qué se llevó, quién lo firmó y cómo tomó el control del servidor.',
            ],
        ],
        'hints' => [
            'El mensaje del PDF está cifrado con RSA usando números muy pequeños. Un cifrado así se puede romper con matemáticas básicas.',
            'Factoriza el módulo n para obtener la clave privada d y descifra: el resultado es el nombre de un comando de Linux.',
            'Ese comando lo puedes ejecutar como root sin contraseña (míralo con sudo -l). Usa la técnica de GTFOBins para ese binario, obtén una shell de root y lee /root/proof.txt.',
        ],
    ],
];
