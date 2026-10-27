<?php

/*
 |--------------------------------------------------------------------------
 | Guía paso a paso de las misiones (Operación RASCASSE)
 |--------------------------------------------------------------------------
 | Comandos tomados TAL CUAL de la "Guía Red Team" oficial del taller
 | (fuente de verdad). No se añaden comandos que no aparezcan en esa guía.
 | Entorno real: víctima 192.168.56.103, web Apache en el puerto 2027,
 | SSH en el 22. Kali como atacante.
 |
 | Reglas de seguridad aplicadas (del brief del taller):
 |  - NO se incluye ninguna flag RASCASSE{...} real (solo el placeholder).
 |  - NO se muestran contraseñas ni passphrases: se usan placeholders
 |    (<CLAVE_DEL_PCAP>, <PASSPHRASE_DCASTRO>) o el propio comando las pide de
 |    forma interactiva (ssh/scp). El alumno las obtiene en el laboratorio.
 |  - Se conservan EXACTAMENTE las opciones, rutas y flags de los comandos de la
 |    guía; solo el valor secreto va como placeholder.
 |
 | Estructura de cada paso:
 |   title, cmd, where (kali|ssh|web|local), what(A), why(B), parts(C: [token,desc]),
 |   where_text(D), expect(E), next(F), fail(G: array), meaning(H, opcional)
 */

return [

    /* ================= MISIÓN 1 · Reconocimiento y acceso ================= */
    1 => [
        'intro' => 'El reconocimiento es la primera fase de cualquier evaluación de seguridad: antes de tocar nada '
            .'dibujas el mapa del objetivo (qué máquina hay, qué puertos y servicios expone) y recoges la información que '
            .'deja a la vista. Aquí localizarás el servidor, sacarás los usuarios de su web, conseguirás la contraseña del '
            .'ingeniero y entrarás por SSH para leer tu primera evidencia.',
        'steps' => [
            [
                'title' => 'Escanear la red y descubrir el servidor',
                'cmd' => "nmap -A -T4 -p- -vv 192.168.56.*",
                'where' => 'kali',
                'what' => 'Escanea toda la red del laboratorio, todos los puertos de cada equipo, e identifica servicios y versiones.',
                'why' => 'No conoces aún la IP de la víctima ni qué expone. Este escaneo te revela la máquina (192.168.56.103) y sus servicios: la web Apache en el 2027 y el SSH en el 22.',
                'parts' => [
                    ['nmap', 'herramienta de exploración de redes y puertos.'],
                    ['-A', 'análisis agresivo: detecta versiones, sistema operativo y scripts por defecto.'],
                    ['-T4', 'velocidad rápida (nivel 4 de 5).'],
                    ['-p-', 'analiza los 65535 puertos, no solo los comunes.'],
                    ['-vv', 'salida muy detallada (verbose).'],
                    ['192.168.56.*', 'toda la red del laboratorio (192.168.56.1–254).'],
                ],
                'where_text' => 'En la terminal de Kali Linux, conectada a la red del laboratorio.',
                'expect' => 'Una máquina (192.168.56.103) con los puertos 22 (SSH, OpenSSH) y 80/2027 (web, Apache httpd). El título de la web es "UIS GP Racing — Proyecto Rascasse".',
                'next' => 'Abre esa web en Firefox para estudiar el equipo (siguiente paso).',
                'fail' => [
                    '«nmap: command not found»: sudo apt install -y nmap.',
                    'Si no aparece ninguna máquina, confirma que la VM víctima esté encendida y que tu Kali esté en la misma red.',
                    'Si tu red usa otro rango, ajústalo (por ejemplo 192.168.49.*).',
                ],
            ],
            [
                'title' => 'Explorar la web del equipo en Firefox',
                'cmd' => "firefox http://192.168.56.103:2027/",
                'where' => 'web',
                'what' => 'Abre el sitio del equipo (puerto 2027) en el navegador Firefox de Kali para leer su contenido.',
                'why' => 'La web de "UIS GP Racing" presenta a sus integrantes. De ahí sacarás los posibles nombres de usuario del servidor.',
                'parts' => [
                    ['firefox', 'el navegador gráfico de Kali.'],
                    ['http://192.168.56.103:2027/', 'la dirección del sitio (ojo al puerto 2027).'],
                ],
                'where_text' => 'En Kali: lánzalo desde la terminal o abre Firefox y escribe la dirección en la barra.',
                'expect' => 'La página de UIS GP Racing con la sección "Nuestro equipo": ingenieros y personal (Pablo Méndez, Daniela Castro, etc.).',
                'next' => 'Apunta esos nombres y conviértelos en posibles usuarios (siguiente paso).',
                'fail' => [
                    'Si no carga, confirma el puerto (2027) y la IP con la salida de nmap.',
                    'Firefox puede avisar de "sitio no seguro" por ser HTTP interno del laboratorio: es normal, continúa.',
                ],
                'meaning' => 'Los nombres y cargos del equipo son materia prima para adivinar usuarios y, más adelante, contraseñas.',
            ],
            [
                'title' => 'Crear la lista de usuarios posibles',
                'cmd' => "nano usuarios.txt",
                'where' => 'kali',
                'what' => 'Abre un editor de texto para crear un archivo con los nombres de usuario que podrían existir en el servidor (incluido root).',
                'why' => 'Para probar credenciales necesitas una lista de usuarios. Los deduces de los nombres del equipo que viste en la web (por ejemplo, apellido o inicial+apellido).',
                'parts' => [
                    ['nano', 'editor de texto sencillo en la terminal.'],
                    ['usuarios.txt', 'el archivo donde escribes un usuario por línea (incluye root). Guarda con Ctrl+O y sal con Ctrl+X.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un archivo usuarios.txt con varios candidatos, uno por línea.',
                'next' => 'Construye ahora un diccionario de contraseñas a partir de la web (siguiente paso).',
                'fail' => [
                    'Si dudas del formato de usuario, prueba variantes (nombre, apellido, inicial+apellido) y añade root.',
                ],
            ],
            [
                'title' => 'Generar un diccionario con las palabras del sitio',
                'cmd' => "cewl -d 2 -m 6 -w dic.txt http://192.168.56.103:2027/",
                'where' => 'kali',
                'what' => 'Recorre la web y crea una lista (dic.txt) con las palabras que aparecen en ella, para usarlas como contraseñas candidatas.',
                'why' => 'La contraseña nace del vocabulario del equipo. CeWL automatiza recoger ese vocabulario del propio sitio.',
                'parts' => [
                    ['cewl', 'genera diccionarios a partir del contenido de una web.'],
                    ['-d 2', 'profundidad de rastreo: sigue enlaces hasta 2 niveles.'],
                    ['-m 6', 'longitud mínima de palabra: 6 caracteres.'],
                    ['-w dic.txt', 'guarda el resultado en dic.txt.'],
                    ['http://192.168.56.103:2027/', 'el sitio del que extraer las palabras.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un dic.txt con decenas de palabras del sitio, incluida la palabra en clave del monoplaza.',
                'next' => 'Combina esas palabras con el año del Gran Premio (siguiente paso).',
                'fail' => [
                    '«cewl: command not found»: sudo apt install -y cewl.',
                ],
            ],
            [
                'title' => 'Añadir el año del Gran Premio y unir los diccionarios',
                'cmd' => "sed 's/\$/2027/' dic.txt > dic2027.txt\ncat dic.txt dic2027.txt > dic_final.txt",
                'where' => 'kali',
                'what' => 'Crea un diccionario que, además de las palabras originales, incluye cada palabra con el año del Gran Premio añadido al final.',
                'why' => 'La contraseña es la palabra en clave + el año. Esto genera esos candidatos y los une con los originales en una sola lista (dic_final.txt).',
                'parts' => [
                    ["sed 's/\$/2027/' dic.txt", 'añade 2027 al final ($ = fin de línea) de cada palabra.'],
                    ['> dic2027.txt', 'guarda esas variantes en un archivo.'],
                    ['cat dic.txt dic2027.txt > dic_final.txt', 'une el diccionario original y las variantes con año en dic_final.txt.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Ajusta el año si la narrativa indica otro.',
                'expect' => 'Un dic_final.txt con las palabras y sus variantes terminadas en el año.',
                'next' => 'Prueba la lista de usuarios y contraseñas contra el SSH (siguiente paso).',
                'fail' => [
                    'El año sale de la narrativa del Gran Premio (Mónaco 2027). Si no cuadra luego, prueba años cercanos.',
                ],
            ],
            [
                'title' => 'Probar las credenciales contra SSH (hydra)',
                'cmd' => "hydra -L usuarios.txt -P dic_final.txt 192.168.56.103 ssh -t 4 -V",
                'where' => 'kali',
                'what' => 'Prueba de forma automática y controlada cada usuario de la lista con cada contraseña del diccionario en el servicio SSH, hasta encontrar combinaciones válidas.',
                'why' => 'Ya tienes usuarios candidatos y contraseñas candidatas; hydra comprueba cuáles funcionan sin hacerlo a mano.',
                'parts' => [
                    ['hydra', 'herramienta de pruebas de credenciales sobre servicios de red.'],
                    ['-L usuarios.txt', 'lista de usuarios (mayúscula = archivo de usuarios).'],
                    ['-P dic_final.txt', 'lista de contraseñas (mayúscula = archivo de contraseñas).'],
                    ['192.168.56.103 ssh', 'la máquina objetivo y el servicio a atacar (SSH).'],
                    ['-t 4', '4 intentos en paralelo (suave, para no saturar el SSH).'],
                    ['-V', 'muestra cada intento (verbose).'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Solo contra la máquina del laboratorio autorizada.',
                'expect' => 'Una o varias líneas resaltadas con "login: ... password: ...". Una de ellas es la del ingeniero de pista (pmendez).',
                'next' => 'Conéctate por SSH con esas credenciales (siguiente paso). No hace falta que anotes la contraseña.',
                'fail' => [
                    '«hydra: command not found»: sudo apt install -y hydra.',
                    'Si no encuentra nada, confirma que el SSH acepta contraseña y que dic_final.txt contiene la combinación palabra+año.',
                ],
                'meaning' => 'Que una contraseña se adivine con un diccionario del propio sitio enseña por qué no deben derivarse de datos públicos del equipo.',
            ],
            [
                'title' => 'Entrar por SSH y leer tu primera evidencia',
                'cmd' => "ssh pmendez@192.168.56.103",
                'where' => 'ssh',
                'what' => 'Abre una sesión remota en el servidor con la cuenta del ingeniero; te pedirá la contraseña que descubrió hydra. Ya dentro, lista tu home y lee user.txt.',
                'why' => 'La primera flag (y la pista hacia la foto) están en el archivo user.txt del home de pmendez. Necesitas la sesión para leerlo.',
                'parts' => [
                    ['ssh', 'cliente de acceso remoto seguro.'],
                    ['pmendez@192.168.56.103', 'usuario@IP: con qué cuenta y a qué máquina te conectas.'],
                ],
                'where_text' => 'Escríbelo en Kali. Desde que entres, tu terminal estará DENTRO del servidor víctima (el prompt cambia a pmendez@unab-racing-server). Ya dentro, ejecuta: ls  y luego  cat user.txt',
                'expect' => 'ls muestra user.txt; cat user.txt imprime una bandera RASCASSE{...} y un mensaje: "Guarde una foto del W15 en /opt/gallery por si acaso".',
                'next' => 'Envía la flag abajo. Guarda la pista de la foto (/opt/gallery): abre la Misión 2.',
                'fail' => [
                    '«Permission denied»: contraseña incorrecta; revísala con hydra.',
                    '«cat.txt: command not found»: escribiste "cat.txt" junto; recuerda el espacio: cat user.txt.',
                    'Si se queja de la clave del host tras recrear la VM: ssh-keygen -R 192.168.56.103 y reconéctate.',
                ],
                'meaning' => 'La flag confirma el compromiso del servidor y la pista te dirige a la fotografía de la galería.',
            ],
        ],
        'hints' => [
            'Un reconocimiento es como estudiar el circuito antes de la carrera: primero el mapa (nmap), luego la web del equipo en Firefox para sacar usuarios, y después conseguir acceso.',
            'La contraseña nace del vocabulario del sitio (CeWL) más el año del Gran Premio. Arma usuarios.txt con los nombres del equipo.',
            'Une el diccionario con el año (sed/cat) y pruébalo con hydra contra SSH. Entra con ssh y lee la flag con cat user.txt.',
        ],
    ],

    /* ================= MISIÓN 2 · Esteganografía ================= */
    2 => [
        'intro' => 'La esteganografía es el arte de ocultar información dentro de algo que parece inofensivo, como una '
            .'fotografía. A diferencia del cifrado (que hace evidente que algo está protegido), aquí el objetivo es que nadie '
            .'sospeche que hay un mensaje. En la foto oficial del monoplaza (que la pista de user.txt te indicó, en /opt/gallery) '
            .'está escondido el acceso de un ingeniero.',
        'steps' => [
            [
                'title' => 'Copiar la foto de la galería a Kali',
                'cmd' => "scp pmendez@192.168.56.103:/opt/gallery/unab_w15_montecarlo.jpg .",
                'where' => 'kali',
                'what' => 'Copia a tu Kali la fotografía del monoplaza desde la galería del servidor. Te pedirá la contraseña del ingeniero.',
                'why' => 'Para analizar la imagen con steghide te conviene tenerla en local. Abre otra terminal en Kali y deja la sesión SSH abierta.',
                'parts' => [
                    ['scp', 'copia segura de archivos por SSH.'],
                    ['pmendez@192.168.56.103:/opt/gallery/unab_w15_montecarlo.jpg', 'la ruta remota de la foto (la que indicaba user.txt).'],
                    ['.', 'destino: tu carpeta actual en Kali.'],
                ],
                'where_text' => 'En una terminal de Kali Linux (distinta a la sesión SSH). Introduce la contraseña del ingeniero cuando la pida.',
                'expect' => 'El archivo unab_w15_montecarlo.jpg en tu Kali.',
                'next' => 'Extrae lo que esté oculto dentro de la imagen (siguiente paso).',
                'fail' => [
                    '«Permission denied»: contraseña incorrecta o ruta equivocada; confírmalas.',
                ],
            ],
            [
                'title' => 'Extraer el contenido oculto con steghide',
                'cmd' => "steghide extract -sf unab_w15_montecarlo.jpg -p \"\" -xf nota.txt",
                'where' => 'kali',
                'what' => 'Saca de la imagen el archivo que steghide tenía incrustado, probando contraseña vacía, y lo guarda como nota.txt.',
                'why' => 'steghide es la herramienta típica para ocultar/recuperar datos en JPG. Si se usó sin contraseña, el contenido sale directamente.',
                'parts' => [
                    ['steghide extract', 'acción de extraer datos ocultos.'],
                    ['-sf unab_w15_montecarlo.jpg', '"stego file": la imagen que contiene el mensaje.'],
                    ['-p ""', 'la passphrase a probar; aquí, vacía.'],
                    ['-xf nota.txt', '"extract file": guarda lo extraído en nota.txt.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un mensaje "wrote extracted data to nota.txt" y el archivo nota.txt en tu carpeta.',
                'next' => 'Decodifica nota.txt para leer las credenciales y la flag (siguiente paso).',
                'fail' => [
                    '«steghide: command not found»: sudo apt install -y steghide.',
                    '«could not extract any data»: confirma que la contraseña es vacía y que es la foto correcta del servidor.',
                ],
            ],
            [
                'title' => 'Decodificar la nota extraída (Base64)',
                'cmd' => "cat nota.txt | base64 -d",
                'where' => 'kali',
                'what' => 'Muestra el contenido de nota.txt decodificándolo de Base64 a texto legible.',
                'why' => 'Lo que steghide sacó está codificado en Base64. Decodificado aparece el usuario del ingeniero, la ruta de la siguiente evidencia, una huella SHA-256 para verificar el nombre en clave, y la flag.',
                'parts' => [
                    ['cat nota.txt', 'lee el archivo extraído.'],
                    ['|', 'tubería hacia el decodificador.'],
                    ['base64 -d', 'decodifica el Base64 a su forma original.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Un texto que confirma el usuario pmendez, indica que el tráfico quedó capturado en /opt/evidence/team_radio.pcap, incluye un hash SHA-256 de la palabra en clave y una bandera RASCASSE{...}.',
                'next' => 'Envía la flag abajo. Apunta la ruta /opt/evidence/team_radio.pcap: abre la Misión 3.',
                'fail' => [
                    'Si no se ve texto legible, confirma que nota.txt se extrajo bien (paso anterior).',
                ],
                'meaning' => 'La huella SHA-256 de la nota permite verificar cuál es la palabra en clave correcta sin "descifrarla": dos textos con la misma huella son la misma palabra. Así se comprueba la integridad.',
            ],
        ],
        'hints' => [
            'Una foto normal puede contener datos escondidos sin que se note. Eso es esteganografía: la foto de /opt/gallery guarda algo más.',
            'Cópiala con scp y usa steghide con contraseña vacía (-p "") para extraerla a un archivo (nota.txt).',
            'Lo extraído está en Base64: decodifícalo con cat nota.txt | base64 -d para ver el usuario, la ruta del .pcap y la flag.',
        ],
    ],

    /* ================= MISIÓN 3 · Análisis de red ================= */
    3 => [
        'intro' => 'Un archivo PCAP es una captura de tráfico: guarda, paquete a paquete, lo que viajó por la red. '
            .'Si algo se envió sin cifrar (como un correo por SMTP), se puede leer tal cual, como una radio a boxes abierta. '
            .'La nota de la foto te dijo que el tráfico del equipo quedó capturado en /opt/evidence/team_radio.pcap.',
        'steps' => [
            [
                'title' => 'Copiar la captura a Kali',
                'cmd' => "scp pmendez@192.168.56.103:/opt/evidence/team_radio.pcap .",
                'where' => 'kali',
                'what' => 'Copia el archivo .pcap del servidor a tu Kali. Te pedirá la contraseña del ingeniero.',
                'why' => 'Analizar la captura con tshark es más cómodo en Kali.',
                'parts' => [
                    ['scp', 'copia segura por SSH.'],
                    ['pmendez@192.168.56.103:/opt/evidence/team_radio.pcap', 'la ruta remota del PCAP (la que indicaba la nota de la foto).'],
                    ['.', 'destino: tu carpeta actual en Kali.'],
                ],
                'where_text' => 'En una terminal de Kali Linux.',
                'expect' => 'El archivo team_radio.pcap en tu Kali (verás el progreso de la copia al 100%).',
                'next' => 'Extrae los correos de la captura (siguiente paso).',
                'fail' => [
                    'Confirma la ruta exacta que te dio la nota de la Misión 2.',
                ],
            ],
            [
                'title' => 'Ver los correos de la captura (SMTP)',
                'cmd' => "tshark -r team_radio.pcap -Y smtp -T fields -e text",
                'where' => 'kali',
                'what' => 'Lee la captura y muestra solo el tráfico de correo (SMTP), extrayendo el texto de esos paquetes.',
                'why' => 'Las comunicaciones de correo viajaron sin cifrar. Filtrando SMTP localizas rápidamente el mensaje relevante entre los dos usuarios.',
                'parts' => [
                    ['tshark', 'versión de consola de Wireshark para analizar PCAP.'],
                    ['-r team_radio.pcap', 'lee ese archivo de captura.'],
                    ['-Y smtp', 'aplica el filtro de visualización: solo protocolo SMTP (correo).'],
                    ['-T fields -e text', 'saca los campos de texto de esos paquetes.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Fragmentos del correo: remitente, destinatario y asunto ("[INTERNO] Paquete aero Rascasse - ubicacion y clave").',
                'next' => 'Reconstruye la conversación completa para leerla entera (siguiente paso).',
                'fail' => [
                    '«tshark: command not found»: sudo apt install -y tshark.',
                ],
            ],
            [
                'title' => 'Reconstruir la conversación completa',
                'cmd' => "tshark -r team_radio.pcap -q -z follow,tcp,ascii,0",
                'where' => 'kali',
                'what' => 'Reconstruye el flujo TCP completo de la conversación y lo muestra como texto, igual que el "Follow TCP Stream" de Wireshark.',
                'why' => 'Así lees el correo entero: revela a la segunda usuaria (dcastro), la ruta del contenedor cifrado, la clave del contenedor y la passphrase de la clave, y la flag.',
                'parts' => [
                    ['-q', 'modo silencioso: solo muestra el resultado del análisis.'],
                    ['-z follow,tcp,ascii,0', 'reconstruye (follow) el flujo TCP número 0 y lo muestra en ASCII.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. (Alternativa gráfica: abrir el .pcap en Wireshark → clic derecho → Follow → TCP Stream.)',
                'expect' => 'El texto del correo entre dcastro y pmendez: la ruta /home/dcastro/evidence/aero_vault.enc, la clave del contenedor, la passphrase del portal de dcastro y una línea Ref: con la flag.',
                'next' => 'Envía la flag abajo. Apunta la ruta del contenedor, la clave del contenedor y la passphrase de dcastro: abre la Misión 4.',
                'fail' => [
                    'Si el flujo 0 no es el correo, prueba otros índices: ...follow,tcp,ascii,1, o filtra por puerto: tshark -r team_radio.pcap -Y "tcp.port==25".',
                ],
                'meaning' => 'Un correo interno viajó sin cifrar y quedó grabado. Lección: el tráfico en claro es legible por cualquiera que capture la red.',
            ],
        ],
        'hints' => [
            'La nota de la foto te dio la ruta de un .pcap en /opt/evidence. Cópialo a Kali y ábrelo con tshark o Wireshark.',
            'El correo viaja sin cifrar por SMTP: filtra con -Y smtp para localizarlo.',
            'Reconstruye el flujo TCP (follow,tcp,ascii,0 o Follow TCP Stream) para leer el correo completo: ahí están la flag, la ruta del contenedor y las claves que necesitas en la Misión 4.',
        ],
    ],

    /* ================= MISIÓN 4 · Criptografía ================= */
    4 => [
        'intro' => 'El Proyecto Rascasse está guardado en una caja fuerte digital de doble capa. Cifrar es transformar '
            .'datos con una clave para que solo quien la tenga pueda leerlos (a diferencia de codificar, como Base64, que no '
            .'protege nada). Abrirás una capa simétrica (un contenedor cifrado con OpenSSL) y dentro otra asimétrica (un PDF '
            .'cifrado con GPG y una clave privada protegida).',
        'steps' => [
            [
                'title' => 'Copiar el contenedor cifrado a Kali',
                'cmd' => "scp pmendez@192.168.56.103:/home/dcastro/evidence/aero_vault.enc .",
                'where' => 'kali',
                'what' => 'Copia a tu Kali el contenedor cifrado que indicaba el correo, desde la carpeta de la ingeniera de datos.',
                'why' => 'Para trabajar con OpenSSL y GPG te conviene tener el archivo en local.',
                'parts' => [
                    ['scp', 'copia segura por SSH.'],
                    ['pmendez@192.168.56.103:/home/dcastro/evidence/aero_vault.enc', 'la ruta remota del contenedor (la que leíste en el correo).'],
                    ['.', 'destino: tu carpeta actual en Kali.'],
                ],
                'where_text' => 'En una terminal de Kali Linux.',
                'expect' => 'El archivo aero_vault.enc en tu Kali.',
                'next' => 'Ábrelo con la clave del contenedor que viste en el correo (siguiente paso).',
                'fail' => [
                    '«Permission denied»: confirma la ruta y que el archivo sea legible por tu usuario SSH.',
                ],
            ],
            [
                'title' => 'Abrir el contenedor con OpenSSL (capa simétrica)',
                'cmd' => "openssl enc -d -aes-256-cbc -pbkdf2 -iter 200000 -md sha512 -in aero_vault.enc -out paquete.tar.gz -pass pass:<CLAVE_DEL_PCAP>",
                'where' => 'kali',
                'what' => 'Descifra el contenedor con la contraseña simétrica y guarda el resultado como un archivo comprimido (paquete.tar.gz).',
                'why' => 'Es la primera capa de la caja fuerte: una contraseña simétrica. La leíste en el correo del PCAP (la clave del contenedor).',
                'parts' => [
                    ['openssl enc -d', 'cifra/descifra con OpenSSL; -d = descifrar (decrypt).'],
                    ['-aes-256-cbc', 'algoritmo simétrico usado (AES de 256 bits, modo CBC).'],
                    ['-pbkdf2 -iter 200000 -md sha512', 'cómo se deriva la clave a partir de la contraseña (deben coincidir con el cifrado original).'],
                    ['-in aero_vault.enc', 'archivo de entrada (el contenedor).'],
                    ['-out paquete.tar.gz', 'archivo de salida (el contenido descifrado).'],
                    ['-pass pass:<CLAVE_DEL_PCAP>', 'la contraseña: sustituye <CLAVE_DEL_PCAP> por la clave del contenedor que leíste en el correo.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Reemplaza <CLAVE_DEL_PCAP> por la clave real que descubriste en la Misión 3.',
                'expect' => 'Sin errores y un archivo paquete.tar.gz. Si la contraseña es incorrecta, OpenSSL dará "bad decrypt".',
                'next' => 'Extrae el contenido del paquete (siguiente paso).',
                'fail' => [
                    '«bad decrypt»: contraseña incorrecta o parámetros distintos; revisa la clave del correo y que uses -pbkdf2 -iter 200000 -md sha512.',
                ],
                'meaning' => 'Una capa simétrica protege con una sola contraseña compartida. Dentro verás que aún hay otra capa, asimétrica.',
            ],
            [
                'title' => 'Extraer el contenido del paquete',
                'cmd' => "mkdir m4 && tar xzf paquete.tar.gz -C m4 && ls -l m4",
                'where' => 'kali',
                'what' => 'Crea una carpeta m4, descomprime dentro el paquete y lista lo que contiene.',
                'why' => 'El contenedor guardaba un archivo comprimido (.tar.gz) con tres ficheros. Necesitas verlos para seguir.',
                'parts' => [
                    ['mkdir m4', 'crea la carpeta de trabajo m4.'],
                    ['&&', 'ejecuta el siguiente comando solo si el anterior tuvo éxito.'],
                    ['tar xzf paquete.tar.gz -C m4', 'extrae (x) el .tar.gz (z = gzip) dentro de m4 (-C).'],
                    ['ls -l m4', 'lista el contenido extraído.'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Tres archivos: el PDF cifrado con GPG (.pdf.gpg), su firma (.sig) y la clave privada de dcastro (.asc).',
                'next' => 'Importa la clave privada y descifra el PDF (siguiente paso).',
                'fail' => [
                    'Si tar se queja del formato, confirma que paquete.tar.gz se descifró bien (paso anterior).',
                ],
            ],
            [
                'title' => 'Importar la clave privada y descifrar el PDF (capa asimétrica)',
                'cmd' => "export GNUPGHOME=\$(mktemp -d)\ngpg --batch --pinentry-mode loopback --passphrase '<PASSPHRASE_DCASTRO>' --import m4/dcastro_priv.asc\ngpg --batch --pinentry-mode loopback --passphrase '<PASSPHRASE_DCASTRO>' --output aero_package_v3.pdf --decrypt m4/aero_package_v3.pdf.gpg",
                'where' => 'kali',
                'what' => 'Prepara un llavero GPG temporal, importa la clave privada de dcastro (protegida por su passphrase) y descifra el PDF.',
                'why' => 'Es la segunda capa (asimétrica): solo la clave privada de la destinataria puede descifrar el PDF. Su passphrase es la contraseña del portal de dcastro que viste en el correo.',
                'parts' => [
                    ['export GNUPGHOME=$(mktemp -d)', 'usa un llavero GPG temporal, para no tocar tu configuración.'],
                    ['gpg --batch --pinentry-mode loopback', 'modo no interactivo para pasar la passphrase por línea de comandos.'],
                    ["--passphrase '<PASSPHRASE_DCASTRO>'", 'la passphrase de la clave: sustitúyela por la del portal de dcastro (del correo).'],
                    ['--import m4/dcastro_priv.asc', 'importa la clave privada al llavero.'],
                    ['--decrypt m4/aero_package_v3.pdf.gpg', 'descifra el PDF.'],
                    ['--output aero_package_v3.pdf', 'guarda el PDF descifrado.'],
                ],
                'where_text' => 'En la terminal de Kali Linux. Reemplaza <PASSPHRASE_DCASTRO> por la passphrase real descubierta en la Misión 3.',
                'expect' => 'La clave se importa y se genera aero_package_v3.pdf (descifrado).',
                'next' => 'Saca la flag y el reto RSA del PDF (siguiente paso).',
                'fail' => [
                    '«No secret key»: la importación falló; revisa la passphrase y repite el import.',
                    '«Bad passphrase»: la passphrase del portal de dcastro no es correcta; vuelve a leer el correo.',
                ],
                'meaning' => 'Cifrado asimétrico: lo que se cifra para dcastro solo lo abre su clave privada, y esa clave está a su vez protegida con una passphrase.',
            ],
            [
                'title' => 'Sacar la flag y el reto RSA del PDF',
                'cmd' => "pdftotext aero_package_v3.pdf - | grep -Ei \"RASCASSE|n *=|e *=|c *=|tar\"",
                'where' => 'kali',
                'what' => 'Convierte el PDF a texto y filtra las líneas importantes: la flag y los parámetros del reto RSA.',
                'why' => 'El PDF contiene la flag de esta misión y un pequeño reto criptográfico (los números n, e y c) que necesitarás en la Misión 5.',
                'parts' => [
                    ['pdftotext aero_package_v3.pdf -', 'extrae el texto del PDF y lo manda a la salida estándar (-).'],
                    ['|', 'tubería hacia el filtro.'],
                    ['grep -Ei "RASCASSE|n *=|e *=|c *=|tar"', 'muestra solo las líneas con la flag o los valores del reto RSA (-E regex, -i sin distinguir mayúsculas).'],
                ],
                'where_text' => 'En la terminal de Kali Linux.',
                'expect' => 'Una bandera RASCASSE{...} y las líneas del reto: n = 23707, e = 7, c = [5017, 2302, 12377].',
                'next' => 'Envía la flag abajo. Guarda los números n, e y c: abre la Misión 5.',
                'fail' => [
                    '«pdftotext: command not found»: instala poppler con sudo apt install -y poppler-utils.',
                    'Si prefieres, ábrelo con un visor: xdg-open aero_package_v3.pdf.',
                ],
            ],
        ],
        'hints' => [
            'La caja fuerte tiene dos capas: primero una simétrica (contenedor OpenSSL .enc con contraseña), luego una asimétrica (un PDF GPG que necesita una clave privada protegida).',
            'La clave del contenedor y la passphrase de la clave privada están en el correo del PCAP. Descifra con openssl enc -d ... y extrae el .tar.gz.',
            'Importa la clave privada con gpg --import (passphrase del portal de dcastro), descifra el PDF con gpg --decrypt y saca la flag y el reto RSA con pdftotext.',
        ],
    ],

    /* ================= MISIÓN 5 · Escalada de privilegios ================= */
    5 => [
        'intro' => 'El documento esconde un reto RSA, el cifrado asimétrico que usa dos claves ligadas matemáticamente. '
            .'Cuando los números son pequeños, se puede romper con matemáticas básicas. El resultado te dirá cómo tomar el '
            .'control total del servidor: la escalada de privilegios consiste en pasar de un usuario normal a administrador '
            .'(root) aprovechando una mala configuración.',
        'steps' => [
            [
                'title' => 'Resolver el reto RSA',
                'cmd' => "python3 -c \"\nn,d=23707,3343\nc=[5017,2302,12377]\nprint(''.join(chr(pow(x,d,n)) for x in c))\n\"",
                'where' => 'kali',
                'what' => 'Descifra el mensaje RSA del PDF usando el módulo n y la clave privada d (que ya venían en el reto).',
                'why' => 'RSA es seguro solo si las claves son enormes. Aquí los números son minúsculos y d está dada, así que descifrar es aplicar una fórmula. El mensaje revela el binario con el que escalarás.',
                'parts' => [
                    ['python3 -c "..."', 'ejecuta el bloque de Python entre comillas.'],
                    ['n,d=23707,3343', 'el módulo y la clave privada del reto (de la Misión 4).'],
                    ['c=[5017,2302,12377]', 'los números cifrados del reto.'],
                    ["chr(pow(x,d,n))", 'descifra cada número (x^d mod n) y lo convierte en su carácter.'],
                ],
                'where_text' => 'En la terminal de Kali Linux (o cualquier equipo con Python 3). No necesita estar dentro del servidor.',
                'expect' => 'Imprime una palabra corta: el nombre de un binario de Linux. Ese programa es tu vía de escalada.',
                'next' => 'Vuelve a tu sesión SSH en el servidor y comprueba tus permisos sudo (siguiente paso).',
                'fail' => [
                    'Si da error de sintaxis, copia el bloque completo con el botón.',
                    'Confirma que n, d y c coinciden con los del PDF de la Misión 4.',
                ],
                'meaning' => 'Lección de RSA: la seguridad depende del tamaño de las claves. Con números pequeños, es trivial de romper.',
            ],
            [
                'title' => 'Volver al servidor y ver tus permisos sudo',
                'cmd' => "ssh pmendez@192.168.56.103\nsudo -l",
                'where' => 'ssh',
                'what' => 'Entra de nuevo al servidor (si cerraste la sesión) y lista qué comandos puede ejecutar tu usuario con privilegios de administrador.',
                'why' => 'Una escalada suele aprovechar un permiso sudo mal configurado. Verás que puedes ejecutar como root, sin contraseña, justo el binario que reveló el reto RSA.',
                'parts' => [
                    ['ssh pmendez@192.168.56.103', 'reconéctate al servidor (la escalada se hace ahí, no en Kali).'],
                    ['sudo -l', 'lista (list) los privilegios sudo de tu usuario.'],
                ],
                'where_text' => 'La primera línea se escribe en Kali; a partir de ahí estás dentro del servidor. Ejecuta sudo -l ya dentro.',
                'expect' => 'Una línea como "(root) NOPASSWD: /usr/bin/tar": puedes ejecutar ese binario como root sin contraseña.',
                'next' => 'Abusa de ese permiso para obtener una shell de root (siguiente paso).',
                'fail' => [
                    'Si pide contraseña y no la tienes, confirma que estás con el usuario correcto (pmendez).',
                    'Si no aparece nada NOPASSWD, revisa que resolviste bien el reto RSA: el binario permitido es justo ese.',
                ],
            ],
            [
                'title' => 'Escalar a root abusando del permiso sudo',
                'cmd' => "sudo tar -cf /dev/null /dev/null --checkpoint=1 --checkpoint-action=exec=/bin/sh",
                'where' => 'ssh',
                'what' => 'Usa una función legítima de tar (ejecutar una acción en un "checkpoint") para lanzar una shell. Como tar corre como root vía sudo, esa shell también es de root.',
                'why' => 'Es la técnica de GTFOBins: si puedes ejecutar tar como root, su opción checkpoint-action te da un intérprete con privilegios de administrador.',
                'parts' => [
                    ['sudo tar', 'ejecuta tar como root (permitido por la mala configuración).'],
                    ['-cf /dev/null /dev/null', 'crea un archivo "a la nada": es solo la excusa para que tar trabaje.'],
                    ['--checkpoint=1', 'pide a tar un punto de control tras el primer registro.'],
                    ['--checkpoint-action=exec=/bin/sh', 'en ese punto de control, ejecuta una shell (/bin/sh).'],
                ],
                'where_text' => 'Dentro de la sesión SSH en el servidor.',
                'expect' => 'Un nuevo prompt de shell. Compruébalo con whoami: debe responder root.',
                'next' => 'Lee la prueba final del caso (siguiente paso).',
                'fail' => [
                    'Si whoami no dice root, confirma que usaste sudo y que el permiso NOPASSWD era sobre tar.',
                ],
                'meaning' => 'Un permiso sudo demasiado amplio sobre un binario común se convierte en control total. Por eso sudo debe concederse al mínimo imprescindible.',
            ],
            [
                'title' => 'Leer la prueba final',
                'cmd' => "whoami\ncat /root/proof.txt",
                'where' => 'ssh',
                'what' => 'Confirma que eres root y muestra el archivo que solo root puede leer: la prueba que cierra el caso.',
                'why' => 'Es la evidencia final de quién tomó el control del servidor. Contiene la última flag.',
                'parts' => [
                    ['whoami', 'muestra el usuario actual (debe decir root).'],
                    ['cat /root/proof.txt', 'muestra el contenido del archivo, accesible solo como administrador.'],
                ],
                'where_text' => 'En la shell de root que acabas de obtener (dentro del servidor).',
                'expect' => 'whoami responde root y aparece la flag final RASCASSE{...} con el nombre del responsable.',
                'next' => 'Envía la flag abajo. Con las cinco flags validadas cerrarás la Operación RASCASSE y podrás descargar tu certificado.',
                'fail' => [
                    '«Permission denied»: no eres root; vuelve al paso anterior y relanza la shell con la técnica de tar.',
                ],
                'meaning' => 'Caso cerrado: has demostrado cómo entró el responsable, qué se llevó, quién lo firmó y cómo tomó el control del servidor.',
            ],
        ],
        'hints' => [
            'El PDF esconde un reto RSA con números muy pequeños. Un cifrado así se rompe con matemáticas básicas (y la clave d ya viene dada).',
            'Descífralo con Python (m = c^d mod n): el resultado es el nombre de un binario de Linux.',
            'Ese binario lo puedes ejecutar como root sin contraseña (sudo -l). Usa la técnica de GTFOBins para tar, obtén una shell de root y lee /root/proof.txt.',
        ],
    ],
];
